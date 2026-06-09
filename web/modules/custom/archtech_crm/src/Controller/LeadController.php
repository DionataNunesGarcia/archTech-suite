<?php

declare(strict_types=1);

namespace Drupal\archtech_crm\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class LeadController extends ControllerBase {

  public function collection(Request $request): JsonResponse {
    if ($request->isMethod('GET')) {
      return $this->listLeads($request);
    }
    if ($request->isMethod('POST')) {
      return $this->createLead($request);
    }
    throw new BadRequestHttpException('Method not allowed');
  }

  private function listLeads(Request $request): JsonResponse {
    $query = $this->entityTypeManager()->getStorage('node')->getQuery()
      ->condition('type', 'lead')
      ->condition('status', 1)
      ->accessCheck(TRUE)
      ->sort('created', 'DESC');

    if ($status = $request->query->get('status')) {
      $query->condition('field_lead_status', $status);
    }

    $limit = min((int) $request->query->get('limit', '50'), 100);
    $offset = (int) $request->query->get('offset', '0');

    $query->range($offset, $limit);
    $nids = $query->execute();

    $leads = [];
    if ($nids) {
      $nodes = $this->entityTypeManager()->getStorage('node')->loadMultiple($nids);
      foreach ($nodes as $node) {
        $leads[] = $this->serializeLead($node);
      }
    }

    return new JsonResponse([
      'data' => $leads,
      'total' => count($leads),
      'offset' => $offset,
      'limit' => $limit,
    ]);
  }

  private function createLead(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (empty($data['name']) || empty($data['email'])) {
      throw new BadRequestHttpException('Name and email are required');
    }

    $node = Node::create([
      'type' => 'lead',
      'title' => $data['name'],
      'field_lead_name' => $data['name'],
      'field_lead_email' => $data['email'],
      'field_lead_phone' => $data['phone'] ?? '',
      'field_lead_company' => $data['company'] ?? '',
      'field_lead_score' => $data['score'] ?? 0,
      'field_lead_status' => $data['status'] ?? 'new',
      'field_lead_notes' => ['value' => $data['notes'] ?? '', 'format' => 'plain_text'],
      'field_lead_assigned_to' => $data['assigned_to'] ?? NULL,
      'uid' => $this->currentUser()->id(),
    ]);

    $node->save();

    return new JsonResponse(
      $this->serializeLead($node),
      JsonResponse::HTTP_CREATED,
    );
  }

  public function updateStatus(Node $node, Request $request): JsonResponse {
    if ($node->bundle() !== 'lead') {
      throw new BadRequestHttpException('Not a lead entity');
    }

    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (empty($data['status'])) {
      throw new BadRequestHttpException('Status field is required');
    }

    $allowed = ['new', 'qualified', 'contacted', 'converted', 'lost'];
    if (!in_array($data['status'], $allowed, TRUE)) {
      throw new BadRequestHttpException(sprintf(
        'Invalid status. Allowed: %s',
        implode(', ', $allowed),
      ));
    }

    $oldStatus = $node->get('field_lead_status')->value;
    $node->set('field_lead_status', $data['status']);
    $node->save();

    return new JsonResponse([
      'id' => $node->id(),
      'old_status' => $oldStatus,
      'new_status' => $data['status'],
    ]);
  }

  private function serializeLead(Node $node): array {
    return [
      'id' => $node->id(),
      'uuid' => $node->uuid(),
      'name' => $node->get('field_lead_name')->value,
      'email' => $node->get('field_lead_email')->value,
      'phone' => $node->get('field_lead_phone')->value,
      'company' => $node->get('field_lead_company')->value,
      'score' => (int) $node->get('field_lead_score')->value,
      'status' => $node->get('field_lead_status')->value,
      'notes' => $node->get('field_lead_notes')->value,
      'assigned_to' => $node->get('field_lead_assigned_to')->target_id,
      'created' => $node->getCreatedTime(),
      'changed' => $node->getChangedTime(),
    ];
  }

}
