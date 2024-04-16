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

  // Today.
  $today = DrupalDateTime::createFromFormat(DateTimeItemInterface::DATE_STORAGE_FORMAT, 'now');

  // Fetch status entity exclude close.
  $status_ids = fetch_open_status_entities('field_is_enabled');

  // Fetch & Load task entities.
  $entities = fetch_task_entities($today, $status_ids);

  // Render markup array.
  $data = [];
  foreach ($entities as $id => $entity) {
    if ($entity instanceof EckEntityInterface) {
      $project_name = get_project_name($entity);
      $data[$project_name][$id] = [
        'name' => get_field_value($entity, 'field_name', 'No Label.'),
        'start_date' => get_field_value($entity, 'field_start_date', '開始日設定なし'),
      ];
    }
  }

  $message = "";
  foreach ($data as $i => $o) {
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

/**
 * Fetch Status entities.
 *
 * @param string $field_name_enable
 *   Field name in valid state.
 *
 * @return int|array
 *   Entity ids.
 */
function fetch_open_status_entities(string $field_name_enable): int|array {
  return \Drupal::entityQuery('status')
    ->accessCheck(FALSE)
    ->condition('type', 'status')
    ->condition($field_name_enable, 1)
    ->execute();
}

/**
 * Fetch Task entities.
 */
function fetch_task_entities($today, $status_ids): array {
  $entity_storage = \Drupal::entityTypeManager()->getStorage('task');
  $entity_query = $entity_storage
    ->getQuery()
    ->accessCheck(FALSE)
    ->condition($entity_storage->getEntityType()->getKey('bundle'), 'task')
    ->condition('field_start_date', $today, '<=')
    ->condition('field_ref_status', $status_ids, 'IN');
  return $entity_storage->loadMultiple($entity_query->execute());
}

/**
 * Get project name.
 */
function get_project_name($entity) {
  $project_entity = $entity->get('field_ref_project')?->entity;
  if ($project_entity instanceof EckEntityInterface && $project_entity->hasField('field_name')) {
    return $project_entity->get('field_name')?->getString() ?: 'No Project';
  }
  return 'No Project';
}

/**
 * Get field value.
 */
function get_field_value(EckEntityInterface $entity, $field_name, $default_value) {
  return $entity->hasField($field_name) ? $entity->get($field_name)?->getString() : $default_value;
}

notice_task();
