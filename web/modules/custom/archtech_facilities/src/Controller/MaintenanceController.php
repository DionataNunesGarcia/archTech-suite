<?php

declare(strict_types=1);

namespace Drupal\archtech_facilities\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class MaintenanceController extends ControllerBase {

  public function collection(Request $request): JsonResponse {
    if ($request->isMethod('GET')) {
      return $this->listMaintenance($request);
    }
    if ($request->isMethod('POST')) {
      return $this->createMaintenance($request);
    }
    throw new BadRequestHttpException('Method not allowed');
  }

  private function listMaintenance(Request $request): JsonResponse {
    $query = $this->entityTypeManager()->getStorage('node')->getQuery()
      ->condition('type', 'maintenance_schedule')
      ->condition('status', 1)
      ->accessCheck(TRUE)
      ->sort('field_maintenance_scheduled_date', 'ASC');

    if ($status = $request->query->get('status')) {
      $query->condition('field_maintenance_status', $status);
    }
    if ($facility = $request->query->get('facility')) {
      $query->condition('field_maintenance_facility', '%' . $facility . '%', 'LIKE');
    }

    $limit = min((int) $request->query->get('limit', '50'), 100);
    $offset = (int) $request->query->get('offset', '0');
    $query->range($offset, $limit);

    $nids = $query->execute();
    $schedules = [];

    if ($nids) {
      $nodes = $this->entityTypeManager()->getStorage('node')->loadMultiple($nids);
      foreach ($nodes as $node) {
        $schedules[] = $this->serializeMaintenance($node);
      }
    }

    return new JsonResponse([
      'data' => $schedules,
      'total' => count($schedules),
      'offset' => $offset,
      'limit' => $limit,
    ]);
  }

  private function createMaintenance(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (empty($data['title']) || empty($data['facility']) || empty($data['task']) || empty($data['scheduled_date'])) {
      throw new BadRequestHttpException('Title, facility, task, and scheduled_date are required');
    }

    $node = Node::create([
      'type' => 'maintenance_schedule',
      'title' => $data['title'],
      'field_maintenance_facility' => $data['facility'],
      'field_maintenance_task' => $data['task'],
      'field_maintenance_scheduled_date' => $data['scheduled_date'],
      'field_maintenance_status' => $data['status'] ?? 'scheduled',
      'field_maintenance_assigned_to' => $data['assigned_to'] ?? NULL,
      'field_maintenance_notes' => ['value' => $data['notes'] ?? '', 'format' => 'plain_text'],
    ]);

    $node->save();

    return new JsonResponse(
      $this->serializeMaintenance($node),
      JsonResponse::HTTP_CREATED,
    );
  }

  private function serializeMaintenance(Node $node): array {
    return [
      'id' => $node->id(),
      'uuid' => $node->uuid(),
      'title' => $node->label(),
      'facility' => $node->get('field_maintenance_facility')->value,
      'task' => $node->get('field_maintenance_task')->value,
      'scheduled_date' => $node->get('field_maintenance_scheduled_date')->value,
      'status' => $node->get('field_maintenance_status')->value,
      'assigned_to' => $node->get('field_maintenance_assigned_to')->target_id,
      'notes' => $node->get('field_maintenance_notes')->value,
      'created' => $node->getCreatedTime(),
      'changed' => $node->getChangedTime(),
    ];
  }

}
