<?php

/**
 * @file
 * Create repeat task.
 */

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Site\Settings;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\eck\EckEntityInterface;
use Drush\Drush;
use GuzzleHttp\Client;

/**
 * Repeat task.
 */
function notice_task() {
  // Log.
  $logger = Drush::logger('notice_task');

  // Table info.
  $entity_type = 'task';
  $bundle_type_task = 'task';

  // Entity Storage.
  $entity_storage = \Drupal::entityTypeManager()->getStorage($entity_type);

  // Today.
  $drupal_date_time = new DrupalDateTime();
  $today = $drupal_date_time->format(DateTimeItemInterface::DATE_STORAGE_FORMAT);

  // Status Entityの条件。Custom Statusで無効状態（Close）のタスクは除外。.
  $status_ids = \Drupal::entityQuery('status')
    ->accessCheck(FALSE)
    ->condition('type', 'status')
    ->condition('field_is_enabled', 1)
    ->execute();

  // Conditon.
  // Open かつ 「開始日が今日以前（今日を含む）」の条件のタスク。
  // Get entity.
  $entity_ids = $entity_storage
    ->getQuery()
    ->accessCheck(FALSE)
    ->condition($entity_storage->getEntityType()->getKey('bundle'), $bundle_type_task)
    ->condition('field_start_date', $today, '<=')
    ->condition('field_ref_status', $status_ids, 'IN')
    ->execute();

  // Render markup array.
  $render = [];
  foreach ($entity_storage->loadMultiple($entity_ids) as $id => $entity) {
    if ($entity instanceof EckEntityInterface) {
      // Get task's project info.
      $project_name = 'No Project';
      $project_entity = $entity->get('field_ref_project')?->entity ?? FALSE;
      if ($project_entity instanceof EckEntityInterface
        && $project_entity->hasField('field_name')
      ) {
        $project_name = $project_entity->get('field_name')?->getString();
      }

      // Render markup array.
      $render[$project_name][$id] = [];
      $render[$project_name][$id]['name'] = $entity->hasField('field_name') ? $entity->get('field_name')?->getString() : 'No Label.';
      $render[$project_name][$id]['start_date'] = $entity->hasField('field_start_date') ? $entity->get('field_start_date')?->getString() : '開始日設定なし';
    }
  }

  $message = "";
  foreach ($render as $i => $o) {
    $message .= "*$i*\n";

    foreach (array_values($o) as $item) {
      $name = $item['name'];
      $message .= "- $name\n";
    }
  }

  $token = Settings::get('slack_token');
  $channel_id = Settings::get('slack_channel_id');
  $client = new Client();
  // Send message to slack.
  $client->post('https://slack.com/api/chat.postMessage', [
    'headers' => [
      'Authorization' => "Bearer {$token}",
      'Content-Type' => 'application/json',
    ],
    'json' => [
      'channel' => $channel_id,
      'text' => $message,
    ],
  ]);
}

notice_task();
