<?php

declare(strict_types=1);

namespace Drupal\feed_api\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Zenn routes.
 */
final class ZennController extends FeedControllerBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('feed_api.fetcher'),
      $container->get('feed_api.zenn_parser'),
      'https://zenn.dev/',
    );
  }

  /**
   * Set cache-key.
   *
   * @param string $id
   *   Zenn account ID used to generate the cache key.
   */
  protected function setCacheKey(string $id): void {
    $this->cacheKey = 'zenn:' . $id;
  }

  /**
   * {@inheritdoc}
   */
  public function buildFeedUrl(string $id): string {
    return $this->baseUrl . $id . '/feed';
  }

}
