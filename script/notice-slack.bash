#!/bin/bash
# スクリプトの目的: Drushを使用してPHPスクリプトを実行

# drushのパスを設定
DRUSH_PATH="/var/www/html/vendor/bin/drush"

# drushのインストール確認
if [ ! -x "$DRUSH_PATH" ]; then
  echo "Error: drush command not found. Please install it or check your PATH."
  exit 1
fi

# Exec.
cd /var/www/html
$DRUSH_PATH php:script /var/www/html/script/batch/notice_task.php
