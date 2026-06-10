<?php

declare(strict_types=1);

namespace Drupal\archtech_proposals\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class ProposalController extends ControllerBase {

  public function collection(Request $request): JsonResponse {
    if ($request->isMethod('GET')) {
      return $this->listProposals($request);
    }
    if ($request->isMethod('POST')) {
      return $this->createProposal($request);
    }
    throw new BadRequestHttpException('Method not allowed');
  }

  private function listProposals(Request $request): JsonResponse {
    $query = $this->entityTypeManager()->getStorage('node')->getQuery()
      ->condition('type', 'proposal')
      ->condition('status', 1)
      ->accessCheck(TRUE)
      ->sort('created', 'DESC');

    if ($status = $request->query->get('status')) {
      $query->condition('field_proposal_status', $status);
    }
    if ($client = $request->query->get('client')) {
      $query->condition('field_proposal_client', (int) $client);
    }

    $limit = min((int) $request->query->get('limit', '50'), 100);
    $offset = (int) $request->query->get('offset', '0');
    $query->range($offset, $limit);

    $nids = $query->execute();
    $proposals = [];

    if ($nids) {
      $nodes = $this->entityTypeManager()->getStorage('node')->loadMultiple($nids);
      foreach ($nodes as $node) {
        $proposals[] = $this->serializeProposal($node);
      }
    }

    return new JsonResponse([
      'data' => $proposals,
      'total' => count($proposals),
      'offset' => $offset,
      'limit' => $limit,
    ]);
  }

  private function createProposal(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (empty($data['title']) || empty($data['client'])) {
      throw new BadRequestHttpException('Title and client are required');
    }

    $node = Node::create([
      'type' => 'proposal',
      'title' => $data['title'],
      'field_proposal_client' => $data['client'],
      'field_proposal_description' => ['value' => $data['description'] ?? '', 'format' => 'plain_text'],
      'field_proposal_budget' => $data['budget'] ?? NULL,
      'field_proposal_status' => $data['status'] ?? 'draft',
      'field_proposal_valid_until' => $data['valid_until'] ?? NULL,
    ]);

    $node->save();

    return new JsonResponse(
      $this->serializeProposal($node),
      JsonResponse::HTTP_CREATED,
    );
  }

  public function updateStatus(Node $node, Request $request): JsonResponse {
    if ($node->bundle() !== 'proposal') {
      throw new BadRequestHttpException('Not a proposal entity');
    }

    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (empty($data['status'])) {
      throw new BadRequestHttpException('Status field is required');
    }

    $allowed = ['draft', 'sent', 'accepted', 'rejected'];
    if (!in_array($data['status'], $allowed, TRUE)) {
      throw new BadRequestHttpException(sprintf(
        'Invalid status. Allowed: %s',
        implode(', ', $allowed),
      ));
    }

    $oldStatus = $node->get('field_proposal_status')->value;
    $node->set('field_proposal_status', $data['status']);
    $node->save();

    return new JsonResponse([
      'id' => $node->id(),
      'old_status' => $oldStatus,
      'new_status' => $data['status'],
    ]);
  }

  private function serializeProposal(Node $node): array {
    return [
      'id' => $node->id(),
      'uuid' => $node->uuid(),
      'title' => $node->label(),
      'client' => $node->get('field_proposal_client')->target_id,
      'description' => $node->get('field_proposal_description')->value,
      'budget' => $node->get('field_proposal_budget')->value ? (float) $node->get('field_proposal_budget')->value : null,
      'status' => $node->get('field_proposal_status')->value,
      'valid_until' => $node->get('field_proposal_valid_until')->value,
      'created' => $node->getCreatedTime(),
      'changed' => $node->getChangedTime(),
    ];
  }

}
