<?php

declare(strict_types=1);

namespace Drupal\ia_marketing_digital\EventSubscriber;

use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class CampaignEventSubscriber implements EventSubscriberInterface {

  private array $campaignStatusChanges = [];

  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::TERMINATE => ['onTerminate', 100],
    ];
  }

  public function onCampaignStatusChange(NodeInterface $node, string $previousStatus = ''): void {
    if ($node->bundle() === 'campaign') {
      $currentStatus = $node->get('field_campaign_status')->value ?? '';
      if ($currentStatus !== $previousStatus) {
        $this->campaignStatusChanges[] = [
          'node' => $node,
          'previous' => $previousStatus,
        ];
      }
    }
  }

  public function onTerminate(TerminateEvent $event): void {
    foreach ($this->campaignStatusChanges as $change) {
      $this->logCampaignChange($change['node'], $change['previous']);
    }
    $this->campaignStatusChanges = [];
  }

  private function logCampaignChange(NodeInterface $node, string $previousStatus): void {
    $currentStatus = $node->get('field_campaign_status')->value ?? '';
    \Drupal::logger('ia_marketing_digital')->info('Campaign @id status changed from @prev to @curr', [
      '@id' => $node->id(),
      '@prev' => $previousStatus ?: 'new',
      '@curr' => $currentStatus,
    ]);
  }
}
