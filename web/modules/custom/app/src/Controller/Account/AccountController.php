<?php

declare(strict_types=1);

namespace Drupal\app\Controller\Account;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * アカウント.
 */
final class AccountController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function __invoke(): array {
    $content = [
      0 => [
        'title' => '共通設定',
        'description' => 'ユーザー名、Email、パスワードの設定',
        'url' => Url::fromRoute('app.account.settings'),
      ],
      1 => [
        'title' => 'プロフィール設定',
        'description' => '',
        'url' => Url::fromRoute('app.account.profile'),
      ],
    ];

    $build['content'] = [
      '#theme' => 'admin_block_content',
      '#content' => $content,
    ];
    return $build;
  }

}
