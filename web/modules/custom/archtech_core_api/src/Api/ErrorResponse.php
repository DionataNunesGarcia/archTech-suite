<?php

declare(strict_types=1);

namespace Drupal\archtech_core_api\Api;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Builds standardized error responses in JSON:API-style error format.
 *
 * Each error object has: code, title, detail, source (optional).
 * The wrapper structure is: { "errors": [...] }
 */
final class ErrorResponse {

  /**
   * Builds a JSON response from an array of errors.
   *
   * @param array<int, array{code?: string, title?: string, detail: string, source?: array{pointer?: string, parameter?: string}}> $errors
   */
  public function build(array $errors, int $status = 400): JsonResponse {
    $normalized = [];
    foreach ($errors as $error) {
      $normalized[] = [
        'code' => $error['code'] ?? (string) $status,
        'title' => $error['title'] ?? $this->statusTitle($status),
        'detail' => $error['detail'],
        'source' => $error['source'] ?? NULL,
      ];
    }

    return new JsonResponse(['errors' => \array_filter($normalized)], $status);
  }

  /**
   * Builds a single-error response.
   */
  public function single(string $detail, int $status = 400, ?string $code = NULL, ?string $title = NULL): JsonResponse {
    return $this->build([[
      'code' => $code ?? (string) $status,
      'title' => $title,
      'detail' => $detail,
    ]], $status);
  }

  /**
   * Builds a 404 Not Found response.
   */
  public function notFound(string $entity = 'Resource'): JsonResponse {
    return $this->single(\sprintf('%s not found.', $entity), 404, 'not_found', 'Not Found');
  }

  /**
   * Builds a 403 Forbidden response.
   */
  public function forbidden(string $detail = 'Access denied.'): JsonResponse {
    return $this->single($detail, 403, 'forbidden', 'Forbidden');
  }

  /**
   * Builds a 401 Unauthorized response.
   */
  public function unauthorized(string $detail = 'Authentication required.'): JsonResponse {
    return $this->single($detail, 401, 'unauthorized', 'Unauthorized');
  }

  /**
   * Builds a 422 Unprocessable Entity validation error response.
   */
  public function validationError(string $field, string $detail): JsonResponse {
    return $this->build([[
      'code' => 'invalid_attribute',
      'title' => 'Invalid Attribute',
      'detail' => $detail,
      'source' => ['pointer' => '/data/attributes/' . $field],
    ]], 422);
  }

  /**
   * Builds a 500 Internal Server Error response.
   */
  public function serverError(string $detail = 'An internal error occurred.'): JsonResponse {
    return $this->single($detail, 500, 'server_error', 'Internal Server Error');
  }

  /**
   * Maps status code to human-readable title.
   */
  private function statusTitle(int $status): string {
    return match ($status) {
      400 => 'Bad Request',
      401 => 'Unauthorized',
      403 => 'Forbidden',
      404 => 'Not Found',
      409 => 'Conflict',
      422 => 'Unprocessable Entity',
      429 => 'Too Many Requests',
      500 => 'Internal Server Error',
      503 => 'Service Unavailable',
      default => 'Error',
    };
  }

}
