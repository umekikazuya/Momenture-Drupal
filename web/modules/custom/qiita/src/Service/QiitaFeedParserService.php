<?php

declare(strict_types=1);

namespace Drupal\qiita\Service;

use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Provides functionality to parse Qiita XML feeds.
 *
 * This service is responsible for parsing XML feed data retrieved from Qiita.
 * It converts the raw XML data into a structured array that can be easily
 * used within Drupal. This class extends the generic FeedParserServiceBase
 * to implement Qiita-specific parsing logic.
 */
final class QiitaFeedParserService extends FeedParserServiceBase {

  /**
   * {@inheritDoc}
   */
  public function parseXml(\SimpleXMLElement $xml): array {
    $articles = [];
    foreach ($xml->entry as $o) {
      // Get feed item.
      $title = (string) $o->title;
      $attributes = $o->link->attributes();
      $link = (string) $attributes['href'];
      $published = (string) $o->published;
      if (!isset($title) || !isset($link) || !isset($published)) {
        continue;
      }
      $published = new DrupalDateTime($published);

      $articles[] = [
        'title' => $title,
        'link' => $link,
        'published' => $published->format('c'),
      ];
    }
    return [
      'title' => (string) $xml->title,
      'link' => (string) current($xml->link[2] ?? []),
      'data' => $articles,
    ];
  }

}
