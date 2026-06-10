<?php

declare(strict_types=1);

namespace Drupal\ia_financeiro_avancado\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class FinanceController extends ControllerBase {

  public function reimbursements(Request $request): JsonResponse {
    $storage = $this->entityTypeManager()->getStorage('node');
    $query = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'reimbursement')
      ->sort('created', 'DESC')
      ->range(0, 50);

    if ($status = $request->query->get('status')) {
      $query->condition('field_reimb_status', $status);
    }
    if ($employee = $request->query->get('employee_id')) {
      $query->condition('field_reimb_employee', (int) $employee);
    }

    $nids = $query->execute();
    $items = [];
    foreach ($storage->loadMultiple($nids) as $node) {
      assert($node instanceof NodeInterface);
      $items[] = $this->serializeReimbursement($node);
    }

    return new JsonResponse(['data' => $items]);
  }

  public function createReimb(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (empty($data['employee_id']) || empty($data['amount'])) {
      return new JsonResponse(['error' => 'employee_id and amount are required'], 422);
    }

    $node = $this->entityTypeManager()->getStorage('node')->create([
      'type' => 'reimbursement',
      'title' => $data['title'] ?? 'Reimbursement ' . date('Y-m-d'),
      'field_reimb_employee' => $data['employee_id'],
      'field_reimb_project' => $data['project_id'] ?? null,
      'field_reimb_amount' => $data['amount'],
      'field_reimb_category' => $data['category'] ?? 'general',
      'field_reimb_status' => 'pending',
    ]);

    $node->save();

    return new JsonResponse([
      'message' => 'Reimbursement created',
      'id' => $node->id(),
    ], 201);
  }

  public function cashflowByProject(int $id, Request $request): JsonResponse {
    $storage = $this->entityTypeManager()->getStorage('node');
    $query = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'cashflow_entry')
      ->condition('field_cashflow_project', $id)
      ->sort('field_cashflow_date', 'DESC');

    $nids = $query->execute();
    $entries = [];
    foreach ($storage->loadMultiple($nids) as $node) {
      assert($node instanceof NodeInterface);
      $entries[] = $this->serializeCashflow($node);
    }

    return new JsonResponse(['data' => $entries]);
  }

  private function serializeReimbursement(NodeInterface $node): array {
    return [
      'id' => (int) $node->id(),
      'title' => $node->getTitle(),
      'employee_id' => $node->get('field_reimb_employee')->target_id ? (int) $node->get('field_reimb_employee')->target_id : null,
      'project_id' => $node->get('field_reimb_project')->target_id ? (int) $node->get('field_reimb_project')->target_id : null,
      'amount' => (float) ($node->get('field_reimb_amount')->value ?? 0),
      'category' => $node->get('field_reimb_category')->value ?? '',
      'status' => $node->get('field_reimb_status')->value ?? '',
      'created' => $node->getCreatedTime(),
    ];
  }

  private function serializeCashflow(NodeInterface $node): array {
    return [
      'id' => (int) $node->id(),
      'title' => $node->getTitle(),
      'project_id' => $node->get('field_cashflow_project')->target_id ? (int) $node->get('field_cashflow_project')->target_id : null,
      'type' => $node->get('field_cashflow_type')->value ?? '',
      'amount' => (float) ($node->get('field_cashflow_amount')->value ?? 0),
      'date' => $node->get('field_cashflow_date')->value ?? '',
      'description' => $node->get('field_cashflow_description')->value ?? '',
    ];
  }
}
