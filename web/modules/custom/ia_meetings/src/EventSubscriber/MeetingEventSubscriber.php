<?php

declare(strict_types=1);

namespace Drupal\ia_meetings\EventSubscriber;

use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class MeetingEventSubscriber implements EventSubscriberInterface {

  private array $completedMeetings = [];

  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::TERMINATE => ['onTerminate', 100],
    ];
  }

  public function onMeetingCompleted(NodeInterface $node): void {
    if ($node->bundle() === 'meeting_record') {
      $status = $node->get('field_meeting_status')->value ?? '';
      if ($status === 'completed' || $status === 'transcribed') {
        $this->completedMeetings[] = $node;
      }
    }
  }

  public function onTerminate(TerminateEvent $event): void {
    foreach ($this->completedMeetings as $node) {
      $this->publishMeetingEvent($node);
    }
    $this->completedMeetings = [];
  }

  private function publishMeetingEvent(NodeInterface $node): void {
    $payload = [
      'event' => 'archtech.meetings',
      'node_id' => $node->id(),
      'project_id' => $node->get('field_meeting_project')->target_id ?? null,
      'status' => $node->get('field_meeting_status')->value ?? '',
      'date' => $node->get('field_meeting_date')->value ?? '',
      'has_transcript' => !empty($node->get('field_meeting_transcript')->value),
      'timestamp' => time(),
    ];

    \Drupal::logger('ia_meetings')->info('Publishing archtech.meetings event: @data', [
      '@data' => json_encode($payload),
    ]);
  }
}
