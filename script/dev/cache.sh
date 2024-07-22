#!/bin/bash
# スクリプトの目的: Cacheのパージ

# Exec `drupal_flush_all_caches`.
if drush php:eval "drupal_flush_all_caches();"; then
  echo "$(date) - Cache flush successful"
else
  echo "$(date) - Cache flush failed"
fi

# Exec `drush cache:rebuild`.
if drush cache:rebuild; then
  echo "$(date) - Cache Rebuild successful"
else
  echo "$(date) - Cache Rebuild failed"
fi
