<?php

declare(strict_types=1);

namespace Drupal\archtech_suppliers\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class SupplierEventSubscriber implements EventSubscriberInterface {

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

    if ($route !== 'archtech_suppliers.collection' || !$request->isMethod('POST')) {
      return;
    }

    $content = $request->getContent();
    if (!$content) {
      return;
    }

    $data = json_decode($content, TRUE);
    if (isset($data['rating']) && (float) $data['rating'] > 0) {
      $payload = json_encode([
        'event' => 'supplier.rating_changed',
        'name' => $data['title'] ?? 'unknown',
        'rating' => (float) $data['rating'],
        'timestamp' => time(),
      ], JSON_THROW_ON_ERROR);

      \Drupal::logger('archtech_suppliers')->info(
        'Supplier rating change published to archtech.internal: @payload',
        ['@payload' => $payload],
      );
    }
  }

}
