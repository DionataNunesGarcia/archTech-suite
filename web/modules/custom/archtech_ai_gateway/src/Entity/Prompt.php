<?php

declare(strict_types=1);

namespace Drupal\archtech_ai_gateway\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * @ConfigEntityType(
 *   id = "archtech_prompt",
 *   label = @Translation("AI Prompt"),
 *   label_collection = @Translation("AI Prompts"),
 *   label_singular = @Translation("AI Prompt"),
 *   label_plural = @Translation("AI Prompts"),
 *   label_count = @PluralTranslation(
 *     singular = "@count AI prompt",
 *     plural = "@count AI prompts",
 *   ),
 *   config_prefix = "archtech_prompt",
 *   admin_permission = "administer ai prompts",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "status" = "status",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "provider",
 *     "model",
 *     "squad",
 *     "system_prompt",
 *     "user_prompt_template",
 *     "temperature",
 *     "max_tokens",
 *     "input_cost_per_1k",
 *     "output_cost_per_1k",
 *     "cache_ttl",
 *   },
 * )
 */
final class Prompt extends ConfigEntityBase {

  protected string $id;

  protected string $label;

  protected string $provider = 'openai';

  protected string $model = 'gpt-4o-mini';

  protected string $squad = 'platform';

  protected string $system_prompt = '';

  protected string $user_prompt_template = '';

  protected float $temperature = 0.7;

  protected int $max_tokens = 4096;

  protected float $input_cost_per_1k = 0.0;

  protected float $output_cost_per_1k = 0.0;

  protected int $cache_ttl = 0;

  public function getProvider(): string {
    return $this->provider;
  }

  public function getModel(): string {
    return $this->model;
  }

  public function getSquad(): string {
    return $this->squad;
  }

  public function getSystemPrompt(): string {
    return $this->system_prompt;
  }

  public function getUserPromptTemplate(): string {
    return $this->user_prompt_template;
  }

  public function getTemperature(): float {
    return $this->temperature;
  }

  public function getMaxTokens(): int {
    return $this->max_tokens;
  }

  public function getInputCostPer1k(): float {
    return $this->input_cost_per_1k;
  }

  public function getOutputCostPer1k(): float {
    return $this->output_cost_per_1k;
  }

  public function getCacheTtl(): int {
    return $this->cache_ttl;
  }

}
