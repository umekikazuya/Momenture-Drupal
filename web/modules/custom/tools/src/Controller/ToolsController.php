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
  public function index() {
    return $this->toolsManager->buildIndex();
  }

  /**
   * Redirects users to their profile page.
   *
   * This controller assumes that it is only invoked for authenticated users.
   * This is enforced for the 'user.page' route with the '_user_is_logged_in'
   * requirement.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Returns a redirect to the profile of the currently logged in user.
   */
  public function profileSettings() {
    return $this->redirect('entity.user.edit_form', [
      'user' => $this->currentUser()->id(),
      'display' => 'default',
    ]);
  }

}
