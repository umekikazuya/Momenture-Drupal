<?php

declare(strict_types=1);

namespace Drupal\qiita\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\qiita\Interface\FeedFetcherInterface;
use Drupal\qiita\Interface\FeedParserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns responses for Qiita routes.
 */
final class QiitaController extends ControllerBase {

  /**
   * Qiita Url.
   *
   * @var string
   */
  private const QIITA_URL = 'https://qiita.com/';

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
   * Constructor.
   *
   * @param \Drupal\qiita\Service\Interface\FeedFetcherInterface $feed_fetcher
   *   Feed Fetcher.
   * @param \Drupal\qiita\Service\Interface\FeedParserInterface $feed_parser
   *   Feed Parser.
   */
  public function __construct(
    FeedFetcherInterface $feed_fetcher,
    FeedParserInterface $feed_parser,
  ) {
    $this->feedFetcher = $feed_fetcher;
    $this->feedParser = $feed_parser;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('qiita.feed_fetcher'),
      $container->get('qiita.feed_parser'),
    );
  }

  /**
   * Set cache-key.
   *
   * @param string $id
   *   Qiita account ID used to generate the cache key.
   */
  protected function setCacheKey(string $id): void {
    $this->cacheKey = 'qiita:' . $id;
  }

  /**
   * Builds and returns the response containing the Qiita feed in HTML format.
   *
   * @param string $id
   *   Qiita accout id.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response with HTML content or a 403 status code on failure.
   */
  public function __invoke(string $id) {
    // Cache.
    $this->setCacheKey($id);
    $cache = $this->cache()->get($this->cacheKey);
    if ($cache) {
      $data = $cache->data;
    }
    else {
      try {
        // Get request.
        $feed = $this->feedFetcher->get(self::QIITA_URL . $id . '/feed');
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

}
