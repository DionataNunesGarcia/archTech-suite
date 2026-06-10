<?php

declare(strict_types=1);

namespace Drupal\archtech_feature_flags\Twig;

use Drupal\archtech_feature_flags\Service\FeatureFlagService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * Twig extension exposing feature flag checks in templates.
 *
 * Usage in Twig:
 *   {% if feature_flag('new_dashboard') %}
 *     <div>New dashboard UI</div>
 *   {% endif %}
 *
 *   {% if feature_flag('beta_search', current_user_id) %}
 *     <search-beta />
 *   {% endif %}
 *
 * Test form:
 *   {% if 'beta_search' is feature_enabled %}
 *     ...
 *   {% endif %}
 */
final class FeatureFlagExtension extends AbstractExtension {

  public function __construct(
    private readonly FeatureFlagService $featureFlagService,
  ) {}

  public function getFunctions(): array {
    return [
      new TwigFunction(
        'feature_flag',
        $this->checkFlag(...),
        ['is_safe' => ['html']],
      ),
    ];
  }

  public function getTests(): array {
    return [
      new TwigTest(
        'feature_enabled',
        $this->checkFlagTest(...),
      ),
    ];
  }

  /**
   * Twig function: feature_flag(name, userId=null) -> bool.
   */
  public function checkFlag(string $name, ?int $userId = NULL): bool {
    return $this->featureFlagService->isEnabled($name, $userId);
  }

  /**
   * Twig test: 'flag_name' is feature_enabled.
   */
  public function checkFlagTest(string $name): bool {
    return $this->featureFlagService->isEnabled($name);
  }

}
