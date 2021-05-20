<?php

namespace Drupal\Abyss\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Simple page controller for drupal.
 */
class PageController extends ControllerBase {

  /**
   * @return string[]
   *   Performs rendering to display the form, saved information,
   *   and pager to navigate pages with broken information.
   */
  public function page() {
    // Call and form rendering for connection.
    $form_class = '\Drupal\Abyss\Form\AbyssEditModalForm';
    $form = \Drupal::formBuilder()->getForm($form_class);
    $renderer = \Drupal::service('renderer')->render($form);

    // Data generation for return for Abyss Theme hook.
    $build['list'] = [
      '#theme' => 'description',
      '#form' => $renderer,
    ];

    return $build;
  }

}
