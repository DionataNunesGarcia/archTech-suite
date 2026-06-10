<?php

declare(strict_types=1);

namespace Drupal\archtech_security\Service;

use Drupal\Core\Database\Connection;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Immutable audit logger.
 *
 * All admin actions and security events are recorded to the archtech_audit_log
 * table. Once written, records cannot be updated or deleted — ensuring an
 * immutable tamper-proof trail.
 */
final class AuditLogger {

  public function __construct(
    private Connection $database,
    private readonly RequestStack $requestStack,
  ) {}

  /**
   * Logs an action to the immutable audit log.
   *
   * @param int $userId
   * @param string $action
   *   Machine name of the action (e.g. 'user.login', 'node.delete').
   * @param string|null $entityType
   * @param string|int|null $entityId
   * @param array|null $details
   *   Arbitrary JSON-serializable context.
   * @param string|null $ipAddress
   * @param string|null $sessionId
   */
  public function log(
    int $userId,
    string $action,
    ?string $entityType = NULL,
    string|int|null $entityId = NULL,
    ?array $details = NULL,
    ?string $ipAddress = NULL,
    ?string $sessionId = NULL,
  ): void {
    $this->database->insert('archtech_audit_log')
      ->fields([
        'user_id' => $userId,
        'action' => $action,
        'entity_type' => $entityType,
        'entity_id' => $entityId !== NULL ? (string) $entityId : NULL,
        'details' => $details !== NULL ? \json_encode($details, JSON_THROW_ON_ERROR) : NULL,
        'ip_address' => $ipAddress ?? $this->getClientIp(),
        'session_id' => $sessionId ?? $this->getSessionId(),
        'created_at' => \gmdate('Y-m-d\TH:i:s.v\Z'),
      ])
      ->execute();
  }

  /**
   * Retrieves audit log entries with optional filtering.
   *
   * @return array<int, object>
   */
  public function query(
    ?int $userId = NULL,
    ?string $action = NULL,
    ?string $fromDate = NULL,
    ?string $toDate = NULL,
    int $limit = 50,
    int $offset = 0,
  ): array {
    $query = $this->database->select('archtech_audit_log', 'a')
      ->fields('a')
      ->orderBy('created_at', 'DESC')
      ->range($offset, $limit);

    if ($userId !== NULL) {
      $query->condition('a.user_id', $userId);
    }
    if ($action !== NULL) {
      $query->condition('a.action', $action);
    }
    if ($fromDate !== NULL) {
      $query->condition('a.created_at', $fromDate, '>=');
    }
    if ($toDate !== NULL) {
      $query->condition('a.created_at', $toDate, '<=');
    }

    return $query->execute()->fetchAll();
  }

  /**
   * Counts audit log entries matching filters.
   */
  public function count(?int $userId = NULL, ?string $action = NULL): int {
    $query = $this->database->select('archtech_audit_log', 'a');

    if ($userId !== NULL) {
      $query->condition('a.user_id', $userId);
    }
    if ($action !== NULL) {
      $query->condition('a.action', $action);
    }

    return (int) $query->countQuery()->execute()->fetchField();
  }

  private function getClientIp(): string {
    return $this->requestStack->getCurrentRequest()?->getClientIp() ?? '0.0.0.0';
  }

  private function getSessionId(): ?string {
    return $this->requestStack->getCurrentRequest()?->getSession()?->getId() ?? NULL;
  }

}
