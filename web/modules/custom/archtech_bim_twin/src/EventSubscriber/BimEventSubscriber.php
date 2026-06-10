<?php

declare(strict_types=1);

namespace Drupal\archtech_bim_twin\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class BimEventSubscriber implements EventSubscriberInterface {

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

    if ($route !== 'archtech_bim_twin.models.validate') {
      return;
    }

    $node = $request->attributes->get('node');
    if (!$node) {
      return;
    }

    \Drupal::logger('archtech_bim_twin')->info(
      'BIM model #@id validated — status: @status',
      ['@id' => $node->id(), '@status' => $node->get('field_bim_status')->value],
    );
  }

}
