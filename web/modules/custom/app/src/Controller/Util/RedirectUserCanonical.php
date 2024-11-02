<?php

namespace Drupal\app\Controller\Util;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\user\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * UserCanonicalへの制御.
 */
class RedirectUserCanonical extends ControllerBase {

  /**
   * Redirect.
   *
   * @param \Drupal\user\UserInterface $user
   *   Node Object.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   The redirect response to the node edit page.
   */
  public function redirectToEdit(UserInterface $user) {
    $url = Url::fromRoute(
      'entity.user.edit_form',
      ['user' => $user->id()],
    )->toString();
    return new RedirectResponse($url);
  }

}
