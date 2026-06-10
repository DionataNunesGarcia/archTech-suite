<?php

declare(strict_types=1);

namespace Drupal\archtech_library\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class DocumentEventSubscriber implements EventSubscriberInterface {

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

    if ($route !== 'archtech_library.search') {
      return;
    }

    $q = $request->query->get('q', '');
    if ($q === '' || $q === null) {
      return;
    }

    // When a document is "indexed" (searched), publish event.
    $payload = json_encode([
      'event' => 'document.indexed',
      'query' => $q,
      'timestamp' => time(),
    ], JSON_THROW_ON_ERROR);

    \Drupal::logger('archtech_library')->info(
      'Document index event published to archtech.internal: @payload',
      ['@payload' => $payload],
    );
  }

}
