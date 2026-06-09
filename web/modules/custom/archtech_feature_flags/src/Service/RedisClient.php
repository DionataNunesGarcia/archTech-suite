<?php

declare(strict_types=1);

namespace Drupal\archtech_feature_flags\Service;

/**
 * Thin wrapper around the PHP Redis extension for the feature flags module.
 *
 * Connection parameters are read from Drupal settings:
 *   $settings['archtech_feature_flags.redis'] = [
 *     'host'     => 'redis',
 *     'port'     => 6379,
 *     'password' => null,
 *     'database' => 0,
 *     'prefix'   => 'ff:',
 *   ];
 */
final class RedisClient {

  private ?\Redis $redis = NULL;
  private bool $connected = FALSE;

  /**
   * Returns the prefix for feature flag keys.
   */
  public function getPrefix(): string {
    return $this->getConfig('prefix', 'ff:');
  }

  /**
   * Gets a raw value from Redis.
   */
  public function get(string $key): ?string {
    $value = $this->client()->get($key);
    return $value !== FALSE ? $value : NULL;
  }

  /**
   * Sets a key-value pair in Redis.
   */
  public function set(string $key, string $value): bool {
    return $this->client()->set($key, $value);
  }

  /**
   * Deletes a key from Redis.
   */
  public function del(string ...$keys): int {
    return $this->client()->del(...$keys);
  }

  /**
   * Scans Redis for keys matching a pattern.
   *
   * @return string[]|null
   */
  public function keys(string $pattern): ?array {
    return $this->client()->keys($pattern);
  }

  /**
   * Sets a TTL on a key.
   */
  public function expire(string $key, int $ttlSeconds): bool {
    return $this->client()->expire($key, $ttlSeconds);
  }

  /**
   * Lazily connects to Redis.
   */
  private function client(): \Redis {
    if ($this->redis === NULL) {
      $this->redis = new \Redis();
    }

    if (!$this->connected) {
      $host = $this->getConfig('host', '127.0.0.1');
      $port = (int) $this->getConfig('port', '6379');
      $timeout = (float) $this->getConfig('timeout', '2.5');
      $password = $this->getConfig('password', '');
      $database = (int) $this->getConfig('database', '0');

      $this->redis->connect($host, $port, $timeout);

      if ($password !== '') {
        $this->redis->auth($password);
      }

      $this->redis->select($database);
      $this->connected = TRUE;
    }

    return $this->redis;
  }

  private function getConfig(string $key, string $default): string {
    $settings = \Drupal::settings()->get('archtech_feature_flags.redis', []);
    return isset($settings[$key]) ? (string) $settings[$key] : $default;
  }

  /**
   * Disconnects from Redis (called on destruct).
   */
  public function __destruct() {
    if ($this->redis !== NULL && $this->connected) {
      try {
        $this->redis->close();
      } catch (\Throwable) {
        // Already closed.
      }
    }
  }

}
