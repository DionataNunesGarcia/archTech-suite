<?php

declare(strict_types=1);

namespace Drupal\archtech_core_api\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Injects trace_id into all HTTP responses and propagates to RabbitMQ messages.
 *
 * Reads trace_id from X-Trace-Id header (incoming) or generates a ULID.
 * Stores in request attributes for use by event publishers (OutboxService).
 */
final class TraceIdSubscriber implements EventSubscriberInterface {

  public function onKernelRequest(RequestEvent $event): void {
    if (!$event->isMainRequest()) {
      return;
    }

    $request = $event->getRequest();
    $traceId = $request->headers->get('X-Trace-Id');

    if (!$traceId || !preg_match('/^[a-f0-9]{32,}$/i', $traceId)) {
      $traceId = $this->generateTraceId();
    }

    $request->attributes->set('archtech.trace_id', $traceId);
  }

  public function onKernelResponse(ResponseEvent $event): void {
    if (!$event->isMainRequest()) {
      return;
    }

    $traceId = $event->getRequest()->attributes->get('archtech.trace_id');
    if ($traceId) {
      $event->getResponse()->headers->set('X-Trace-Id', $traceId);
    }
  }

  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => ['onKernelRequest', 250],
      KernelEvents::RESPONSE => ['onKernelResponse', -250],
    ];
  }

  private function generateTraceId(): string {
    return bin2hex(random_bytes(16));
  }

}
