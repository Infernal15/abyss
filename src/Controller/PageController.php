<?php

namespace Drupal\abyss\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Simple page controller for drupal.
 */
class PageController extends ControllerBase {

  /**
   * Used to save the RendererInterface object.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected RendererInterface $renderer;

  /**
   * Used to store a FormBuilderInterface object.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected FormBuilderInterface $builder;

  /**
   * Constructor for NewsSendController.
   */
  public function __construct(RendererInterface $renderer, FormBuilderInterface $builder) {
    $this->renderer = $renderer;
    $this->builder = $builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): PageController {
    $instance = new static($container->get('renderer'), $container->get('form_builder'));
    return $instance;
  }

  /**
   * Used to display the table form on a page.
   *
   * @return string[]
   *   Performs rendering to display the form, saved information,
   *   and pager to navigate pages with broken information.
   */
  public function page() {
    // Call and form rendering for connection.
    $form_class = '\Drupal\abyss\Form\AbyssEditModalForm';
    $form = $this->builder->getForm($form_class);
    $renderer = $this->renderer->render($form);

    // Data generation for return for abyss Theme hook.
    $build['list'] = [
      '#theme' => 'description',
      '#form' => $renderer,
    ];

    return $build;
  }

}
