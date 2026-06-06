<?php

namespace Drupal\MODULENAME\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for MODULELABEL routes.
 */
class ExampleController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function content() {
    $build['content'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Example content from MODULELABEL module.'),
    ];

    return $build;
  }

}
