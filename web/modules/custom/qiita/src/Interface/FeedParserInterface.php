<?php

namespace Drupal\qiita\Service\Interface;

/**
 * Interface that provide to parse feed data.
 */
interface FeedParserInterface {

  /**
   * Parse an XML feed into a structured array.
   *
   * @param \SimpleXMLElement $xml
   *   The XML data to parse, typically retrieved from a feed source.
   *
   * @return array
   *   An associative array containing the parsed data.
   */
  public function parseXml(\SimpleXMLElement $xml): array;

}
