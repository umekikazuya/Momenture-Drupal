<?php

namespace Drupal\qiita\Service;

use Drupal\qiita\Service\Interface\FeedParserInterface;

/**
 * Abstract base class for feed parser services.
 *
 * Provides a common foundation for all feed parser implementations.
 * Defines structure and method that all derived classes must implement.
 * Ensures adherence to the FeedParserInterface with implementation of
 * the parseXml method.
 */
abstract class FeedParserServiceBase implements FeedParserInterface {

  /**
   * {@inheritDoc}
   */
  abstract public function parseXml(\SimpleXMLElement $xml): array;

}
