<?php

namespace Drupal\feed_api\Interface;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Interface that provide to controller for feed api-endpoint.
 */
interface FeedControllerInterface {

  /**
   * Fetch and return the feed data as a JSON response.
   *
   * @param string $id
   *   The account ID for which the feed is to be fetched.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response with feed data.
   */
  public function fetchFeed(string $id): JsonResponse;

  /**
   * Build the feed URL based on the given ID.
   *
   * @param string $id
   *   The account ID.
   *
   * @return string
   *   The feed URL.
   */
  public function buildFeedUrl(string $id): string;

}
