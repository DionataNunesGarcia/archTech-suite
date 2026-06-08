<?php

namespace Drupal\archtech_health\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

class HealthController extends ControllerBase {

  public function check(): JsonResponse {
    $db_status = 'ok';
    try {
      \Drupal::database()->query('SELECT 1');
    }
    catch (\Exception $e) {
      $db_status = 'error';
    }

    return new JsonResponse([
      'status' => $db_status,
      'database' => $db_status,
      'timestamp' => time(),
    ], $db_status === 'ok' ? 200 : 500);
  }

  public function api(): JsonResponse {
    return new JsonResponse([
      'status' => 'ok',
      'version' => \Drupal::VERSION,
      'modules' => array_keys(\Drupal::moduleHandler()->getModuleList()),
      'timestamp' => time(),
    ]);
  }

}
