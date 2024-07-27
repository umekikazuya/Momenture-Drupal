<?php

declare(strict_types=1);

namespace Drupal\feed_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\feed_api\Interface\FeedControllerInterface;
use Drupal\feed_api\Interface\FeedFetcherInterface;
use Drupal\feed_api\Interface\FeedParserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides a base controller for feed fetching and parsing.
 */
abstract class FeedControllerBase extends ControllerBase implements FeedControllerInterface {

  /**
   * Feed Fetcher.
   */
  protected FeedFetcherInterface $feedFetcher;

  /**
   * Feed Parser.
   */
  protected FeedParserInterface $feedParser;

  /**
   * CacheKey.
   */
  protected string $cacheKey;

  /**
   * Base URL for the feed.
   */
  protected string $baseUrl;

  /**
   * Constructor.
   *
   * @param \Drupal\feed_api\Service\Interface\FeedFetcherInterface $feed_fetcher
   *   Feed Fetcher.
   * @param \Drupal\feed_api\Service\Interface\FeedParserInterface $feed_parser
   *   Feed Parser.
   * @param string $base_url
   *   The base URL for the feed.
   */
  public function __construct(
    FeedFetcherInterface $feed_fetcher,
    FeedParserInterface $feed_parser,
    string $base_url,
  ) {
    $this->feedFetcher = $feed_fetcher;
    $this->feedParser = $feed_parser;
    $this->baseUrl = $base_url;
  }

  /**
   * Set cache-key.
   *
   * @param string $id
   *   Account ID used to generate the cache key.
   */
  protected function setCacheKey(string $id): void {
    $this->cacheKey = parse_url($this->buildFeedUrl($id), PHP_URL_HOST) . ':' . $id;
  }

  /**
   * Builds and returns the response containing the feed in HTML format.
   *
   * @param string $id
   *   Accout id.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response with HTML content or a 403 status code on failure.
   */
  public function __invoke(string $id) {
    $this->setCacheKey($id);
    return $this->fetchFeed($id);
  }

  /**
   * Fetch and return the feed data as a JSON response.
   *
   * @param string $id
   *   The account ID for which the feed is to be fetched.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response with feed data.
   */
  public function fetchFeed(string $id): JsonResponse {
    // Cache.
    $cache = $this->cache()->get($this->cacheKey);
    if ($cache) {
      $data = $cache->data;
    }
    else {
      try {
        // Get request.
        $feed = $this->feedFetcher->get($this->buildFeedUrl($id));
        // Load and parse xml data.
        $data = $this->feedParser->loadRawFeedData($feed);
        if (!$data) {
          throw new \Exception();
        }
        $data = $this->feedParser->parseXml($data);
        // Set cache.
        $this->cache()->set($this->cacheKey, $data, time() + 8 * 3600);
      }
      catch (\Exception $e) {
        return new JsonResponse([], 403);
      }
    }
    return new JsonResponse($data);
  }

  /**
   * {@inheritdoc}
   */
  abstract public function buildFeedUrl(string $id): string;

}
