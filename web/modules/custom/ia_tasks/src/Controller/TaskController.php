<?php

declare(strict_types=1);

namespace Drupal\ia_tasks\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class TaskController extends ControllerBase {

  public function collection(Request $request): JsonResponse {
    $storage = $this->entityTypeManager()->getStorage('node');
    $query = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'task')
      ->sort('created', 'DESC')
      ->range(0, 100);

    if ($project_id = $request->query->get('project_id')) {
      $query->condition('field_task_project', (int) $project_id);
    }
    if ($status = $request->query->get('status')) {
      $query->condition('field_task_status', $status);
    }
    if ($priority = $request->query->get('priority')) {
      $query->condition('field_task_priority', $priority);
    }
    if ($assigned = $request->query->get('assigned_to')) {
      $query->condition('field_task_assigned_to', (int) $assigned);
    }

    $nids = $query->execute();
    $tasks = [];
    foreach ($storage->loadMultiple($nids) as $node) {
      assert($node instanceof NodeInterface);
      $tasks[] = $this->serializeTask($node);
    }

    return new JsonResponse(['data' => $tasks]);
  }

  public function createTask(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (empty($data['title'])) {
      return new JsonResponse(['error' => 'title is required'], 422);
    }

    $node = $this->entityTypeManager()->getStorage('node')->create([
      'type' => 'task',
      'title' => $data['title'],
      'field_task_title' => $data['title'],
      'field_task_description' => [
        'value' => $data['description'] ?? '',
        'format' => 'plain_text',
      ],
      'field_task_project' => $data['project_id'] ?? null,
      'field_task_assigned_to' => $data['assigned_to'] ?? [],
      'field_task_priority' => $data['priority'] ?? 'medium',
      'field_task_status' => $data['status'] ?? 'backlog',
      'field_task_due_date' => $data['due_date'] ?? null,
    ]);

    $node->save();

    return new JsonResponse([
      'message' => 'Task created',
      'id' => $node->id(),
    ], 201);
  }

  public function updateStatus(int $id, Request $request): JsonResponse {
    $node = $this->entityTypeManager()->getStorage('node')->load($id);
    if (!$node || $node->bundle() !== 'task') {
      return new JsonResponse(['error' => 'Task not found'], 404);
    }

    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);
    $validStatuses = ['backlog', 'todo', 'in_progress', 'review', 'done'];

    if (empty($data['status']) || !in_array($data['status'], $validStatuses, TRUE)) {
      return new JsonResponse([
        'error' => 'Valid status required: ' . implode(', ', $validStatuses),
      ], 422);
    }

    $node->set('field_task_status', $data['status']);
    $node->save();

    return new JsonResponse([
      'message' => 'Task status updated',
      'id' => $node->id(),
      'status' => $data['status'],
    ]);
  }

  public function kanban(Request $request): JsonResponse {
    $storage = $this->entityTypeManager()->getStorage('node');
    $statuses = ['backlog', 'todo', 'in_progress', 'review', 'done'];
    $board = [];

    foreach ($statuses as $status) {
      $query = $storage->getQuery()
        ->accessCheck(TRUE)
        ->condition('type', 'task')
        ->condition('field_task_status', $status)
        ->sort('field_task_priority', 'ASC')
        ->sort('created', 'ASC')
        ->range(0, 50);

      if ($project_id = $request->query->get('project_id')) {
        $query->condition('field_task_project', (int) $project_id);
      }

      $nids = $query->execute();
      $tasks = [];
      foreach ($storage->loadMultiple($nids) as $node) {
        assert($node instanceof NodeInterface);
        $tasks[] = $this->serializeTask($node);
      }
      $board[$status] = $tasks;
    }

    return new JsonResponse(['data' => $board]);
  }

  private function serializeTask(NodeInterface $node): array {
    $assigned = [];
    foreach ($node->get('field_task_assigned_to')->referencedEntities() as $user) {
      $assigned[] = (int) $user->id();
    }

    return [
      'id' => (int) $node->id(),
      'title' => $node->getTitle(),
      'description' => $node->get('field_task_description')->value ?? '',
      'project_id' => $node->get('field_task_project')->target_id ? (int) $node->get('field_task_project')->target_id : null,
      'assigned_to' => $assigned,
      'priority' => $node->get('field_task_priority')->value ?? '',
      'status' => $node->get('field_task_status')->value ?? '',
      'due_date' => $node->get('field_task_due_date')->value ?? '',
      'created' => $node->getCreatedTime(),
    ];
  }
}
