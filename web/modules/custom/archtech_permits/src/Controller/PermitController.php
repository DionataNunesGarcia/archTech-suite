<?php

declare(strict_types=1);

namespace Drupal\archtech_permits\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class PermitController extends ControllerBase {

  public function collection(Request $request): JsonResponse {
    if ($request->isMethod('GET')) {
      return $this->listPermits($request);
    }
    if ($request->isMethod('POST')) {
      return $this->createPermit($request);
    }
    throw new BadRequestHttpException('Method not allowed');
  }

  private function listPermits(Request $request): JsonResponse {
    $query = $this->entityTypeManager()->getStorage('node')->getQuery()
      ->condition('type', 'permit_application')
      ->condition('status', 1)
      ->accessCheck(TRUE)
      ->sort('created', 'DESC');

    if ($status = $request->query->get('status')) {
      $query->condition('field_permit_status', $status);
    }
    if ($project = $request->query->get('project')) {
      $query->condition('field_permit_project', (int) $project);
    }
    if ($type = $request->query->get('type')) {
      $query->condition('field_permit_type', $type);
    }

    $limit = min((int) $request->query->get('limit', '50'), 100);
    $offset = (int) $request->query->get('offset', '0');
    $query->range($offset, $limit);

    $nids = $query->execute();
    $permits = [];

    if ($nids) {
      $nodes = $this->entityTypeManager()->getStorage('node')->loadMultiple($nids);
      foreach ($nodes as $node) {
        $permits[] = $this->serializePermit($node);
      }
    }

    return new JsonResponse([
      'data' => $permits,
      'total' => count($permits),
      'offset' => $offset,
      'limit' => $limit,
    ]);
  }

  private function createPermit(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (empty($data['title']) || empty($data['permit_type']) || empty($data['project'])) {
      throw new BadRequestHttpException('Title, permit_type, and project are required');
    }

    $node = Node::create([
      'type' => 'permit_application',
      'title' => $data['title'],
      'field_permit_project' => $data['project'],
      'field_permit_type' => $data['permit_type'],
      'field_permit_status' => $data['status'] ?? 'draft',
      'field_permit_submitted_date' => $data['submitted_date'] ?? NULL,
      'field_permit_approved_date' => $data['approved_date'] ?? NULL,
      'field_permit_notes' => ['value' => $data['notes'] ?? '', 'format' => 'plain_text'],
    ]);

    $node->save();

    return new JsonResponse(
      $this->serializePermit($node),
      JsonResponse::HTTP_CREATED,
    );
  }

  private function serializePermit(Node $node): array {
    $documents = [];
    foreach ($node->get('field_permit_documents') as $file) {
      if ($file->entity) {
        $documents[] = [
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
      'project' => $node->get('field_permit_project')->target_id,
      'permit_type' => $node->get('field_permit_type')->value,
      'status' => $node->get('field_permit_status')->value,
      'submitted_date' => $node->get('field_permit_submitted_date')->value,
      'approved_date' => $node->get('field_permit_approved_date')->value,
      'notes' => $node->get('field_permit_notes')->value,
      'documents' => $documents,
      'created' => $node->getCreatedTime(),
      'changed' => $node->getChangedTime(),
    ];
  }

}
