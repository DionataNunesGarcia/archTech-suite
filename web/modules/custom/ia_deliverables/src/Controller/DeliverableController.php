<?php

declare(strict_types=1);

namespace Drupal\ia_deliverables\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class DeliverableController extends ControllerBase {

  public function collection(Request $request): JsonResponse {
    $storage = $this->entityTypeManager()->getStorage('node');
    $query = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'deliverable')
      ->sort('created', 'DESC')
      ->range(0, 50);

    if ($project_id = $request->query->get('project_id')) {
      $query->condition('field_deliverable_project', (int) $project_id);
    }
    if ($status = $request->query->get('status')) {
      $query->condition('field_deliverable_status', $status);
    }

    $nids = $query->execute();
    $items = [];
    foreach ($storage->loadMultiple($nids) as $node) {
      assert($node instanceof NodeInterface);
      $items[] = $this->serialize($node);
    }

    return new JsonResponse(['data' => $items]);
  }

  public function createItem(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (empty($data['project_id']) || empty($data['title'])) {
      return new JsonResponse(['error' => 'project_id and title are required'], 422);
    }

    $node = $this->entityTypeManager()->getStorage('node')->create([
      'type' => 'deliverable',
      'title' => $data['title'],
      'field_deliverable_project' => $data['project_id'],
      'field_deliverable_phase' => $data['phase'] ?? '',
      'field_deliverable_title' => $data['title'],
      'field_deliverable_description' => [
        'value' => $data['description'] ?? '',
        'format' => 'plain_text',
      ],
      'field_deliverable_status' => $data['status'] ?? 'pending',
      'field_deliverable_due_date' => $data['due_date'] ?? null,
    ]);

    $node->save();

    return new JsonResponse([
      'message' => 'Deliverable created',
      'id' => $node->id(),
    ], 201);
  }

  private function serialize(NodeInterface $node): array {
    return [
      'id' => (int) $node->id(),
      'title' => $node->getTitle(),
      'project_id' => $node->get('field_deliverable_project')->target_id ? (int) $node->get('field_deliverable_project')->target_id : null,
      'phase' => $node->get('field_deliverable_phase')->value ?? '',
      'description' => $node->get('field_deliverable_description')->value ?? '',
      'status' => $node->get('field_deliverable_status')->value ?? '',
      'due_date' => $node->get('field_deliverable_due_date')->value ?? '',
      'created' => $node->getCreatedTime(),
    ];
  }
}
