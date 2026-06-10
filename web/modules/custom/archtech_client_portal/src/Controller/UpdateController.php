<?php

declare(strict_types=1);

namespace Drupal\archtech_client_portal\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class UpdateController extends ControllerBase {

  public function listUpdates(Request $request): JsonResponse {
    $query = $this->entityTypeManager()->getStorage('node')->getQuery()
      ->condition('type', 'project_update')
      ->condition('status', 1)
      ->accessCheck(TRUE)
      ->sort('created', 'DESC');

    // Filter by visibility for non-admin users.
    if (!$this->currentUser()->hasPermission('administer nodes')) {
      $query->condition('field_update_visibility', ['public', 'client_only'], 'IN');
    }

    if ($projectId = $request->query->get('project')) {
      $query->condition('field_update_project', (int) $projectId);
    }

    $limit = min((int) $request->query->get('limit', '50'), 100);
    $offset = (int) $request->query->get('offset', '0');
    $query->range($offset, $limit);

    $nids = $query->execute();
    $updates = [];

    if ($nids) {
      $nodes = $this->entityTypeManager()->getStorage('node')->loadMultiple($nids);
      foreach ($nodes as $node) {
        $updates[] = $this->serializeUpdate($node);
      }
    }

    return new JsonResponse([
      'data' => $updates,
      'total' => count($updates),
      'offset' => $offset,
      'limit' => $limit,
    ]);
  }

  private function serializeUpdate($node): array {
    $attachments = [];
    foreach ($node->get('field_update_attachments') as $file) {
      if ($file->entity) {
        $attachments[] = [
          'fid' => $file->target_id,
          'filename' => $file->entity->getFilename(),
          'url' => $file->entity->createFileUrl(),
        ];
      }
    }

    return [
      'id' => $node->id(),
      'uuid' => $node->uuid(),
      'title' => $node->label(),
      'project_id' => $node->get('field_update_project')->target_id,
      'body' => $node->get('field_update_body')->value,
      'visibility' => $node->get('field_update_visibility')->value,
      'attachments' => $attachments,
      'created' => $node->getCreatedTime(),
      'changed' => $node->getChangedTime(),
    ];
  }

}
