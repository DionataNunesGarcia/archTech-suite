<?php

declare(strict_types=1);

namespace Drupal\archtech_ai_gateway\Controller;

use Drupal\archtech_ai_gateway\Service\AiGatewayService;
use Drupal\archtech_ai_gateway\Service\ContentModerationService;
use Drupal\archtech_ai_gateway\Service\CostTrackerService;
use Drupal\archtech_ai_gateway\Service\PiiMaskerService;
use Drupal\archtech_ai_gateway\Service\PromptRegistryService;
use Drupal\archtech_core_api\Api\ErrorResponse;
use Drupal\archtech_core_api\Api\Paginator;
use Drupal\archtech_core_api\Controller\ApiControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for AI gateway REST endpoints.
 */
final class AiGatewayController extends ApiControllerBase {

  /**
   * @param \Drupal\archtech_ai_gateway\Service\AiGatewayService $aiGateway
   * @param \Drupal\archtech_ai_gateway\Service\PromptRegistryService $promptRegistry
   * @param \Drupal\archtech_ai_gateway\Service\CostTrackerService $costTracker
   * @param \Drupal\archtech_ai_gateway\Service\PiiMaskerService $piiMasker
   * @param \Drupal\archtech_ai_gateway\Service\ContentModerationService $contentModeration
   */
  public function __construct(
    Paginator $paginator,
    ErrorResponse $errorResponse,
    private readonly AiGatewayService $aiGateway,
    private readonly PromptRegistryService $promptRegistry,
    private readonly CostTrackerService $costTracker,
    private readonly PiiMaskerService $piiMasker,
    private readonly ContentModerationService $contentModeration,
  ) {
    parent::__construct($paginator, $errorResponse);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('archtech_core_api.paginator'),
      $container->get('archtech_core_api.error_response'),
      $container->get('archtech_ai_gateway.ai_gateway'),
      $container->get('archtech_ai_gateway.prompt_registry'),
      $container->get('archtech_ai_gateway.cost_tracker'),
      $container->get('archtech_ai_gateway.pii_masker'),
      $container->get('archtech_ai_gateway.content_moderation'),
    );
  }

  /**
   * Executes an AI prompt through the gateway.
   *
   * POST /api/v1/ai/execute
   * Body: { "prompt_name": "ia-crm/lead-scorer", "variables": { "lead_nome": "...", ... } }
   */
  public function execute(Request $request): JsonResponse {
    $body = \json_decode($request->getContent(), TRUE);

    if (!\is_array($body) || !isset($body['prompt_name'])) {
      return $this->errorResponse->single('Missing required field: prompt_name', 400, missing_field: 'prompt_name');
    }

    $promptName = (string) $body['prompt_name'];
    $variables = \is_array($body['variables'] ?? NULL) ? $body['variables'] : [];

    foreach ($variables as $key => $value) {
      if (\is_string($value)) {
        $masked = $this->piiMasker->maskPii($value);
        $variables[$key] = $masked['clean_text'];
      }
    }

    try {
      $apiCall = function (array $prompt) use ($variables) {
        $httpClient = \Drupal::httpClient();
        $renderedPrompt = $this->promptRegistry->renderTemplate(
          $prompt['user_prompt_template'],
          $variables,
        );

        $messages = [];
        if ($prompt['system_prompt'] !== '' && $prompt['system_prompt'] !== '0') {
          $messages[] = ['role' => 'system', 'content' => $prompt['system_prompt']];
        }
        $messages[] = ['role' => 'user', 'content' => $renderedPrompt];

        $apiKey = \getenv('OPENAI_API_KEY') ?: '';
        $response = $httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
          'headers' => [
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
          ],
          'json' => [
            'model' => $prompt['model'],
            'messages' => $messages,
            'temperature' => $prompt['temperature'],
            'max_tokens' => $prompt['max_tokens'],
          ],
          'timeout' => 60,
        ]);

        $result = \json_decode((string) $response->getBody(), TRUE);
        $usage = $result['usage'] ?? [];
        $content = $result['choices'][0]['message']['content'] ?? '';

        $this->contentModeration->moderate($content);

        return [
          'content' => $content,
          'input_tokens' => $usage['prompt_tokens'] ?? 0,
          'output_tokens' => $usage['completion_tokens'] ?? 0,
        ];
      };

      $result = $this->aiGateway->execute($promptName, $variables, $apiCall);

      return $this->success([
        'prompt_name' => $promptName,
        'result' => $result,
      ]);
    } catch (\RuntimeException $e) {
      return $this->errorResponse->single($e->getMessage(), 503, 'ai_gateway_error', 'AI Gateway Error');
    }
  }

  /**
   * Lists all available prompts.
   *
   * GET /api/v1/ai/prompts
   */
  public function listPrompts(Request $request): JsonResponse {
    $promptNames = $this->promptRegistry->listPromptNames();

    $prompts = [];
    foreach ($promptNames as $name) {
      $prompt = $this->promptRegistry->loadPrompt($name);
      if ($prompt !== NULL) {
        // Do not expose the full system prompt and template in the list.
        $prompts[] = [
          'prompt_id' => $name,
          'provider' => $prompt['provider'],
          'model' => $prompt['model'],
          'temperature' => $prompt['temperature'],
          'max_tokens' => $prompt['max_tokens'],
          'cache_ttl' => $prompt['cache_ttl'],
        ];
      }
    }

    return $this->success($prompts, ['total' => \count($prompts)]);
  }

  /**
   * Returns cost report for a time window.
   *
   * GET /api/v1/ai/cost-report?from=2026-06-01&to=2026-06-09
   */
  public function costReport(Request $request): JsonResponse {
    $from = (string) ($request->query->get('from') ?? \gmdate('Y-m-d\TH:i:s.v\Z', \strtotime('-7 days')));
    $to = (string) ($request->query->get('to') ?? \gmdate('Y-m-d\TH:i:s.v\Z'));

    $byProvider = $this->costTracker->aggregateByProvider($from, $to);

    $totalCost = 0.0;
    foreach ($byProvider as $data) {
      $totalCost += $data['total_cost'];
    }

    return $this->success([
      'period' => ['from' => $from, 'to' => $to],
      'total_cost' => \round($totalCost, 6),
      'by_provider' => $byProvider,
    ]);
  }

  /**
   * Returns the circuit breaker state for a provider.
   *
   * GET /api/v1/ai/circuit-state/{provider}
   */
  public function circuitState(string $provider): JsonResponse {
    $state = $this->aiGateway->getCircuitState($provider);
    return $this->success($state);
  }

  /**
   * Resets the circuit breaker for a provider.
   *
   * POST /api/v1/ai/circuit-state/{provider}/reset
   */
  public function resetCircuit(string $provider): JsonResponse {
    $this->aiGateway->resetCircuit($provider);
    return $this->success([
      'provider' => $provider,
      'message' => 'Circuit breaker reset successfully.',
    ]);
  }

}
