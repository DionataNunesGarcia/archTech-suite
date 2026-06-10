<?php

declare(strict_types=1);

namespace Drupal\archtech_financeiro\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class InvoiceController extends ControllerBase {

  public function collection(Request $request): JsonResponse {
    if ($request->isMethod('GET')) {
      return $this->listInvoices($request);
    }
    if ($request->isMethod('POST')) {
      return $this->createInvoice($request);
    }
    throw new BadRequestHttpException('Method not allowed');
  }

  private function listInvoices(Request $request): JsonResponse {
    $query = $this->entityTypeManager()->getStorage('node')->getQuery()
      ->condition('type', 'invoice')
      ->condition('status', 1)
      ->accessCheck(TRUE)
      ->sort('created', 'DESC');

    if ($status = $request->query->get('status')) {
      $query->condition('field_invoice_status', $status);
    }
    if ($client = $request->query->get('client')) {
      $query->condition('field_invoice_client', (int) $client);
    }

    $limit = min((int) $request->query->get('limit', '50'), 100);
    $offset = (int) $request->query->get('offset', '0');
    $query->range($offset, $limit);

    $nids = $query->execute();
    $invoices = [];

    if ($nids) {
      $nodes = $this->entityTypeManager()->getStorage('node')->loadMultiple($nids);
      foreach ($nodes as $node) {
        $invoices[] = $this->serializeInvoice($node);
      }
    }

    return new JsonResponse([
      'data' => $invoices,
      'total' => count($invoices),
      'offset' => $offset,
      'limit' => $limit,
    ]);
  }

  private function createInvoice(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (empty($data['title']) || empty($data['amount'])) {
      throw new BadRequestHttpException('Title and amount are required');
    }

    $node = Node::create([
      'type' => 'invoice',
      'title' => $data['title'],
      'field_invoice_client' => $data['client'] ?? NULL,
      'field_invoice_project' => $data['project'] ?? NULL,
      'field_invoice_amount' => $data['amount'],
      'field_invoice_status' => $data['status'] ?? 'draft',
      'field_invoice_due_date' => $data['due_date'] ?? NULL,
      'field_invoice_paid_date' => $data['paid_date'] ?? NULL,
    ]);

    $node->save();

    return new JsonResponse(
      $this->serializeInvoice($node),
      JsonResponse::HTTP_CREATED,
    );
  }

  public function updateStatus(Node $node, Request $request): JsonResponse {
    if ($node->bundle() !== 'invoice') {
      throw new BadRequestHttpException('Not an invoice entity');
    }

    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (empty($data['status'])) {
      throw new BadRequestHttpException('Status field is required');
    }

    $allowed = ['draft', 'sent', 'paid', 'overdue', 'cancelled'];
    if (!in_array($data['status'], $allowed, TRUE)) {
      throw new BadRequestHttpException(sprintf(
        'Invalid status. Allowed: %s',
        implode(', ', $allowed),
      ));
    }

    $oldStatus = $node->get('field_invoice_status')->value;
    $node->set('field_invoice_status', $data['status']);

    if ($data['status'] === 'paid' && empty($node->get('field_invoice_paid_date')->value)) {
      $node->set('field_invoice_paid_date', date('Y-m-d'));
    }

    $node->save();

    return new JsonResponse([
      'id' => $node->id(),
      'old_status' => $oldStatus,
      'new_status' => $data['status'],
    ]);
  }

  private function serializeInvoice(Node $node): array {
    return [
      'id' => $node->id(),
      'uuid' => $node->uuid(),
      'title' => $node->label(),
      'client' => $node->get('field_invoice_client')->target_id,
      'project' => $node->get('field_invoice_project')->target_id,
      'amount' => $node->get('field_invoice_amount')->value ? (float) $node->get('field_invoice_amount')->value : null,
      'status' => $node->get('field_invoice_status')->value,
      'due_date' => $node->get('field_invoice_due_date')->value,
      'paid_date' => $node->get('field_invoice_paid_date')->value,
      'created' => $node->getCreatedTime(),
      'changed' => $node->getChangedTime(),
    ];
  }

}
