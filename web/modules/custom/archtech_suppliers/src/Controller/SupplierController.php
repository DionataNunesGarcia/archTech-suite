<?php

declare(strict_types=1);

namespace Drupal\archtech_suppliers\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class SupplierController extends ControllerBase {

  public function collection(Request $request): JsonResponse {
    if ($request->isMethod('GET')) {
      return $this->listSuppliers($request);
    }
    if ($request->isMethod('POST')) {
      return $this->createSupplier($request);
    }
    throw new BadRequestHttpException('Method not allowed');
  }

  private function listSuppliers(Request $request): JsonResponse {
    $query = $this->entityTypeManager()->getStorage('node')->getQuery()
      ->condition('type', 'supplier')
      ->condition('status', 1)
      ->accessCheck(TRUE)
      ->sort('title', 'ASC');

    if ($category = $request->query->get('category')) {
      $query->condition('field_supplier_category', $category);
    }

    $limit = min((int) $request->query->get('limit', '50'), 100);
    $offset = (int) $request->query->get('offset', '0');
    $query->range($offset, $limit);

    $nids = $query->execute();
    $suppliers = [];

    if ($nids) {
      $nodes = $this->entityTypeManager()->getStorage('node')->loadMultiple($nids);
      foreach ($nodes as $node) {
        $suppliers[] = $this->serializeSupplier($node);
      }
    }

    return new JsonResponse([
      'data' => $suppliers,
      'total' => count($suppliers),
      'offset' => $offset,
      'limit' => $limit,
    ]);
  }

  private function createSupplier(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (empty($data['title']) || empty($data['category'])) {
      throw new BadRequestHttpException('Title and category are required');
    }

    $node = Node::create([
      'type' => 'supplier',
      'title' => $data['title'],
      'field_supplier_category' => $data['category'],
      'field_supplier_rating' => $data['rating'] ?? '0.0',
      'field_supplier_email' => $data['email'] ?? '',
      'field_supplier_phone' => $data['phone'] ?? '',
      'field_supplier_sla_score' => $data['sla_score'] ?? '0.00',
    ]);

    $node->save();

    return new JsonResponse(
      $this->serializeSupplier($node),
      JsonResponse::HTTP_CREATED,
    );
  }

  public function sla(Node $node, Request $request): JsonResponse {
    if ($node->bundle() !== 'supplier') {
      throw new BadRequestHttpException('Not a supplier entity');
    }

    $slaScore = (float) $node->get('field_supplier_sla_score')->value;
    $rating = (float) $node->get('field_supplier_rating')->value;

    $status = 'unknown';
    if ($slaScore >= 90.0) {
      $status = 'excellent';
    } elseif ($slaScore >= 75.0) {
      $status = 'good';
    } elseif ($slaScore >= 50.0) {
      $status = 'average';
    } elseif ($slaScore > 0) {
      $status = 'poor';
    }

    return new JsonResponse([
      'supplier_id' => $node->id(),
      'supplier_name' => $node->label(),
      'sla_score' => $slaScore,
      'rating' => $rating,
      'status' => $status,
      'recommendation' => $this->getRecommendation($slaScore, $rating),
    ]);
  }

  private function getRecommendation(float $slaScore, float $rating): string {
    if ($slaScore >= 80.0 && $rating >= 4.0) {
      return 'Highly recommended — excellent SLA and rating.';
    }
    if ($slaScore >= 60.0 && $rating >= 3.0) {
      return 'Recommended — meets quality standards.';
    }
    if ($slaScore >= 40.0) {
      return 'Use with caution — monitor SLA compliance.';
    }
    return 'Not recommended — poor performance metrics.';
  }

  private function serializeSupplier(Node $node): array {
    return [
      'id' => $node->id(),
      'uuid' => $node->uuid(),
      'name' => $node->label(),
      'category' => $node->get('field_supplier_category')->value,
      'rating' => (float) $node->get('field_supplier_rating')->value,
      'email' => $node->get('field_supplier_email')->value,
      'phone' => $node->get('field_supplier_phone')->value,
      'sla_score' => (float) $node->get('field_supplier_sla_score')->value,
      'created' => $node->getCreatedTime(),
      'changed' => $node->getChangedTime(),
    ];
  }

}
