#!/bin/bash
# スクリプトの目的: Drushを使用してPHPスクリプトを実行

# drushのパスを動的に取得
DRUSH_PATH=$(which drush)

# drushのインストール確認
if [ -z "$DRUSH_PATH" ]; then
  echo "Error: drush command not found. Please install it or check your PATH."
  exit 1
fi

# Exec.
$DRUSH_PATH php:script script/batch/repeat_task.php
