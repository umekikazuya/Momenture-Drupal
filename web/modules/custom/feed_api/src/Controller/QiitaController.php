<?php

declare(strict_types=1);

namespace Drupal\feed_api\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Qiita routes.
 */
final class QiitaController extends FeedControllerBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('feed_api.fetcher'),
      $container->get('feed_api.qiita_parser'),
      'https://qiita.com/',
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
   * {@inheritdoc}
   */
  public function buildFeedUrl(string $id): string {
    return $this->baseUrl . $id . '/feed';
  }

}
