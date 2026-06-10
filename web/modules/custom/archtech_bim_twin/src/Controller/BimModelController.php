<?php

declare(strict_types=1);

namespace Drupal\archtech_bim_twin\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class BimModelController extends ControllerBase {

  public function collection(Request $request): JsonResponse {
    if ($request->isMethod('GET')) {
      return $this->listModels($request);
    }
    if ($request->isMethod('POST')) {
      return $this->createModel($request);
    }
    throw new BadRequestHttpException('Method not allowed');
  }

  private function listModels(Request $request): JsonResponse {
    $query = $this->entityTypeManager()->getStorage('node')->getQuery()
      ->condition('type', 'bim_model')
      ->condition('status', 1)
      ->accessCheck(TRUE)
      ->sort('created', 'DESC');

    if ($status = $request->query->get('status')) {
      $query->condition('field_bim_status', $status);
    }
    if ($project = $request->query->get('project')) {
      $query->condition('field_bim_project', (int) $project);
    }

    $limit = min((int) $request->query->get('limit', '50'), 100);
    $offset = (int) $request->query->get('offset', '0');
    $query->range($offset, $limit);

    $nids = $query->execute();
    $models = [];

    if ($nids) {
      $nodes = $this->entityTypeManager()->getStorage('node')->loadMultiple($nids);
      foreach ($nodes as $node) {
        $models[] = $this->serializeModel($node);
      }
    }

    return new JsonResponse([
      'data' => $models,
      'total' => count($models),
      'offset' => $offset,
      'limit' => $limit,
    ]);
  }

  private function createModel(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (empty($data['title']) || empty($data['project'])) {
      throw new BadRequestHttpException('Title and project are required');
    }

    $node = Node::create([
      'type' => 'bim_model',
      'title' => $data['title'],
      'field_bim_project' => $data['project'],
      'field_bim_status' => $data['status'] ?? 'uploaded',
      'field_bim_validation_errors' => ['value' => $data['validation_errors'] ?? '', 'format' => 'plain_text'],
      'field_bim_metadata' => $data['metadata'] ?? '',
    ]);

    $node->save();

    return new JsonResponse(
      $this->serializeModel($node),
      JsonResponse::HTTP_CREATED,
    );
  }

  public function validate(Node $node, Request $request): JsonResponse {
    if ($node->bundle() !== 'bim_model') {
      throw new BadRequestHttpException('Not a BIM model entity');
    }

    $node->set('field_bim_status', 'processing');
    $node->save();

    // Simulate IFC validation.
    $errors = [];
    $ifcFile = $node->get('field_bim_ifc_file')->entity;

    if (!$ifcFile) {
      $errors[] = 'No IFC file attached to this model.';
    } else {
      $filesize = $ifcFile->getSize();
      if ($filesize < 1024) {
        $errors[] = 'IFC file appears too small — possible corruption.';
      }
      $extension = pathinfo($ifcFile->getFilename(), PATHINFO_EXTENSION);
      if (!in_array(strtolower($extension), ['ifc', 'ifczip', 'ifcxml'], TRUE)) {
        $errors[] = sprintf('Unsupported file extension: .%s', $extension);
      }
    }

    $metadata = json_decode($node->get('field_bim_metadata')->value ?? '', TRUE);
    if ($metadata === null && !empty($node->get('field_bim_metadata')->value)) {
      $errors[] = 'Metadata is not valid JSON.';
    }

    $valid = empty($errors);
    $node->set('field_bim_status', $valid ? 'validated' : 'error');
    $node->set('field_bim_validation_errors', [
      'value' => $errors ? implode("\n", $errors) : '',
      'format' => 'plain_text',
    ]);
    $node->save();

    return new JsonResponse([
      'model_id' => $node->id(),
      'status' => $node->get('field_bim_status')->value,
      'valid' => $valid,
      'errors' => $errors,
    ]);
  }

  private function serializeModel(Node $node): array {
    $ifcFile = null;
    if ($fid = $node->get('field_bim_ifc_file')->target_id) {
      $fileEntity = $this->entityTypeManager()->getStorage('file')->load($fid);
      if ($fileEntity) {
        $ifcFile = [
          'fid' => (int) $fid,
          'filename' => $fileEntity->getFilename(),
          'url' => $fileEntity->createFileUrl(),
          'size' => $fileEntity->getSize(),
        ];
      }
    }

    return [
      'id' => $node->id(),
      'uuid' => $node->uuid(),
      'title' => $node->label(),
      'project' => $node->get('field_bim_project')->target_id,
      'ifc_file' => $ifcFile,
      'status' => $node->get('field_bim_status')->value,
      'validation_errors' => $node->get('field_bim_validation_errors')->value,
      'metadata' => $node->get('field_bim_metadata')->value,
      'created' => $node->getCreatedTime(),
      'changed' => $node->getChangedTime(),
    ];
  }

}
