<?php

declare(strict_types=1);

namespace Drupal\ia_diary\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class DiaryController extends ControllerBase {

  public function collection(Request $request): JsonResponse {
    $storage = $this->entityTypeManager()->getStorage('node');
    $query = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'diary_entry')
      ->sort('created', 'DESC')
      ->range(0, 50);

    if ($project_id = $request->query->get('project_id')) {
      $query->condition('field_diary_project', (int) $project_id);
    }

    $nids = $query->execute();
    $entries = [];

    foreach ($storage->loadMultiple($nids) as $node) {
      assert($node instanceof NodeInterface);
      $entries[] = $this->serializeEntry($node);
    }

    return new JsonResponse(['data' => $entries]);
  }

  public function createEntry(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (empty($data['project_id'])) {
      return new JsonResponse(['error' => 'project_id is required'], 422);
    }

    $node = $this->entityTypeManager()->getStorage('node')->create([
      'type' => 'diary_entry',
      'title' => $data['title'] ?? 'Diary Entry ' . date('Y-m-d'),
      'field_diary_project' => $data['project_id'],
      'field_diary_date' => $data['date'] ?? date('Y-m-d'),
      'field_diary_weather' => $data['weather'] ?? '',
      'field_diary_activities' => [
        'value' => $data['activities'] ?? '',
        'format' => 'plain_text',
      ],
      'field_diary_equipment' => [
        'value' => $data['equipment'] ?? '',
        'format' => 'plain_text',
      ],
      'field_diary_workers_count' => $data['workers_count'] ?? 0,
      'field_diary_notes' => [
        'value' => $data['notes'] ?? '',
        'format' => 'plain_text',
      ],
    ]);

    $node->save();

    return new JsonResponse([
      'message' => 'Diary entry created',
      'id' => $node->id(),
    ], 201);
  }

  public function byProject(int $project_id): JsonResponse {
    $storage = $this->entityTypeManager()->getStorage('node');
    $nids = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'diary_entry')
      ->condition('field_diary_project', $project_id)
      ->sort('field_diary_date', 'DESC')
      ->execute();

    $entries = [];
    foreach ($storage->loadMultiple($nids) as $node) {
      assert($node instanceof NodeInterface);
      $entries[] = $this->serializeEntry($node);
    }

    return new JsonResponse(['data' => $entries]);
  }

  private function serializeEntry(NodeInterface $node): array {
    return [
      'id' => (int) $node->id(),
      'title' => $node->getTitle(),
      'project_id' => $node->get('field_diary_project')->target_id ? (int) $node->get('field_diary_project')->target_id : null,
      'date' => $node->get('field_diary_date')->value ?? '',
      'weather' => $node->get('field_diary_weather')->value ?? '',
      'activities' => $node->get('field_diary_activities')->value ?? '',
      'equipment' => $node->get('field_diary_equipment')->value ?? '',
      'workers_count' => (int) ($node->get('field_diary_workers_count')->value ?? 0),
      'notes' => $node->get('field_diary_notes')->value ?? '',
      'created' => $node->getCreatedTime(),
    ];
  }
}
