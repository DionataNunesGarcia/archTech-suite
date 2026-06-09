<?php

declare(strict_types=1);

namespace Drupal\ia_teams\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class TeamController extends ControllerBase {

  public function members(Request $request): JsonResponse {
    $storage = $this->entityTypeManager()->getStorage('node');
    $query = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'team_member')
      ->sort('title', 'ASC');

    if ($squad = $request->query->get('squad')) {
      $query->condition('field_tm_squad', $squad);
    }

    $nids = $query->execute();
    $members = [];
    foreach ($storage->loadMultiple($nids) as $node) {
      assert($node instanceof NodeInterface);
      $members[] = $this->serializeMember($node);
    }

    return new JsonResponse(['data' => $members]);
  }

  public function squad(string $name): JsonResponse {
    $storage = $this->entityTypeManager()->getStorage('node');
    $nids = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'team_member')
      ->condition('field_tm_squad', $name)
      ->sort('title', 'ASC')
      ->execute();

    $members = [];
    foreach ($storage->loadMultiple($nids) as $node) {
      assert($node instanceof NodeInterface);
      $members[] = $this->serializeMember($node);
    }

    return new JsonResponse(['data' => $members]);
  }

  public function createAllocation(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (empty($data['project_id']) || empty($data['member_id'])) {
      return new JsonResponse(['error' => 'project_id and member_id are required'], 422);
    }

    $node = $this->entityTypeManager()->getStorage('node')->create([
      'type' => 'project_allocation',
      'title' => 'Allocation ' . date('Y-m-d'),
      'field_alloc_project' => $data['project_id'],
      'field_alloc_member' => $data['member_id'],
      'field_alloc_start_date' => $data['start_date'] ?? date('Y-m-d'),
      'field_alloc_end_date' => $data['end_date'] ?? null,
      'field_alloc_percentage' => $data['percentage'] ?? 100,
    ]);

    $node->save();

    return new JsonResponse([
      'message' => 'Allocation created',
      'id' => $node->id(),
    ], 201);
  }

  private function serializeMember(NodeInterface $node): array {
    return [
      'id' => (int) $node->id(),
      'title' => $node->getTitle(),
      'user_id' => $node->get('field_tm_user')->target_id ? (int) $node->get('field_tm_user')->target_id : null,
      'role' => $node->get('field_tm_role')->value ?? '',
      'squad' => $node->get('field_tm_squad')->value ?? '',
      'skills' => $node->get('field_tm_skills')->value ?? '',
      'availability_percentage' => (int) ($node->get('field_tm_availability')->value ?? 100),
    ];
  }
}
