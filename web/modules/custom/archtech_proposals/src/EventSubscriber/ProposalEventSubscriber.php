<?php

declare(strict_types=1);

namespace Drupal\archtech_proposals\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ProposalEventSubscriber implements EventSubscriberInterface {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::TERMINATE => 'onTerminate',
    ];
  }

  public function onTerminate(TerminateEvent $event): void {
    $request = $event->getRequest();
    $route = $request->attributes->get('_route');

    if ($route !== 'archtech_proposals.status' || !$request->isMethod('PATCH')) {
      return;
    }

    $content = $request->getContent();
    if (!$content) {
      return;
    }

    $data = json_decode($content, TRUE);
    $node = $request->attributes->get('node');

    if ($node && isset($data['status'])) {
      $payload = json_encode([
        'event' => 'proposal.status_changed',
        'id' => $node->id(),
        'title' => $node->label(),
        'status' => $data['status'],
        'timestamp' => time(),
      ], JSON_THROW_ON_ERROR);

      \Drupal::logger('archtech_proposals')->info(
        'Proposal status change published to archtech.projects: @payload',
        ['@payload' => $payload],
      );
    }
  }

}
