<?php

declare(strict_types=1);

namespace Drupal\ia_marketing_digital\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class CampaignController extends ControllerBase {

  public function collection(Request $request): JsonResponse {
    $storage = $this->entityTypeManager()->getStorage('node');
    $query = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'campaign')
      ->sort('created', 'DESC')
      ->range(0, 50);

    if ($status = $request->query->get('status')) {
      $query->condition('field_campaign_status', $status);
    }
    if ($channel = $request->query->get('channel')) {
      $query->condition('field_campaign_channel', $channel);
    }

    $nids = $query->execute();
    $campaigns = [];
    foreach ($storage->loadMultiple($nids) as $node) {
      assert($node instanceof NodeInterface);
      $campaigns[] = $this->serializeCampaign($node);
    }

    return new JsonResponse(['data' => $campaigns]);
  }

  public function createCampaign(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (empty($data['name'])) {
      return new JsonResponse(['error' => 'name is required'], 422);
    }

    $node = $this->entityTypeManager()->getStorage('node')->create([
      'type' => 'campaign',
      'title' => $data['name'],
      'field_campaign_name' => $data['name'],
      'field_campaign_channel' => $data['channel'] ?? '',
      'field_campaign_start_date' => $data['start_date'] ?? date('Y-m-d'),
      'field_campaign_end_date' => $data['end_date'] ?? null,
      'field_campaign_budget' => $data['budget'] ?? 0,
      'field_campaign_status' => $data['status'] ?? 'planned',
      'field_campaign_metrics' => [
        'value' => $data['metrics'] ?? '',
        'format' => 'plain_text',
      ],
    ]);

    $node->save();

    return new JsonResponse([
      'message' => 'Campaign created',
      'id' => $node->id(),
    ], 201);
  }

  private function serializeCampaign(NodeInterface $node): array {
    return [
      'id' => (int) $node->id(),
      'title' => $node->getTitle(),
      'name' => $node->get('field_campaign_name')->value ?? $node->getTitle(),
      'channel' => $node->get('field_campaign_channel')->value ?? '',
      'start_date' => $node->get('field_campaign_start_date')->value ?? '',
      'end_date' => $node->get('field_campaign_end_date')->value ?? '',
      'budget' => (float) ($node->get('field_campaign_budget')->value ?? 0),
      'status' => $node->get('field_campaign_status')->value ?? '',
      'metrics' => $node->get('field_campaign_metrics')->value ?? '',
      'created' => $node->getCreatedTime(),
    ];
  }
}
