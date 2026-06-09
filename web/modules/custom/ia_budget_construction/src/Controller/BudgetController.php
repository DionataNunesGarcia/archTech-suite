<?php

declare(strict_types=1);

namespace Drupal\ia_budget_construction\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class BudgetController extends ControllerBase {

  public function byProject(int $id): JsonResponse {
    $storage = $this->entityTypeManager()->getStorage('node');
    $nids = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'budget')
      ->condition('field_budget_project', $id)
      ->sort('created', 'DESC')
      ->execute();

    $budgets = [];
    foreach ($storage->loadMultiple($nids) as $node) {
      assert($node instanceof NodeInterface);
      $budgets[] = $this->serializeBudget($node);
    }

    return new JsonResponse(['data' => $budgets]);
  }

  public function createBudget(int $id, Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    $node = $this->entityTypeManager()->getStorage('node')->create([
      'type' => 'budget',
      'title' => $data['title'] ?? 'Budget for Project ' . $id,
      'field_budget_project' => $id,
      'field_budget_total' => $data['total'] ?? 0,
      'field_budget_status' => 'draft',
    ]);

    if (!empty($data['items'])) {
      $paragraphs = [];
      foreach ($data['items'] as $item) {
        $p = $this->entityTypeManager()->getStorage('paragraph')->create([
          'type' => 'budget_item',
          'field_budget_item_name' => $item['name'] ?? '',
          'field_budget_item_quantity' => $item['quantity'] ?? 1,
          'field_budget_item_unit_price' => $item['unit_price'] ?? 0,
          'field_budget_item_total' => $item['total'] ?? 0,
        ]);
        $paragraphs[] = $p;
      }
      $node->set('field_budget_items', $paragraphs);
    }

    $node->save();

    return new JsonResponse([
      'message' => 'Budget created',
      'id' => $node->id(),
    ], 201);
  }

  public function createMeasurement(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (empty($data['project_id']) || empty($data['item'])) {
      return new JsonResponse(['error' => 'project_id and item are required'], 422);
    }

    $node = $this->entityTypeManager()->getStorage('node')->create([
      'type' => 'measurement',
      'title' => 'Measurement ' . ($data['item'] ?? '') . ' ' . date('Y-m-d'),
      'field_measurement_project' => $data['project_id'],
      'field_measurement_date' => $data['date'] ?? date('Y-m-d'),
      'field_measurement_item' => $data['item'],
      'field_measurement_quantity' => $data['quantity'] ?? 0,
      'field_measurement_unit' => $data['unit'] ?? 'm2',
      'field_measurement_unit_price' => $data['unit_price'] ?? 0,
      'field_measurement_total' => $data['total'] ?? ($data['quantity'] ?? 0) * ($data['unit_price'] ?? 0),
    ]);

    $node->save();

    return new JsonResponse([
      'message' => 'Measurement created',
      'id' => $node->id(),
    ], 201);
  }

  private function serializeBudget(NodeInterface $node): array {
    return [
      'id' => (int) $node->id(),
      'title' => $node->getTitle(),
      'project_id' => $node->get('field_budget_project')->target_id ? (int) $node->get('field_budget_project')->target_id : null,
      'total' => (float) ($node->get('field_budget_total')->value ?? 0),
      'status' => $node->get('field_budget_status')->value ?? '',
      'created' => $node->getCreatedTime(),
    ];
  }
}
