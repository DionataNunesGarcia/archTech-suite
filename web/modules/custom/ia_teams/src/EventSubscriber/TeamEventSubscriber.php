<?php

declare(strict_types=1);

namespace Drupal\ia_teams\EventSubscriber;

use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class TeamEventSubscriber implements EventSubscriberInterface {

  private array $allocationChanges = [];

  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::TERMINATE => ['onTerminate', 100],
    ];
  }

  public function onAllocationChanged(NodeInterface $node): void {
    if ($node->bundle() === 'project_allocation') {
      $this->allocationChanges[] = $node;
    }
  }

  public function onTerminate(TerminateEvent $event): void {
    foreach (array_unique($this->allocationChanges, SORT_REGULAR) as $node) {
      $this->publishTeamsEvent($node);
    }
    $this->allocationChanges = [];
  }

  private function publishTeamsEvent(NodeInterface $node): void {
    $payload = [
      'event' => 'archtech.teams',
      'node_id' => $node->id(),
      'project_id' => $node->get('field_alloc_project')->target_id ?? null,
      'member_id' => $node->get('field_alloc_member')->target_id ?? null,
      'start_date' => $node->get('field_alloc_start_date')->value ?? '',
      'end_date' => $node->get('field_alloc_end_date')->value ?? '',
      'percentage' => $node->get('field_alloc_percentage')->value ?? 100,
      'timestamp' => time(),
    ];

    \Drupal::logger('ia_teams')->info('Publishing archtech.teams event: @data', [
      '@data' => json_encode($payload),
    ]);
  }
}
