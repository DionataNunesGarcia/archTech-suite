<?php

declare(strict_types=1);

namespace Drupal\archtech_financeiro\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class InvoiceEventSubscriber implements EventSubscriberInterface {

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

    if ($route !== 'archtech_financeiro.invoices.status' || !$request->isMethod('PATCH')) {
      return;
    }

    $content = $request->getContent();
    if (!$content) {
      return;
    }

    $data = json_decode($content, TRUE);
    $node = $request->attributes->get('node');

    if ($node && isset($data['status']) && $data['status'] === 'paid') {
      $payload = json_encode([
        'event' => 'invoice.paid',
        'id' => $node->id(),
        'title' => $node->label(),
        'amount' => (float) $node->get('field_invoice_amount')->value,
        'client' => $node->get('field_invoice_client')->target_id,
        'paid_date' => $node->get('field_invoice_paid_date')->value,
        'timestamp' => time(),
      ], JSON_THROW_ON_ERROR);

      \Drupal::logger('archtech_financeiro')->info(
        'Payment received — published to archtech.internal: @payload',
        ['@payload' => $payload],
      );
    }
  }

}
