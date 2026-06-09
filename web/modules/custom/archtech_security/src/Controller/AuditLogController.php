<?php

declare(strict_types=1);

namespace Drupal\archtech_security\Controller;

use Drupal\archtech_core_api\Api\ErrorResponse;
use Drupal\archtech_core_api\Api\Paginator;
use Drupal\archtech_core_api\Controller\ApiControllerBase;
use Drupal\archtech_security\Service\AuditLogger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for the immutable audit log retrieval API.
 */
final class AuditLogController extends ApiControllerBase {

  public function __construct(
    Paginator $paginator,
    ErrorResponse $errorResponse,
    private readonly AuditLogger $auditLogger,
  ) {
    parent::__construct($paginator, $errorResponse);
  }

  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('archtech_core_api.paginator'),
      $container->get('archtech_core_api.error_response'),
      $container->get('archtech_security.audit_logger'),
    );
  }

  public function list(Request $request): JsonResponse {
    $pagination = $this->paginate($request);
    $params = $pagination['params'];
    $cursor = $this->safeCursorDecode($params['cursor']);

    $offset = $cursor !== NULL ? $cursor[2] : 0;

    $entries = $this->auditLogger->query(
      limit: $params['limit'],
      offset: $offset,
    );
    $total = $this->auditLogger->count();

    $meta = $this->paginatedMeta($params, $total, \count($entries), 'id', $entries[\count($entries) - 1]->id ?? NULL);

    return $this->success($entries, $meta);
  }

  public function listByUser(int $user, Request $request): JsonResponse {
    $pagination = $this->paginate($request);
    $params = $pagination['params'];
    $cursor = $this->safeCursorDecode($params['cursor']);

    $offset = $cursor !== NULL ? $cursor[2] : 0;

    $entries = $this->auditLogger->query(
      userId: $user,
      limit: $params['limit'],
      offset: $offset,
    );
    $total = $this->auditLogger->count(userId: $user);

    $meta = $this->paginatedMeta($params, $total, \count($entries), 'id', $entries[\count($entries) - 1]->id ?? NULL);

    return $this->success($entries, $meta);
  }

}
