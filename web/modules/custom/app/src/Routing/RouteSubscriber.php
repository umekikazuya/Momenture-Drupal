<?php

namespace Drupal\app\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {

    // UserEntityの`canonical`ルートを変更.
    if ($route = $collection->get('entity.user.canonical')) {
      $route->setPath('/account');
      $route->setDefault('_controller', '\Drupal\app\Controller\Account\AccountController');
    }
  }

}
