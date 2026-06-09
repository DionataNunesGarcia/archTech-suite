<?php

declare(strict_types=1);

namespace Drupal\archtech_events\Commands;

use Drupal\archtech_events\EventBus\OutboxService;
use Drush\Attributes as CLI;
use Drush\Commands\AutowireTrait;
use Drush\Commands\DrushCommands;

/**
 * Drush commands for processing the transactional outbox.
 */
#[CLI\Bootstrap(level: Drupal\Core\DrupalKernelInterface::BOOTSTRAP_FULL)]
final class OutboxProcessorCommands extends DrushCommands {

  use AutowireTrait;

  public function __construct(
    private readonly OutboxService $outboxService,
  ) {}

  /**
   * Process pending outbox messages and publish to RabbitMQ.
   */
  #[CLI\Command(name: 'archtech:outbox:process')]
  #[CLI\Option(name: 'batch-size', description: 'Number of messages to process per run.')]
  #[CLI\Usage(name: 'drush archtech:outbox:process', description: 'Process pending outbox entries.')]
  public function processOutbox(array $options = ['batch-size' => 50]): void {
    $batchSize = (int) ($options['batch-size'] ?? 50);
    $result = $this->outboxService->processBatch($batchSize);

    $this->output()->writeln(
      \sprintf('Outbox processed: %d succeeded, %d failed.', $result['processed'], $result['failed']),
    );
  }

  /**
   * Move exhausted outbox entries to Dead Letter Queue.
   */
  #[CLI\Command(name: 'archtech:outbox:dlq')]
  #[CLI\Usage(name: 'drush archtech:outbox:dlq', description: 'Move max-retried messages to DLQ.')]
  public function moveToDlq(): void {
    $count = $this->outboxService->moveToDlq();
    $this->output()->writeln(\sprintf('Moved %d entries to DLQ.', $count));
  }

  /**
   * Retry failed outbox messages.
   */
  #[CLI\Command(name: 'archtech:outbox:retry')]
  #[CLI\Option(name: 'batch-size', description: 'Number of messages to retry per run.')]
  #[CLI\Usage(name: 'drush archtech:outbox:retry', description: 'Retry failed outbox entries.')]
  public function retryFailed(array $options = ['batch-size' => 50]): void {
    $batchSize = (int) ($options['batch-size'] ?? 50);
    $count = $this->outboxService->retryFailed($batchSize);
    $this->output()->writeln(\sprintf('Retried %d failed entries.', $count));
  }

}
