<?php

declare(strict_types=1);

namespace Drupal\archtech_events\EventBus;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Database\Connection;

/**
 * Transactional outbox service.
 *
 * Writes events to the outbox table within the same database transaction
 * as the business operation. A background Drush command polls and publishes
 * them to RabbitMQ, ensuring at-least-once delivery.
 */
final class OutboxService {

  private const string STATUS_PENDING = 'pending';
  private const string STATUS_PROCESSING = 'processing';
  private const string STATUS_PROCESSED = 'processed';
  private const string STATUS_FAILED = 'failed';
  private const string STATUS_DLQ = 'dlq';

  public function __construct(
    private Connection $database,
    private readonly RabbitMqPublisher $publisher,
    private readonly UuidInterface $uuid,
    private readonly TimeInterface $time,
  ) {}

  /**
   * Writes an event to the outbox within the current transaction.
   *
   * Call this during a business operation so the event write is atomic
   * with the data mutation.
   */
  public function enqueue(
    string $eventType,
    array $eventData,
    ?string $aggregateId = NULL,
    int $maxRetries = 3,
  ): void {
    $this->database->insert('archtech_outbox')
      ->fields([
        'event_id' => $this->uuid->generate(),
        'event_type' => $eventType,
        'event_data' => \json_encode($eventData, JSON_THROW_ON_ERROR),
        'aggregate_id' => $aggregateId,
        'status' => self::STATUS_PENDING,
        'max_retries' => $maxRetries,
        'retry_count' => 0,
        'created_at' => \gmdate('Y-m-d\TH:i:s.v\Z'),
      ])
      ->execute();
  }

  /**
   * Processes pending outbox entries and publishes them to RabbitMQ.
   *
   * Designed to be called from a Drush command or cron.
   *
   * @param int $batchSize
   *   Max number of messages to process in one call.
   *
   * @return array{processed: int, failed: int}
   */
  public function processBatch(int $batchSize = 50): array {
    $processed = 0;
    $failed = 0;

    $this->database->startTransaction();

    try {
      $rows = $this->database->select('archtech_outbox', 'o')
        ->fields('o')
        ->condition('o.status', self::STATUS_PENDING)
        ->orderBy('o.created_at', 'ASC')
        ->range(0, $batchSize)
        ->forUpdate()
        ->execute()
        ->fetchAllAssoc('id');

      foreach ($rows as $id => $row) {
        $this->markProcessing((int) $id);

        $eventData = \json_decode($row->event_data, TRUE, 512, JSON_THROW_ON_ERROR);
        $published = $this->publisher->publish($row->event_type, $eventData);

        if ($published) {
          $this->markProcessed((int) $id);
          $processed++;
        } else {
          $this->handleFailure($row);
          $failed++;
        }
      }
    } catch (\Throwable) {
      // Transaction will roll back if an unexpected error occurs.
    }

    return ['processed' => $processed, 'failed' => $failed];
  }

  /**
   * Moves entries that have exceeded max retries to the DLQ.
   *
   * @return int Number of entries moved to DLQ.
   */
  public function moveToDlq(int $batchSize = 100): int {
    $count = 0;

    $rows = $this->database->select('archtech_outbox', 'o')
      ->fields('o', ['id', 'retry_count', 'max_retries'])
      ->condition('o.status', self::STATUS_FAILED)
      ->orderBy('o.created_at', 'ASC')
      ->range(0, $batchSize)
      ->execute()
      ->fetchAll();

    foreach ($rows as $row) {
      if ((int) $row->retry_count >= (int) $row->max_retries) {
        $this->database->update('archtech_outbox')
          ->fields(['status' => self::STATUS_DLQ])
          ->condition('id', (int) $row->id)
          ->execute();
        $count++;
      }
    }

    return $count;
  }

  /**
   * Retries failed entries with backoff delay.
   */
  public function retryFailed(int $batchSize = 50): int {
    $count = 0;

    $rows = $this->database->select('archtech_outbox', 'o')
      ->fields('o')
      ->condition('o.status', self::STATUS_FAILED)
      ->condition('o.retry_count', 'o.max_retries', '<')
      ->orderBy('o.next_retry_at', 'ASC')
      ->range(0, $batchSize)
      ->execute()
      ->fetchAllAssoc('id');

    foreach ($rows as $id => $row) {
      if ($row->next_retry_at !== NULL && $row->next_retry_at > \gmdate('Y-m-d\TH:i:s.v\Z')) {
        continue;
      }

      $this->markProcessing((int) $id);

      $eventData = \json_decode($row->event_data, TRUE, 512, JSON_THROW_ON_ERROR);
      $published = $this->publisher->publish($row->event_type, $eventData);

      if ($published) {
        $this->markProcessed((int) $id);
      } else {
        $this->handleFailure($row);
      }
      $count++;
    }

    return $count;
  }

  private function markProcessing(int $id): void {
    $this->database->update('archtech_outbox')
      ->fields(['status' => self::STATUS_PROCESSING])
      ->condition('id', $id)
      ->execute();
  }

  private function markProcessed(int $id): void {
    $this->database->update('archtech_outbox')
      ->fields([
        'status' => self::STATUS_PROCESSED,
        'processed_at' => \gmdate('Y-m-d\TH:i:s.v\Z'),
      ])
      ->condition('id', $id)
      ->execute();
  }

  private function handleFailure(object $row): void {
    $retryCount = (int) $row->retry_count + 1;
    $backoffSeconds = 2 ** $retryCount;
    $nextRetryAt = \gmdate('Y-m-d\TH:i:s.v\Z', $this->time->getCurrentTime() + $backoffSeconds);

    $this->database->update('archtech_outbox')
      ->fields([
        'status' => self::STATUS_FAILED,
        'retry_count' => $retryCount,
        'last_error' => 'Failed to publish to RabbitMQ.',
        'next_retry_at' => $nextRetryAt,
      ])
      ->condition('id', (int) $row->id)
      ->execute();
  }

}
