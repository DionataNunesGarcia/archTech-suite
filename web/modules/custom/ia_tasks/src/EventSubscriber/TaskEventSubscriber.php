<?php

declare(strict_types=1);

namespace Drupal\ia_tasks\EventSubscriber;

use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class TaskEventSubscriber implements EventSubscriberInterface {

  private array $statusChanges = [];

  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::TERMINATE => ['onTerminate', 100],
    ];
  }

  public function onTaskStatusChange(NodeInterface $node, string $previousStatus = ''): void {
    if ($node->bundle() === 'task') {
      $currentStatus = $node->get('field_task_status')->value ?? '';
      if ($currentStatus !== $previousStatus) {
        $this->statusChanges[] = [
          'node' => $node,
          'previous' => $previousStatus,
        ];
      }
    }
  }

  public function onTerminate(TerminateEvent $event): void {
    foreach ($this->statusChanges as $change) {
      $this->logStatusChange($change['node'], $change['previous']);
    }
    $this->statusChanges = [];
  }

  private function logStatusChange(NodeInterface $node, string $previousStatus): void {
    $currentStatus = $node->get('field_task_status')->value ?? '';
    \Drupal::logger('ia_tasks')->info('Task @id moved: @prev -> @curr', [
      '@id' => $node->id(),
      '@prev' => $previousStatus ?: 'new',
      '@curr' => $currentStatus,
    ]);
  }
}
