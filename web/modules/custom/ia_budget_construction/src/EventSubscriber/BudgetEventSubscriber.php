<?php

declare(strict_types=1);

namespace Drupal\ia_budget_construction\EventSubscriber;

use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class BudgetEventSubscriber implements EventSubscriberInterface {

  private array $budgetChanges = [];

  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::TERMINATE => ['onTerminate', 100],
    ];
  }

  public function onBudgetChanged(NodeInterface $node): void {
    if ($node->bundle() === 'budget' || $node->bundle() === 'measurement') {
      $this->budgetChanges[] = $node;
    }
  }

  public function onTerminate(TerminateEvent $event): void {
    foreach ($this->budgetChanges as $node) {
      $this->logBudgetChange($node);
    }
    $this->budgetChanges = [];
  }

  private function logBudgetChange(NodeInterface $node): void {
    $bundle = $node->bundle();
    \Drupal::logger('ia_budget_construction')->info('Budget change detected: @type/@id', [
      '@type' => $bundle,
      '@id' => $node->id(),
    ]);
  }
}
