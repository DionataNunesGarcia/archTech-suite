<?php

declare(strict_types=1);

namespace Drupal\archtech_events\EventBus;

use Drupal\Core\Database\Connection;

/**
 * Append-only event store for domain event replay and audit.
 *
 * Records every published event with its full payload and metadata.
 * Events are immutable once written.
 */
final class EventStore {

  public function __construct(
    private Connection $database,
  ) {}

  /**
   * Records an event in the event store.
   *
   * @param string $eventId
   *   UUID for the event.
   * @param string $eventType
   *   Dot-separated event type (e.g. 'crm.lead.created').
   * @param array $eventData
   *   Full event payload.
   * @param string|null $aggregateId
   *   Aggregate root ID.
   * @param array|null $metadata
   *   Additional metadata (correlation_id, causation_id, etc.).
   */
  public function record(
    string $eventId,
    string $eventType,
    array $eventData,
    ?string $aggregateId = NULL,
    ?array $metadata = NULL,
  ): void {
    $now = \gmdate('Y-m-d\TH:i:s.v\Z');

    $this->database->insert('archtech_event_store')
      ->fields([
        'event_id' => $eventId,
        'event_type' => $eventType,
        'aggregate_id' => $aggregateId,
        'event_data' => \json_encode($eventData, JSON_THROW_ON_ERROR),
        'metadata' => $metadata !== NULL ? \json_encode($metadata, JSON_THROW_ON_ERROR) : NULL,
        'occurred_at' => $now,
        'recorded_at' => $now,
      ])
      ->execute();
  }

  /**
   * Replays events for a given aggregate from the store.
   *
   * @return array<int, object>
   */
  public function loadForAggregate(string $aggregateId, ?string $fromTimestamp = NULL): array {
    $query = $this->database->select('archtech_event_store', 'e')
      ->fields('e')
      ->condition('e.aggregate_id', $aggregateId)
      ->orderBy('e.occurred_at', 'ASC');

    if ($fromTimestamp !== NULL) {
      $query->condition('e.occurred_at', $fromTimestamp, '>=');
    }

    return $query->execute()->fetchAll();
  }

  /**
   * Replays events of a given type within a time window.
   *
   * @return array<int, object>
   */
  public function loadByType(string $eventType, ?string $from = NULL, ?string $to = NULL, int $limit = 100): array {
    $query = $this->database->select('archtech_event_store', 'e')
      ->fields('e')
      ->condition('e.event_type', $eventType)
      ->orderBy('e.occurred_at', 'ASC')
      ->range(0, $limit);

    if ($from !== NULL) {
      $query->condition('e.occurred_at', $from, '>=');
    }
    if ($to !== NULL) {
      $query->condition('e.occurred_at', $to, '<=');
    }

    return $query->execute()->fetchAll();
  }

  /**
   * Returns the total count of stored events.
   */
  public function count(): int {
    return (int) $this->database->select('archtech_event_store', 'e')
      ->countQuery()
      ->execute()
      ->fetchField();
  }

}
