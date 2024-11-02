<?php

declare(strict_types=1);

namespace Drupal\app\Controller\Account;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\UserInterface;

/**
 * アカウント - 基本設定.
 */
final class AccountSettingsController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function __invoke(): array {
    $user = $this->entityTypeManager()->getStorage('user')->load($this->currentUser()->id());
    if ($user instanceof UserInterface) {
      return $this->entityFormBuilder()->getForm($user, 'settings');
    }

    return [];
  }

}
