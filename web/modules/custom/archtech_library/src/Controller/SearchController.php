<?php

declare(strict_types=1);

namespace Drupal\archtech_library\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class SearchController extends ControllerBase {

  public function search(Request $request): JsonResponse {
    $query = $this->entityTypeManager()->getStorage('node')->getQuery()
      ->condition('type', 'document')
      ->condition('status', 1)
      ->accessCheck(TRUE)
      ->sort('title', 'ASC');

    if ($q = $request->query->get('q')) {
      $q = trim($q);
      if ($q !== '') {
        $group = $query->orConditionGroup()
          ->condition('title', '%' . $q . '%', 'LIKE')
          ->condition('field_document_description', '%' . $q . '%', 'LIKE')
          ->condition('field_document_tags', '%' . $q . '%', 'LIKE');
        $query->condition($group);
      }
    }

    if ($category = $request->query->get('category')) {
      $query->condition('field_document_category', $category);
    }

    $limit = min((int) $request->query->get('limit', '50'), 100);
    $offset = (int) $request->query->get('offset', '0');
    $query->range($offset, $limit);

    $nids = $query->execute();
    $documents = [];

    if ($nids) {
      $nodes = $this->entityTypeManager()->getStorage('node')->loadMultiple($nids);
      foreach ($nodes as $node) {
        $documents[] = $this->serializeDocument($node);
      }
    }

    \Drupal::logger('archtech_library')->info(
      'Library search query="%q" returned %count results.',
      ['%q' => $q ?? '', '%count' => count($documents)],
    );

    return new JsonResponse([
      'data' => $documents,
      'total' => count($documents),
      'offset' => $offset,
      'limit' => $limit,
      'query' => $q ?? null,
    ]);
  }

  private function serializeDocument($node): array {
    $file = null;
    if ($fid = $node->get('field_document_file')->target_id) {
      $fileEntity = $this->entityTypeManager()->getStorage('file')->load($fid);
      if ($fileEntity) {
        $file = [
          'fid' => (int) $fid,
          'filename' => $fileEntity->getFilename(),
          'url' => $fileEntity->createFileUrl(),
        ];
      }
    }

    return [
      'id' => $node->id(),
      'uuid' => $node->uuid(),
      'title' => $node->label(),
      'category' => $node->get('field_document_category')->value,
      'tags' => $node->get('field_document_tags')->value,
      'description' => $node->get('field_document_description')->value,
      'file' => $file,
      'created' => $node->getCreatedTime(),
      'changed' => $node->getChangedTime(),
    ];
  }

}
