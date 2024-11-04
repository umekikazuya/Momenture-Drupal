<?php

namespace Drupal\toolkit\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\toolkit\ToolkitManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Toolkit controller.
 */
class ToolkitController extends ControllerBase {

  /**
   * Tools Manager.
   */
  protected ToolkitManager $toolkitManager;

  /**
   * Constructs a new Controller.
   *
   * @param \Drupal\toolkit\ToolsManager $toolkit_manager
   *   Tools manager service.
   */
  public function __construct(ToolkitManager $toolkit_manager) {
    $this->toolkitManager = $toolkit_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('toolkit.manager'),
    );
  }

  /**
   * Builds the response.
   */
  public function index() {
    return $this->toolkitManager->buildIndex();
  }

}
