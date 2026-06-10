<?php

declare(strict_types=1);

namespace Drupal\archtech_events\EventBus;

use Drupal\Core\Config\ConfigFactoryInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Publishes domain events to RabbitMQ exchanges.
 *
 * Uses php-amqplib for AMQP 0-9-1 communication.
 * Connection details are read from Drupal configuration.
 */
final class RabbitMqPublisher {

  private ?AMQPStreamConnection $connection = NULL;
  private bool $available = TRUE;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Publishes an event to the configured exchange.
   *
   * @param string $eventType
   *   The routing key / event type.
   * @param array $eventData
   *   The event payload.
   * @param string|null $exchange
   *   Exchange name (defaults to configured default).
   * @param string $contentType
   *   Message content type.
   *
   * @return bool
   *   TRUE if published successfully.
   */
  public function publish(
    string $eventType,
    array $eventData,
    ?string $exchange = NULL,
    string $contentType = 'application/json',
  ): bool {
    if (!$this->available) {
      return FALSE;
    }

    try {
      $connection = $this->getConnection();
      $channel = $connection->channel();

      $exchange ??= $this->getConfig('exchange', 'archtech.events');
      $channel->exchange_declare($exchange, 'topic', FALSE, TRUE, FALSE);

      $message = new AMQPMessage(
        \json_encode($eventData, JSON_THROW_ON_ERROR),
        [
          'content_type' => $contentType,
          'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
          'timestamp' => \time(),
        ],
      );

      $channel->basic_publish($message, $exchange, $eventType);
      $channel->close();

      return TRUE;
    } catch (\Throwable $e) {
      \Drupal::logger('archtech_events')->error(
        'Failed to publish event @type: @error',
        ['@type' => $eventType, '@error' => $e->getMessage()],
      );
      return FALSE;
    }
  }

  /**
   * Publishes an event with explicit correlation and causation IDs.
   */
  public function publishWithContext(
    string $eventType,
    array $eventData,
    string $correlationId,
    ?string $causationId = NULL,
  ): bool {
    $headers = [
      'correlation_id' => $correlationId,
    ];

    if ($causationId !== NULL) {
      $headers['causation_id'] = $causationId;
    }

    $eventData['_metadata'] = [
      'correlation_id' => $correlationId,
      'causation_id' => $causationId,
      'timestamp' => \gmdate('c'),
    ];

    return $this->publish($eventType, $eventData);
  }

  /**
   * Disconnects from RabbitMQ.
   */
  public function disconnect(): void {
    if ($this->connection !== NULL) {
      try {
        $this->connection->close();
      } catch (\Throwable) {
        // Connection already closed.
      }
      $this->connection = NULL;
    }
  }

  private function getConnection(): AMQPStreamConnection {
    if ($this->connection !== NULL && $this->connection->isConnected()) {
      return $this->connection;
    }

    $host = $this->getConfig('host', 'rabbitmq');
    $port = (int) $this->getConfig('port', '5672');
    $user = $this->getConfig('user', 'guest');
    $password = $this->getConfig('password', 'guest');
    $vhost = $this->getConfig('vhost', '/');

    $this->connection = new AMQPStreamConnection(
      $host, $port, $user, $password, $vhost,
      heartbeat: 30,
      connection_timeout: 5.0,
    );

    return $this->connection;
  }

  private function getConfig(string $key, string $default): string {
    return $this->configFactory
      ->get('archtech_events.settings')
      ->get('rabbitmq.' . $key) ?? $default;
  }

}
