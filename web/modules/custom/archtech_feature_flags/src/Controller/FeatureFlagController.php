<?php

declare(strict_types=1);

namespace Drupal\archtech_feature_flags\Controller;

use Drupal\archtech_feature_flags\Service\FeatureFlagService;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * REST API controller for feature flag management.
 */
final class FeatureFlagController extends ControllerBase {

  public function __construct(
    private readonly FeatureFlagService $featureFlagService,
  ) {}

  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('archtech_feature_flags.feature_flag_service'),
    );
  }

  /**
   * List all feature flags.
   */
  public function list(): JsonResponse {
    return new JsonResponse([
      'data' => $this->featureFlagService->listAll(),
    ]);
  }

  /**
   * Get a single feature flag value.
   */
  public function get(string $name): JsonResponse {
    $value = $this->featureFlagService->get($name);

    if ($value === NULL) {
      return new JsonResponse([
        'errors' => [[
          'code' => 'not_found',
          'title' => 'Not Found',
          'detail' => \sprintf('Feature flag "%s" is not defined.', $name),
        ]],
      ], 404);
    }

    return new JsonResponse([
      'data' => [
        'name' => $name,
        'value' => $value,
        'enabled' => $this->featureFlagService->isEnabled($name),
      ],
    ]);
  }

  /**
   * Set (create/update) a feature flag.
   */
  public function set(string $name, Request $request): JsonResponse {
    $body = \json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (!isset($body['value'])) {
      return new JsonResponse([
        'errors' => [[
          'code' => 'invalid_request',
          'title' => 'Invalid Request',
          'detail' => 'The "value" field is required.',
        ]],
      ], 400);
    }

    $value = (string) $body['value'];
    $this->featureFlagService->set($name, $value);

    return new JsonResponse([
      'data' => [
        'name' => $name,
        'value' => $value,
      ],
    ], 200);
  }

  /**
   * Delete a feature flag.
   */
  public function delete(string $name): JsonResponse {
    $this->featureFlagService->delete($name);

    return new JsonResponse([
      'data' => ['deleted' => $name],
    ]);
  }

  /**
   * Set a per-user override for a feature flag.
   */
  public function setUserOverride(string $name, int $user, Request $request): JsonResponse {
    $body = \json_decode($request->getContent(), TRUE, 512, JSON_THROW_ON_ERROR);

    if (!isset($body['enabled'])) {
      return new JsonResponse([
        'errors' => [[
          'code' => 'invalid_request',
          'title' => 'Invalid Request',
          'detail' => 'The "enabled" field is required (boolean).',
        ]],
      ], 400);
    }

    if ((bool) $body['enabled']) {
      $this->featureFlagService->enableForUser($name, $user);
    } else {
      $this->featureFlagService->disableForUser($name, $user);
    }

    return new JsonResponse([
      'data' => [
        'name' => $name,
        'user_id' => $user,
        'enabled' => (bool) $body['enabled'],
      ],
    ]);
  }

}
