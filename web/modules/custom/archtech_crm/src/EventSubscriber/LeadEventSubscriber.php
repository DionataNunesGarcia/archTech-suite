<?php

declare(strict_types=1);

namespace Drupal\archtech_crm\EventSubscriber;

use Drupal\archtech_crm\Event\LeadCreatedEvent;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class LeadEventSubscriber implements EventSubscriberInterface {

  public function __construct(
    private readonly EventDispatcherInterface $eventDispatcher,
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public static function getSubscribedEvents(): array {
    return [
      LeadCreatedEvent::EVENT_NAME => 'onLeadCreated',
    ];
  }

  public function onLeadCreated(LeadCreatedEvent $event): void {
    $lead = $event->lead;

    \Drupal::logger('archtech_crm')->info('Lead created: @title (ID: @id)', [
      '@title' => $lead->label(),
      '@id' => $lead->id(),
    ]);

    $this->dispatchToRabbitMq($lead, 'archtech.leads');
  }

  public function onEntityInsert(NodeInterface $node): void {
    if ($node->bundle() !== 'lead') {
      return;
    }

    $event = new LeadCreatedEvent($node);
    $this->eventDispatcher->dispatch($event, LeadCreatedEvent::EVENT_NAME);
  }

  private function dispatchToRabbitMq(NodeInterface $lead, string $routingKey): void {
    $payload = json_encode([
      'event' => 'lead.created',
      'id' => $lead->id(),
      'title' => $lead->label(),
      'email' => $lead->get('field_lead_email')->value ?? '',
      'company' => $lead->get('field_lead_company')->value ?? '',
      'score' => (int) ($lead->get('field_lead_score')->value ?? 0),
      'status' => $lead->get('field_lead_status')->value ?? 'new',
      'timestamp' => $lead->getCreatedTime(),
    ], JSON_THROW_ON_ERROR);

    // RabbitMQ dispatch placeholder — integrate with a RabbitMQ module
    // such as rabbitmq or a custom queue service when available.
    \Drupal::logger('archtech_crm')->info(
      'RabbitMQ dispatch to routing key @key: @payload',
      ['@key' => $routingKey, '@payload' => $payload],
    );
  }

}
