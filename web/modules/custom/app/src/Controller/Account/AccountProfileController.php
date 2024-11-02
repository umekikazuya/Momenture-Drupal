<?php

declare(strict_types=1);

namespace Drupal\app\Controller\Account;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\UserInterface;

/**
 * アカウント - プロフィール.
 */
final class AccountProfileController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function __invoke(): array {
    $user = $this->entityTypeManager()->getStorage('user')->load($this->currentUser()->id());
    if ($user instanceof UserInterface) {
      return $this->entityFormBuilder()->getForm($user, 'profile');
    }
    return [];
  }

}
