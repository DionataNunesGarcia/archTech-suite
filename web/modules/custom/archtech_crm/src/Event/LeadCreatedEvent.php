<?php

declare(strict_types=1);

namespace Drupal\archtech_crm\Event;

use Drupal\Component\EventDispatcher\Event;
use Drupal\node\NodeInterface;

final class LeadCreatedEvent extends Event {

  public const EVENT_NAME = 'archtech_crm.lead.created';

  public function __construct(
    public readonly NodeInterface $lead,
  ) {}

}
