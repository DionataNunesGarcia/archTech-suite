<?php

declare(strict_types=1);

namespace Drupal\archtech_feature_flags\Service;

use Drupal\Core\Session\AccountProxyInterface;

/**
 * Feature flag service backed by Redis.
 *
 * Keys are stored with the prefix `ff:`.
 * Per-user overrides use the pattern: `ff:{flag_name}:user:{uid}`.
 * Global flags use: `ff:{flag_name}`.
 */
final class FeatureFlagService {

  private const string KEY_ENABLED = '1';
  private const string KEY_DISABLED = '0';

  public function __construct(
    private readonly RedisClient $redis,
    private AccountProxyInterface $currentUser,
  ) {}

  /**
   * Checks if a feature flag is enabled, optionally for a specific user.
   *
   * Resolution order:
   *  1. Per-user override (if userId provided or current user)
   *  2. Global flag value
   *  3. Default: FALSE
   */
  public function isEnabled(string $name, ?int $userId = NULL): bool {
    $userId ??= (int) $this->currentUser()->id();

    if ($userId > 0) {
      $userOverride = $this->redis->get($this->userKey($name, $userId));
      if ($userOverride !== NULL) {
        return $userOverride === self::KEY_ENABLED;
      }
    }

    $globalValue = $this->redis->get($this->globalKey($name));
    return $globalValue === self::KEY_ENABLED;
  }

  /**
   * Enables a feature flag globally.
   */
  public function enable(string $name): bool {
    return $this->redis->set($this->globalKey($name), self::KEY_ENABLED);
  }

  /**
   * Disables a feature flag globally.
   */
  public function disable(string $name): bool {
    return $this->redis->set($this->globalKey($name), self::KEY_DISABLED);
  }

  /**
   * Gets the raw value of a feature flag.
   */
  public function get(string $name): ?string {
    return $this->redis->get($this->globalKey($name));
  }

  /**
   * Sets a feature flag to an arbitrary value.
   */
  public function set(string $name, string $value): bool {
    return $this->redis->set($this->globalKey($name), $value);
  }

  /**
   * Deletes a feature flag globally.
   */
  public function delete(string $name): int {
    return $this->redis->del($this->globalKey($name));
  }

  /**
   * Enables a feature flag for a specific user.
   */
  public function enableForUser(string $name, int $userId): bool {
    return $this->redis->set($this->userKey($name, $userId), self::KEY_ENABLED);
  }

  /**
   * Disables a feature flag for a specific user.
   */
  public function disableForUser(string $name, int $userId): bool {
    return $this->redis->set($this->userKey($name, $userId), self::KEY_DISABLED);
  }

  /**
   * Clears a per-user override.
   */
  public function clearUserOverride(string $name, int $userId): int {
    return $this->redis->del($this->userKey($name, $userId));
  }

  /**
   * Lists all globally defined feature flags.
   *
   * @return array<string, string>
   */
  public function listAll(): array {
    $keys = $this->redis->keys($this->redis->getPrefix() . '*') ?? [];
    $result = [];

    foreach ($keys as $fullKey) {
      $flagName = \str_replace($this->redis->getPrefix(), '', $fullKey);
      $value = $this->redis->get($fullKey);
      if ($value !== NULL) {
        $result[$flagName] = $value;
      }
    }

    \ksort($result);
    return $result;
  }

  /**
   * Builds the global key for a flag.
   */
  private function globalKey(string $name): string {
    return $this->redis->getPrefix() . $name;
  }

  /**
   * Builds the per-user key for a flag.
   */
  private function userKey(string $name, int $userId): string {
    return $this->redis->getPrefix() . $name . ':user:' . $userId;
  }

}
