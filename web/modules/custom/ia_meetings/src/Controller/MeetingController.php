<?php

declare(strict_types=1);

namespace Drupal\ia_meetings\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class MeetingController extends ControllerBase {

  public function collection(Request $request): JsonResponse {
    $storage = $this->entityTypeManager()->getStorage('node');
    $query = $storage->getQuery()
      ->accessCheck(TRUE)
      ->condition('type', 'meeting_record')
      ->sort('created', 'DESC')
      ->range(0, 50);

    if ($project_id = $request->query->get('project_id')) {
      $query->condition('field_meeting_project', (int) $project_id);
    }
    if ($status = $request->query->get('status')) {
      $query->condition('field_meeting_status', $status);
    }

    $nids = $query->execute();
    $meetings = [];
    foreach ($storage->loadMultiple($nids) as $node) {
      assert($node instanceof NodeInterface);
      $meetings[] = $this->serializeMeeting($node);
    }

    return new JsonResponse(['data' => $meetings]);
  }

  public function createMeeting(Request $request): JsonResponse {
    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (empty($data['project_id'])) {
      return new JsonResponse(['error' => 'project_id is required'], 422);
    }

    $node = $this->entityTypeManager()->getStorage('node')->create([
      'type' => 'meeting_record',
      'title' => $data['title'] ?? 'Meeting ' . date('Y-m-d H:i'),
      'field_meeting_project' => $data['project_id'],
      'field_meeting_date' => $data['date'] ?? date('Y-m-d\TH:i:s'),
      'field_meeting_attendees' => [
        'value' => $data['attendees'] ?? '',
        'format' => 'plain_text',
      ],
      'field_meeting_agenda' => [
        'value' => $data['agenda'] ?? '',
        'format' => 'plain_text',
      ],
      'field_meeting_status' => $data['status'] ?? 'scheduled',
    ]);

    $node->save();

    return new JsonResponse([
      'message' => 'Meeting created',
      'id' => $node->id(),
    ], 201);
  }

  public function updateTranscript(int $id, Request $request): JsonResponse {
    $node = $this->entityTypeManager()->getStorage('node')->load($id);
    if (!$node || $node->bundle() !== 'meeting_record') {
      return new JsonResponse(['error' => 'Meeting not found'], 404);
    }

    $data = json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (isset($data['transcript'])) {
      $node->set('field_meeting_transcript', [
        'value' => $data['transcript'],
        'format' => 'plain_text',
      ]);
    }
    if (isset($data['decisions'])) {
      $node->set('field_meeting_decisions', [
        'value' => $data['decisions'],
        'format' => 'plain_text',
      ]);
    }
    $node->save();

    return new JsonResponse([
      'message' => 'Transcript updated',
      'id' => $node->id(),
    ]);
  }

  private function serializeMeeting(NodeInterface $node): array {
    return [
      'id' => (int) $node->id(),
      'title' => $node->getTitle(),
      'project_id' => $node->get('field_meeting_project')->target_id ? (int) $node->get('field_meeting_project')->target_id : null,
      'date' => $node->get('field_meeting_date')->value ?? '',
      'attendees' => $node->get('field_meeting_attendees')->value ?? '',
      'agenda' => $node->get('field_meeting_agenda')->value ?? '',
      'status' => $node->get('field_meeting_status')->value ?? '',
      'created' => $node->getCreatedTime(),
    ];
  }
}
