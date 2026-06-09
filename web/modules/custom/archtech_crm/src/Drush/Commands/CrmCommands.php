<?php

declare(strict_types=1);

namespace Drupal\archtech_crm\Drush\Commands;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drush\Attributes as CLI;
use Drush\Commands\AutowireTrait;
use Drush\Commands\DrushCommands;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class CrmCommands extends DrushCommands {

  use AutowireTrait;

  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    private readonly EventDispatcherInterface $eventDispatcher,
  ) {
    parent::__construct();
  }

  #[CLI\Command(name: 'archtech:crm:score-leads', aliases: ['acsl'])]
  #[CLI\Help(description: 'Score all unqualified leads using AI heuristics.')]
  #[CLI\Usage(name: 'drush archtech:crm:score-leads', description: 'Score all leads with status "new"')]
  public function scoreLeads(): void {
    $storage = $this->entityTypeManager()->getStorage('node');
    $nids = $storage->getQuery()
      ->condition('type', 'lead')
      ->condition('field_lead_status', 'new')
      ->condition('status', 1)
      ->accessCheck(FALSE)
      ->execute();

    if (empty($nids)) {
      $this->output()->writeln('No unscored leads found.');
      return;
    }

    $nodes = $storage->loadMultiple($nids);
    $scored = 0;

    foreach ($nodes as $node) {
      $score = $this->calculateScore($node);
      $node->set('field_lead_score', $score);
      $node->save();
      $scored++;
      $this->output()->writeln(sprintf('Lead #%d "%s" scored: %d', $node->id(), $node->label(), $score));
    }

    $this->output()->writeln(sprintf('Scored %d leads.', $scored));
  }

  private function calculateScore($node): int {
    $score = 1;

    // Higher score for leads with company info.
    if (!empty($node->get('field_lead_company')->value)) {
      $score += 2;
    }

    // Higher score for leads with phone.
    if (!empty($node->get('field_lead_phone')->value)) {
      $score += 1;
    }

    // Check email domain for corporate vs personal.
    $email = $node->get('field_lead_email')->value ?? '';
    $freeDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'aol.com'];
    $domain = strtolower(substr((string) strrchr($email, '@'), 1));
    if ($domain && !in_array($domain, $freeDomains, TRUE)) {
      $score += 2;
    }

    // Notes indicate engagement.
    if (!empty($node->get('field_lead_notes')->value)) {
      $score += 1;
    }

    return min($score, 10);
  }

}
