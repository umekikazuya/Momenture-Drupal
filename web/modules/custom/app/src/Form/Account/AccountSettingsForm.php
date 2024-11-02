<?php

namespace Drupal\app\Form\Account;

use Drupal\Core\Form\FormStateInterface;
use Drupal\user\ProfileForm;
use Drupal\user\UserInterface;

/**
 * アカウント - 共通設定.
 */
class AccountSettingsForm extends ProfileForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?UserInterface $user = NULL) {
    $form = parent::buildForm($form, $form_state);

    // フォームモードを設定.
    $form_state->set('form_mode', 'user.settings');

    // 親クラスの buildForm を呼び出す.
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $this->messenger()->addMessage('アカウント情報を保存しました。');
  }

}
