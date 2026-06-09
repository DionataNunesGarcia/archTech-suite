<?php

declare(strict_types=1);

namespace Drupal\ia_compliance\EventSubscriber;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ComplianceEventSubscriber implements EventSubscriberInterface {

  private array $auditQueue = [];

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::TERMINATE => ['onTerminate', 100],
    ];
  }

  public function trackEntityChange(EntityInterface $entity, string $action): void {
    $type = $entity->getEntityTypeId();
    // Skip compliance_record entities to prevent infinite loop.
    if ($type === 'node' && method_exists($entity, 'bundle') && $entity->bundle() === 'compliance_record') {
      return;
    }
    $this->auditQueue[] = [
      'entity' => $entity,
      'action' => $action,
    ];
  }

  public function onTerminate(TerminateEvent $event): void {
    foreach ($this->auditQueue as $item) {
      $this->createAuditRecord($item['entity'], $item['action']);
    }
    $this->auditQueue = [];
  }

  private function createAuditRecord(EntityInterface $entity, string $action): void {
    $entityType = $entity->getEntityTypeId();
    $entityId = (int) $entity->id();
    $userId = (int) \Drupal::currentUser()->id();

    $changes = json_encode([
      'entity_type' => $entityType,
      'entity_id' => $entityId,
      'action' => $action,
    ], JSON_THROW_ON_ERROR);

    $hash = hash('sha256', $changes . $userId . time());

    $record = $this->entityTypeManager()->getStorage('node')->create([
      'type' => 'compliance_record',
      'title' => "compliance_record {$entityType}/{$entityId}/{$action}",
      'field_compliance_entity_type' => $entityType,
      'field_compliance_entity_id' => $entityId,
      'field_compliance_action' => $action,
      'field_compliance_changes_json' => [
        'value' => $changes,
        'format' => 'plain_text',
      ],
      'field_compliance_user' => $userId,
      'field_compliance_timestamp' => time(),
      'field_compliance_hash' => $hash,
    ]);

    $record->save();

    \Drupal::logger('ia_compliance')->notice('Compliance record created: @hash', [
      '@hash' => $hash,
    ]);
  }
}
