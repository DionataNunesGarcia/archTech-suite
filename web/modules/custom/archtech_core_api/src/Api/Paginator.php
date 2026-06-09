<?php

declare(strict_types=1);

namespace Drupal\archtech_core_api\Api;

/**
 * Provides cursor-based pagination for API endpoints.
 *
 * Cursor format: base64(sort_key|value|offset)
 * The cursor is opaque to clients; they simply pass it back as-is.
 */
final class Paginator {

  /**
   * Default number of items per page.
   */
  private const int DEFAULT_PER_PAGE = 25;

  /**
   * Maximum items per page.
   */
  private const int MAX_PER_PAGE = 100;

  /**
   * Private constructor — use named constructors.
   */
  private function __construct(
    private readonly string $cursorParam = 'cursor',
    private readonly string $limitParam = 'limit',
  ) {}

  /**
   * Creates a paginator from the current request query parameters.
   */
  public static function fromRequest(\Symfony\Component\HttpFoundation\Request $request): static {
    return new static();
  }

  /**
   * Decodes a cursor string into its components.
   *
   * @return array{0: string, 1: string|int, 2: int}
   */
  public function decode(string $cursor): array {
    $decoded = \base64_decode($cursor, TRUE);
    if ($decoded === FALSE) {
      throw new \InvalidArgumentException('Invalid cursor format.');
    }
    $parts = \explode('::', $decoded, 3);
    if (\count($parts) !== 3) {
      throw new \InvalidArgumentException('Invalid cursor specification.');
    }
    return [$parts[0], $parts[1], (int) $parts[2]];
  }

  /**
   * Encodes a sort key, value, and offset into an opaque cursor string.
   */
  public function encode(string $sortKey, string|int $lastValue, int $offset): string {
    return \base64_encode(\sprintf('%s::%s::%d', $sortKey, $lastValue, $offset));
  }

  /**
   * Parses pagination parameters from the request.
   *
   * @return array{cursor: ?string, limit: int, per_page: int}
   */
  public function parseParams(\Symfony\Component\HttpFoundation\Request $request): array {
    $limit = (int) $request->query->get($this->limitParam, self::DEFAULT_PER_PAGE);
    $limit = \max(1, \min($limit, self::MAX_PER_PAGE));

    return [
      'cursor' => $request->query->get($this->cursorParam),
      'limit' => $limit,
      'per_page' => $limit,
    ];
  }

  /**
   * Builds pagination metadata for the response.
   */
  public function buildMeta(int $total, int $perPage, ?string $nextCursor): array {
    return [
      'cursor' => $nextCursor,
      'total' => $total,
      'per_page' => $perPage,
    ];
  }

  /**
   * Generates the next cursor based on the last item's sort key and value.
   */
  public function nextCursor(string $sortKey, string|int $lastValue, int $currentCount, ?int $total): ?string {
    if ($total !== null && $currentCount >= $total) {
      return NULL;
    }
    return $this->encode($sortKey, $lastValue, $currentCount);
  }

}
