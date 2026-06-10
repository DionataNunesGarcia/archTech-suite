<?php

declare(strict_types=1);

namespace Drupal\ia_compliance\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class ComplianceController extends ControllerBase {

  public function audit(Request $request): JsonResponse {
    $storage = $this->entityTypeManager()->getStorage('node');
    $query = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'compliance_record')
      ->sort('created', 'DESC')
      ->range(0, 100);

    if ($entity_type = $request->query->get('entity_type')) {
      $query->condition('field_compliance_entity_type', $entity_type);
    }
    if ($entity_id = $request->query->get('entity_id')) {
      $query->condition('field_compliance_entity_id', (int) $entity_id);
    }
    if ($action = $request->query->get('action')) {
      $query->condition('field_compliance_action', $action);
    }
    if ($user_id = $request->query->get('user_id')) {
      $query->condition('field_compliance_user', (int) $user_id);
    }

    $nids = $query->execute();
    $records = [];
    foreach ($storage->loadMultiple($nids) as $node) {
      assert($node instanceof NodeInterface);
      $records[] = [
        'id' => (int) $node->id(),
        'entity_type' => $node->get('field_compliance_entity_type')->value ?? '',
        'entity_id' => (int) ($node->get('field_compliance_entity_id')->value ?? 0),
        'action' => $node->get('field_compliance_action')->value ?? '',
        'user_id' => $node->get('field_compliance_user')->target_id ? (int) $node->get('field_compliance_user')->target_id : null,
        'timestamp' => $node->get('field_compliance_timestamp')->value ?? '',
        'hash' => $node->get('field_compliance_hash')->value ?? '',
        'created' => $node->getCreatedTime(),
      ];
    }

    return new JsonResponse(['data' => $records]);
  }
}
