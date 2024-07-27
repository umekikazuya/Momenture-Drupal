<?php

declare(strict_types=1);

namespace Drupal\feed_api\Service;

use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Provides functionality to parse Zenn XML feeds.
 *
 * This service is responsible for parsing XML feed data retrieved from Zenn.
 * It converts the raw XML data into a structured array that can be easily
 * used within Drupal. This class extends the generic FeedParserServiceBase
 * to implement Zenn-specific parsing logic.
 */
final class ZennFeedParserService extends FeedParserServiceBase {

  /**
   * {@inheritDoc}
   */
  public function parseXml(\SimpleXMLElement $xml): array {
    $articles = [];
    foreach ($xml->channel->item as $o) {
      // Get feed item.
      $title = (string) $o->title;
      $link = (string) $o->link;
      $published = (string) $o->published;
      if (!isset($title) || !isset($link) || !isset($published)) {
        continue;
      }
      $published = new DrupalDateTime($published);

      $articles[] = [
        'title' => $title,
        'link' => $link,
        'published' => $published->format(DATE_ISO8601_EXPANDED),
      ];
    }
    return [
      'title' => (string) $xml->channel->image->title,
      'link' => (string) $xml->channel->image->link,
      'data' => $articles,
    ];
  }

}
