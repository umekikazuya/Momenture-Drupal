<?php

namespace Drupal\tools\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\tools\ToolsManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Tools controller.
 */
class ToolsController extends ControllerBase {

  /**
   * Tools Manager.
   */
  protected ToolsManager $toolsManager;

  /**
   * Constructs a new Controller.
   *
   * @param \Drupal\tools\ToolsManager $tools_manager
   *   Tools manager service.
   */
  public function __construct(ToolsManager $tools_manager) {
    $this->toolsManager = $tools_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('tools.manager'),
    );
  }

  /**
   * Builds the response.
   */
  public function build() {
    return $this->toolsManager->buildDashboard();
  }

}
