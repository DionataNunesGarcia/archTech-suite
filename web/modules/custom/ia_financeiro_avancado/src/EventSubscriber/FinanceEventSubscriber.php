<?php

declare(strict_types=1);

namespace Drupal\ia_financeiro_avancado\EventSubscriber;

use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class FinanceEventSubscriber implements EventSubscriberInterface {

  private array $approvedReimbursements = [];

  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::TERMINATE => ['onTerminate', 100],
    ];
  }

  public function onReimbursementApproved(NodeInterface $node, string $previousStatus = ''): void {
    if ($node->bundle() === 'reimbursement') {
      $currentStatus = $node->get('field_reimb_status')->value ?? '';
      if ($currentStatus === 'approved' && $previousStatus !== 'approved') {
        $this->approvedReimbursements[] = $node;
      }
    }
  }

  public function onTerminate(TerminateEvent $event): void {
    foreach ($this->approvedReimbursements as $node) {
      $this->publishFinancialAdvEvent($node);
    }
    $this->approvedReimbursements = [];
  }

  private function publishFinancialAdvEvent(NodeInterface $node): void {
    $payload = [
      'event' => 'archtech.financial_adv',
      'node_id' => $node->id(),
      'employee_id' => $node->get('field_reimb_employee')->target_id ?? null,
      'project_id' => $node->get('field_reimb_project')->target_id ?? null,
      'amount' => $node->get('field_reimb_amount')->value ?? 0,
      'category' => $node->get('field_reimb_category')->value ?? '',
      'status' => 'approved',
      'timestamp' => time(),
    ];

    \Drupal::logger('ia_financeiro_avancado')->info('Publishing archtech.financial_adv event: @data', [
      '@data' => json_encode($payload),
    ]);
  }
}
