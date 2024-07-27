<?php

namespace Drupal\feed_api\Service;

use Drupal\feed_api\Interface\FeedParserInterface;

/**
 * Abstract base class for feed parser services.
 *
 * Provides a common foundation for all feed parser implementations.
 * Defines structure and method that all derived classes must implement.
 * Ensures adherence to the FeedParserInterface with implementation of
 * the parseXml method.
 */
abstract class GetFeedServiceBase implements FeedParserInterface {

  /**
   * {@inheritDoc}
   */
  public function loadRawFeedData(string $feed): \SimpleXMLElement|false {
    $data = simplexml_load_string($feed);
    if (assert($data instanceof \SimpleXMLElement)) {
      return $data;
    }
    return FALSE;
  }

  /**
   * {@inheritDoc}
   */
  abstract public function parseXml(\SimpleXMLElement $xml): array;

}
