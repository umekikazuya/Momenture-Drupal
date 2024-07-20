<?php

declare(strict_types=1);

namespace Drupal\qiita\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\qiita\Interface\FeedParserInterface;
use GuzzleHttp\Client;
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
   * Http client.
   */
  protected Client $httpClient;

  /**
   * Feed Parser.
   */
  protected FeedParserInterface $feedParser;

  /**
   * Constructor.
   *
   * @param \GuzzleHttp\Client $http_client
   *   Guzzle.
   * @param \Drupal\qiita\Service\Interface\FeedParserInterface $feed_parser
   *   Feed Parser.
   */
  public function __construct(
    Client $http_client,
    FeedParserInterface $feed_parser,
  ) {
    $this->httpClient = $http_client;
    $this->feedParser = $feed_parser;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client'),
      $container->get('qiita.feed_parser'),
    );
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
    $cache_key = 'api_qiita:' . $id;
    $cache = $this->cache()->get($cache_key);
    if ($cache) {
      $data = $cache->data;
    }
    else {
      try {
        $qiita_url = self::QIITA_URL . $id;
        $http_request = $this->httpClient->get($qiita_url . '/feed');
        $feed = $http_request->getBody()->getContents();
        if (!$feed) {
          throw new \Exception();
        }
        // Load and parse xml data.
        $data = $this->feedParser->loadRawFeedData($feed);
        if (!$data) {
          throw new \Exception();
        }
        $data = $this->feedParser->parseXml($data);
        // Set cache.
        $this->cache()->set($cache_key, $data, time() + 8 * 3600);
      }
      catch (\Exception $e) {
        return new JsonResponse([], 403);
      }
    }
    return new JsonResponse($data);
  }

  /**
   * Get feed data.
   */
  protected function getFeedData(string $url) {
    $http_request = $this->httpClient->get($url);
    $feed = $http_request->getBody()->getContents();
    if (!$feed) {
      throw new \Exception();
    }
  }

}
