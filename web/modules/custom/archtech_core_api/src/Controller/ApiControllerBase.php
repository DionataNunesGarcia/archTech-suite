<?php

declare(strict_types=1);

namespace Drupal\archtech_core_api\Controller;

use Drupal\archtech_core_api\Api\ErrorResponse;
use Drupal\archtech_core_api\Api\Paginator;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Abstract base controller providing pagination, filters, and standardized responses.
 *
 * Extend this controller for any JSON:API-compatible endpoint that needs
 * cursor-based pagination, declarative query filters, and consistent error handling.
 */
abstract class ApiControllerBase extends ControllerBase {

  /**
   * @param \Drupal\archtech_core_api\Api\Paginator $paginator
   * @param \Drupal\archtech_core_api\Api\ErrorResponse $errorResponse
   */
  public function __construct(
    protected readonly Paginator $paginator,
    protected readonly ErrorResponse $errorResponse,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('archtech_core_api.paginator'),
      $container->get('archtech_core_api.error_response'),
    );
  }

  /**
   * Builds a standardized success response.
   *
   * @param array $data
   *   The response data payload.
   * @param array|null $meta
   *   Metadata including cursor, total, per_page.
   * @param int $status
   *   HTTP status code.
   */
  protected function success(array $data, ?array $meta = NULL, int $status = 200): JsonResponse {
    $response = ['data' => $data];
    if ($meta !== NULL) {
      $response['meta'] = $meta;
    }
    return new JsonResponse($response, $status);
  }

  /**
   * Parses pagination parameters and returns a configured Paginator + meta base.
   *
   * @return array{paginator: \Drupal\archtech_core_api\Api\Paginator, params: array}
   */
  protected function paginate(Request $request): array {
    $params = $this->paginator->parseParams($request);
    return [
      'paginator' => $this->paginator,
      'params' => $params,
    ];
  }

  /**
   * Builds paginated collection meta from query results.
   *
   * @param array $params
   *   From paginate() -> ['params'].
   * @param int $total
   *   Total items in the collection.
   * @param int $returnedCount
   *   Number of items in this response.
   * @param string $sortKey
   *   The sort field used for cursor.
   * @param string|int|null $lastValue
   *   The sort field value of the last item.
   */
  protected function paginatedMeta(array $params, int $total, int $returnedCount, string $sortKey, string|int|null $lastValue): array {
    $nextCursor = $lastValue !== null
      ? $this->paginator->nextCursor($sortKey, $lastValue, $returnedCount, $total)
      : NULL;

    return $this->paginator->buildMeta($total, $params['per_page'], $nextCursor);
  }

  /**
   * Applies declarative filter rules to a query.
   *
   * Declared filters map query parameter names to allowed operators:
   *
   *   $allowedFilters = [
   *     'status' => ['eq', 'in'],
   *     'created_before' => ['lt'],
   *     'search' => ['like'],
   *   ];
   *
   * Query params are automatically parsed: `?filter[status]=active&filter[search]=term`.
   *
   * @param \Drupal\Core\Database\Query\SelectInterface $query
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param array<string, string[]> $allowedFilters
   *   Map of field => allowed operators.
   *
   * @return array<string, mixed>
   *   Parsed filter values for downstream use.
   */
  protected function applyFilters(\Drupal\Core\Database\Query\SelectInterface $query, Request $request, array $allowedFilters): array {
    $filters = $request->query->all('filter');

    foreach ($filters as $field => $value) {
      if (!isset($allowedFilters[$field])) {
        continue;
      }

      $allowed = $allowedFilters[$field];

      if (\in_array('eq', $allowed, TRUE)) {
        $query->condition($field, $value);
      }
      elseif (\in_array('like', $allowed, TRUE)) {
        $query->condition($field, '%' . $query->escapeLike($value) . '%', 'LIKE');
      }
      elseif (\in_array('lt', $allowed, TRUE)) {
        $query->condition($field, $value, '<');
      }
      elseif (\in_array('gt', $allowed, TRUE)) {
        $query->condition($field, $value, '>');
      }
      elseif (\in_array('lte', $allowed, TRUE)) {
        $query->condition($field, $value, '<=');
      }
      elseif (\in_array('gte', $allowed, TRUE)) {
        $query->condition($field, $value, '>=');
      }
      elseif (\in_array('in', $allowed, TRUE)) {
        $values = \is_array($value) ? $value : \explode(',', (string) $value);
        $query->condition($field, $values, 'IN');
      }
    }

    return $filters;
  }

  /**
   * Decodes a cursor string safely, returning defaults on failure.
   *
   * @return array{0: string, 1: string|int, 2: int}|null
   */
  protected function safeCursorDecode(?string $cursor): ?array {
    if ($cursor === NULL || $cursor === '') {
      return NULL;
    }
    try {
      return $this->paginator->decode($cursor);
    } catch (\InvalidArgumentException) {
      return NULL;
    }
  }

}
