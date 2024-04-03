<?php

namespace Drupal\tools\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Form\UserLoginForm;

/**
 * Override login form.
 */
final class ToolsUserLoginForm extends UserLoginForm {

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    if (empty($uid = $form_state->get('uid'))) {
      return;
    }
    $account = $this->userStorage->load($uid);

    // A destination was set, probably on an exception controller.
    if (!$this->getRequest()->request->has('destination')) {
      $form_state->setRedirect(
        '<front>'
      );
    }
    else {
      $this->getRequest()->query->set('destination', $this->getRequest()->request->get('destination'));
    }

    user_login_finalize($account);
  }

}
