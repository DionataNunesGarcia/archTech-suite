<?php

declare(strict_types=1);

namespace Drupal\archtech_ai_gateway\Service;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Cache\CacheBackendInterface;

/**
 * AI gateway service with circuit breaker, retry backoff, and cost tracking.
 *
 * The circuit breaker tracks consecutive failures per provider in Redis cache.
 * After 5 failures within a 60-second window, the circuit opens and all calls
 * are short-circuited for 30 seconds.
 *
 * Retry uses exponential backoff: 1s, 2s, 4s, 8s (configurable max).
 */
final class AiGatewayService {

  private const int CIRCUIT_FAILURE_THRESHOLD = 5;
  private const int CIRCUIT_WINDOW_SECONDS = 60;
  private const int CIRCUIT_OPEN_SECONDS = 30;
  private const int MAX_RETRIES = 4;

  public function __construct(
    private readonly CostTrackerService $costTracker,
    private readonly PromptRegistryService $promptRegistry,
    private readonly CacheBackendInterface $cache,
    private readonly TimeInterface $time,
  ) {}

  /**
   * Executes an AI call through the gateway with all protections.
   *
   * @param string $promptName
   *   Logical prompt name from the registry.
   * @param array<string, string> $variables
   *   Template variables for prompt rendering.
   * @param callable $apiCall
   *   The actual API call function. Receives prompt config and returns
   *   an array with keys: 'content', 'input_tokens', 'output_tokens'.
   * @param int|null $userId
   *   Acting user for cost attribution.
   *
   * @throws \RuntimeException
   *   When circuit is open, all retries exhausted, or prompt not found.
   */
  public function execute(
    string $promptName,
    array $variables,
    callable $apiCall,
    ?int $userId = NULL,
  ): string {
    $prompt = $this->promptRegistry->loadPrompt($promptName);

    if ($prompt === NULL) {
      throw new \RuntimeException(\sprintf('Prompt "%s" not found in registry.', $promptName));
    }

    $provider = $prompt['provider'];

    if ($this->isCircuitOpen($provider)) {
      $this->costTracker->recordFailure(
        provider: $provider,
        model: $prompt['model'],
        promptName: $promptName,
        status: 'circuit_open',
        errorMessage: \sprintf('Circuit open for provider "%s".', $provider),
        userId: $userId,
      );
      throw new \RuntimeException(\sprintf('AI provider "%s" is temporarily unavailable (circuit open).', $provider));
    }

    $lastException = NULL;
    $startTime = \hrtime(TRUE);

    for ($attempt = 0; $attempt <= self::MAX_RETRIES; $attempt++) {
      try {
        $result = $apiCall($prompt);

        $durationMs = (int) ((\hrtime(TRUE) - $startTime) / 1_000_000);

        $cost = $this->calculateCost(
          $result['input_tokens'],
          $result['output_tokens'],
          $prompt['input_cost_per_1k'],
          $prompt['output_cost_per_1k'],
        );

        $this->costTracker->recordSuccess(
          provider: $provider,
          model: $prompt['model'],
          promptName: $promptName,
          inputTokens: $result['input_tokens'],
          outputTokens: $result['output_tokens'],
          cost: $cost,
          durationMs: $durationMs,
          userId: $userId,
        );

        $this->recordSuccess($provider);

        return $result['content'];
      } catch (\Throwable $e) {
        $lastException = $e;

        if ($attempt < self::MAX_RETRIES) {
          $backoffMs = (2 ** $attempt) * 1000;
          \usleep($backoffMs * 1000);
        }
      }
    }

    $durationMs = (int) ((\hrtime(TRUE) - $startTime) / 1_000_000);

    $this->costTracker->recordFailure(
      provider: $provider,
      model: $prompt['model'],
      promptName: $promptName,
      status: 'error',
      errorMessage: $lastException?->getMessage(),
      userId: $userId,
    );

    $this->recordFailure($provider);

    throw new \RuntimeException(
      \sprintf('AI call failed after %d retries: %s', self::MAX_RETRIES, $lastException?->getMessage() ?? 'Unknown error'),
      previous: $lastException,
    );
  }

  /**
   * Checks if the circuit breaker is open for a provider.
   */
  public function isCircuitOpen(string $provider): bool {
    $openKey = 'ai_circuit_open:' . $provider;
    $openUntil = $this->cache->get($openKey);

    if ($openUntil !== FALSE && $openUntil->data !== NULL) {
      if ($openUntil->data > $this->time->getCurrentTime()) {
        return TRUE;
      }
      $this->cache->delete($openKey);
    }

    return FALSE;
  }

  /**
   * Returns the current circuit state for inspection.
   */
  public function getCircuitState(string $provider): array {
    $failuresKey = 'ai_circuit_failures:' . $provider;
    $openKey = 'ai_circuit_open:' . $provider;

    $failures = [];
    $cached = $this->cache->get($failuresKey);
    if ($cached !== FALSE && $cached->data !== NULL) {
      $failures = $cached->data;
    }

    $openUntil = NULL;
    $cached = $this->cache->get($openKey);
    if ($cached !== FALSE && $cached->data !== NULL) {
      $openUntil = $cached->data;
    }

    return [
      'provider' => $provider,
      'failure_count' => \count($failures),
      'circuit_open' => $openUntil !== NULL && $openUntil > $this->time->getCurrentTime(),
      'open_until' => $openUntil,
    ];
  }

  /**
   * Manually resets the circuit breaker for a provider.
   */
  public function resetCircuit(string $provider): void {
    $this->cache->delete('ai_circuit_failures:' . $provider);
    $this->cache->delete('ai_circuit_open:' . $provider);
  }

  private function calculateCost(int $inputTokens, int $outputTokens, float $inputCostPer1k, float $outputCostPer1k): float {
    return ($inputTokens / 1000 * $inputCostPer1k) + ($outputTokens / 1000 * $outputCostPer1k);
  }

  private function recordSuccess(string $provider): void {
    $key = 'ai_circuit_failures:' . $provider;
    $this->cache->delete($key);
  }

  private function recordFailure(string $provider): void {
    $key = 'ai_circuit_failures:' . $provider;
    $now = $this->time->getCurrentTime();

    $cached = $this->cache->get($key);
    $failures = $cached !== FALSE && $cached->data !== NULL ? $cached->data : [];

    $failures[] = $now;

    $failures = \array_filter($failures, fn(int $t): bool => ($now - $t) <= self::CIRCUIT_WINDOW_SECONDS);

    $this->cache->set($key, $failures);

    if (\count($failures) >= self::CIRCUIT_FAILURE_THRESHOLD) {
      $openKey = 'ai_circuit_open:' . $provider;
      $openUntil = $now + self::CIRCUIT_OPEN_SECONDS;
      $this->cache->set($openKey, $openUntil);
    }
  }

}
