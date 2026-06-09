<?php

declare(strict_types=1);

namespace Drupal\ia_deliverables\EventSubscriber;

use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class DeliverableEventSubscriber implements EventSubscriberInterface {

  private array $submittedDeliverables = [];

  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::TERMINATE => ['onTerminate', 100],
    ];
  }

  public function onDeliverableSubmitted(NodeInterface $node): void {
    if ($node->bundle() === 'deliverable') {
      $status = $node->get('field_deliverable_status')->value ?? '';
      if ($status === 'submitted') {
        $this->submittedDeliverables[] = $node;
      }
    }
  }

  public function onTerminate(TerminateEvent $event): void {
    foreach ($this->submittedDeliverables as $node) {
      $this->logSubmission($node);
    }
    $this->submittedDeliverables = [];
  }

  private function logSubmission(NodeInterface $node): void {
    \Drupal::logger('ia_deliverables')->info('Deliverable submitted: @id - @title', [
      '@id' => $node->id(),
      '@title' => $node->getTitle(),
    ]);
  }
}
