<?php

declare(strict_types=1);

namespace Drupal\archtech_ai_gateway\Service;

use Drupal\Core\Database\Connection;

/**
 * Records AI API token usage and cost for each call.
 *
 * Writes to the archtech_ai_cost_log table for billing and analytics.
 */
final class CostTrackerService {

  public function __construct(
    private Connection $database,
  ) {}

  /**
   * Records a successful AI API call.
   *
   * @param string $provider
   *   AI provider (e.g. 'openai', 'anthropic').
   * @param string $model
   *   Model name (e.g. 'gpt-4o').
   * @param string $promptName
   *   Logical prompt name from the registry.
   * @param int $inputTokens
   * @param int $outputTokens
   * @param float $cost
   *   Calculated cost in the configured currency.
   * @param int $durationMs
   *   Wall-clock duration in milliseconds.
   * @param int|null $userId
   *   User who initiated the call.
   */
  public function recordSuccess(
    string $provider,
    string $model,
    string $promptName,
    int $inputTokens,
    int $outputTokens,
    float $cost,
    int $durationMs,
    ?int $userId = NULL,
  ): void {
    $this->database->insert('archtech_ai_cost_log')
      ->fields([
        'provider' => $provider,
        'model' => $model,
        'prompt_name' => $promptName,
        'input_tokens' => $inputTokens,
        'output_tokens' => $outputTokens,
        'total_tokens' => $inputTokens + $outputTokens,
        'cost' => \number_format($cost, 10, '.', ''),
        'duration_ms' => $durationMs,
        'status' => 'success',
        'user_id' => $userId,
        'created_at' => \gmdate('Y-m-d\TH:i:s.v\Z'),
      ])
      ->execute();
  }

  /**
   * Records a failed or circuit-blocked AI API call.
   *
   * @param string $status
   *   'error' or 'circuit_open'.
   */
  public function recordFailure(
    string $provider,
    string $model,
    string $promptName,
    string $status,
    ?string $errorMessage = NULL,
    ?int $userId = NULL,
  ): void {
    $this->database->insert('archtech_ai_cost_log')
      ->fields([
        'provider' => $provider,
        'model' => $model,
        'prompt_name' => $promptName,
        'input_tokens' => 0,
        'output_tokens' => 0,
        'total_tokens' => 0,
        'cost' => '0.0000000000',
        'duration_ms' => 0,
        'status' => $status,
        'error_message' => $errorMessage !== NULL ? \mb_substr($errorMessage, 0, 2000) : NULL,
        'user_id' => $userId,
        'created_at' => \gmdate('Y-m-d\TH:i:s.v\Z'),
      ])
      ->execute();
  }

  /**
   * Returns aggregated cost for a given time window.
   *
   * @return array<string, array{total_cost: float, total_tokens: int, call_count: int}>
   */
  public function aggregateByProvider(string $from, string $to): array {
    $rows = $this->database->query(
      'SELECT provider, SUM(cost) AS total_cost, SUM(total_tokens) AS total_tokens, COUNT(*) AS call_count
       FROM {archtech_ai_cost_log}
       WHERE created_at BETWEEN :from AND :to
         AND status = :status
       GROUP BY provider
       ORDER BY total_cost DESC',
      [
        ':from' => $from,
        ':to' => $to,
        ':status' => 'success',
      ],
    )->fetchAll();

    $result = [];
    foreach ($rows as $row) {
      $result[$row->provider] = [
        'total_cost' => (float) $row->total_cost,
        'total_tokens' => (int) $row->total_tokens,
        'call_count' => (int) $row->call_count,
      ];
    }

    return $result;
  }

  /**
   * Returns cost summary for a specific prompt.
   */
  public function aggregateByPrompt(string $promptName, string $from, string $to): array {
    $rows = $this->database->query(
      'SELECT model, SUM(cost) AS total_cost, AVG(cost) AS avg_cost, COUNT(*) AS call_count, AVG(duration_ms) AS avg_duration_ms
       FROM {archtech_ai_cost_log}
       WHERE prompt_name = :prompt_name
         AND created_at BETWEEN :from AND :to
         AND status = :status
       GROUP BY model
       ORDER BY total_cost DESC',
      [
        ':prompt_name' => $promptName,
        ':from' => $from,
        ':to' => $to,
        ':status' => 'success',
      ],
    )->fetchAll();

    $result = [];
    foreach ($rows as $row) {
      $result[$row->model] = [
        'total_cost' => (float) $row->total_cost,
        'avg_cost' => (float) $row->avg_cost,
        'call_count' => (int) $row->call_count,
        'avg_duration_ms' => (float) $row->avg_duration_ms,
      ];
    }

    return $result;
  }

}
