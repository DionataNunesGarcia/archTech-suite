<?php

declare(strict_types=1);

namespace Drupal\archtech_ai_gateway\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Loads AI prompts from Drupal config entities.
 *
 * Prompts are stored as a custom config entity type 'prompt' with fields:
 *   - id: machine name
 *   - label: human-readable name
 *   - provider: AI provider
 *   - model: model name
 *   - system_prompt: system-level instructions
 *   - user_prompt_template: template with {{variable}} placeholders
 *   - temperature: float 0–2
 *   - max_tokens: int
 *   - input_cost_per_1k: float
 *   - output_cost_per_1k: float
 *   - cache_ttl: int seconds (0 = no cache)
 */
final class PromptRegistryService {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Loads a prompt configuration by its machine name.
   *
   * @return array{provider: string, model: string, system_prompt: string, user_prompt_template: string, temperature: float, max_tokens: int, input_cost_per_1k: float, output_cost_per_1k: float, cache_ttl: int}|null
   */
  public function loadPrompt(string $promptName): ?array {
    $storage = $this->entityTypeManager()->getStorage('prompt');
    /** @var \Drupal\Core\Config\Entity\ConfigEntityInterface|null $prompt */
    $prompt = $storage->load($promptName);

    if ($prompt === NULL) {
      return NULL;
    }

    return [
      'provider' => $prompt->get('provider') ?? 'openai',
      'model' => $prompt->get('model') ?? 'gpt-4o',
      'system_prompt' => $prompt->get('system_prompt') ?? '',
      'user_prompt_template' => $prompt->get('user_prompt_template') ?? '',
      'temperature' => (float) ($prompt->get('temperature') ?? 0.7),
      'max_tokens' => (int) ($prompt->get('max_tokens') ?? 4096),
      'input_cost_per_1k' => (float) ($prompt->get('input_cost_per_1k') ?? 0.0),
      'output_cost_per_1k' => (float) ($prompt->get('output_cost_per_1k') ?? 0.0),
      'cache_ttl' => (int) ($prompt->get('cache_ttl') ?? 0),
    ];
  }

  /**
   * Renders a prompt with variable substitution.
   *
   * @param string $template
   *   Template with {{variable}} placeholders.
   * @param array<string, string> $variables
   *
   * @return string
   */
  public function renderTemplate(string $template, array $variables): string {
    $replacements = [];
    foreach ($variables as $key => $value) {
      $replacements['{{' . $key . '}}'] = $value;
    }
    return \strtr($template, $replacements);
  }

  /**
   * Returns all available prompt names.
   *
   * @return string[]
   */
  public function listPromptNames(): array {
    $storage = $this->entityTypeManager()->getStorage('prompt');
    $ids = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('status', TRUE)
      ->execute();

    return \array_values($ids);
  }

}
