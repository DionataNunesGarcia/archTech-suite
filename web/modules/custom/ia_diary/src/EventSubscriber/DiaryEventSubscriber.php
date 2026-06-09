<?php

declare(strict_types=1);

namespace Drupal\ia_diary\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class DiaryEventSubscriber implements EventSubscriberInterface {

  private array $entriesToNotify = [];

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::TERMINATE => ['onTerminate', 100],
    ];
  }

  public function onEntryCreated(NodeInterface $node): void {
    if ($node->bundle() === 'diary_entry' && $node->isNew()) {
      $this->entriesToNotify[] = $node;
    }
  }

  public function onTerminate(TerminateEvent $event): void {
    foreach ($this->entriesToNotify as $node) {
      $this->publishDiaryEvent($node);
    }
    $this->entriesToNotify = [];
  }

  private function publishDiaryEvent(NodeInterface $node): void {
    $payload = [
      'event' => 'archtech.diary',
      'node_id' => $node->id(),
      'project_id' => $node->get('field_diary_project')->target_id ?? null,
      'date' => $node->get('field_diary_date')->value ?? '',
      'workers_count' => $node->get('field_diary_workers_count')->value ?? 0,
      'timestamp' => time(),
    ];

    \Drupal::logger('ia_diary')->info('Publishing archtech.diary event: @data', [
      '@data' => json_encode($payload),
    ]);
  }
}
