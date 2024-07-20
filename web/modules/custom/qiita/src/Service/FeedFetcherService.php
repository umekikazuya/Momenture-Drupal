<?php

declare(strict_types=1);

namespace Drupal\qiita\Service;

use Drupal\qiita\Interface\FeedFetcherInterface;
use GuzzleHttp\Client;

/**
 * Service class for fetching feed data using the Guzzle HTTP client.
 *
 * This class implements the FeedFetcherInterface to provide a concrete
 * method for fetching feed data over HTTP. It utilizes the Guzzle HTTP
 * client to send GET requests and handle responses. The class is designed
 * to manage HTTP communication and error handling effectively, encapsulating
 * all the complexities of network interactions.
 */
final class FeedFetcherService implements FeedFetcherInterface {

  /**
   * The HTTP client used for making requests.
   */
  protected Client $httpClient;

  /**
   * Constructor.
   *
   * Initializes the feed fetcher service with a Guzzle HTTP client.
   *
   * @param \GuzzleHttp\Client $http_client
   *   The Guzzle HTTP client instance.
   */
  public function __construct(
    Client $http_client,
  ) {
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritDoc}
   */
  public function get(string $url): string {
    $http_request = $this->httpClient->get($url);
    $feed = $http_request->getBody()->getContents();
    if (!$feed) {
      throw new \Exception();
    }
    return $feed;
  }

}
