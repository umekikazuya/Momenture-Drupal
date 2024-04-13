<?php

/**
 * @file
 * Create repeat task.
 */

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\eck\EckEntityInterface;
use Drush\Drush;

/**
 * Repeat task.
 */
function repeat_task() {
  // Log.
  $logger = Drush::logger('repeat_task');

  // Table info.
  $entity_type = 'task';
  $bundle_type_repeat = 'repeat';
  $field_name_status = 'field_is_enabled';
  $bundle_type_task = 'task';

  // Entity Storage.
  $entity_storage = \Drupal::entityTypeManager()->getStorage($entity_type);

  // Conditon.
  $condition_repeat = [
    $entity_storage->getEntityType()->getKey('bundle') => $bundle_type_repeat,
    $field_name_status => 1,
  ];
  $condition_create = [
    $entity_storage->getEntityType()->getKey('bundle') => $bundle_type_task,
    'field_name' => NULL,
  ];

  // Get entity.
  $target_entities = $entity_storage->loadByProperties($condition_repeat);

  foreach ($target_entities as $repeat_task) {
    if ($repeat_task instanceof EckEntityInterface
      && $repeat_task->hasField('field_name')
      && $repeat_task->hasField('field_ref_project')
      && $repeat_task->hasField('uid')
    ) {
      $condition_create['field_name'] = $repeat_task->get('field_name')?->value;
      $condition_create['field_ref_project'] = $repeat_task->get('field_ref_project')?->getString() ?? NULL;
      $condition_create['uid'] = $repeat_task->get('uid')?->getString() ?? NULL;

      // Get today.
      $drupal_date_time = new DrupalDateTime();
      $condition_create['field_start_date'] = $drupal_date_time?->format('Y-m-d') ?? NULL;
      // Create.
      try {
        $new_entity = $entity_storage->create($condition_create);
        $new_entity->save();
        $logger->notice(t('Success create new task(Id:@id)!!', [
          '@id' => $new_entity->id(),
        ]));
      }
      catch (EntityStorageException $e) {
        $logger->error($e->getMessage());
      }
    }
  }
}

repeat_task();
