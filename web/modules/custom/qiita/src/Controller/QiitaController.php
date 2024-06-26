<?php

declare(strict_types=1);

namespace Drupal\qiita\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns responses for Qiita routes.
 */
final class QiitaController extends ControllerBase {

  /**
   * Http client.
   */
  protected Client $httpClient;

  /**
   * Constructor.
   *
   * @param \GuzzleHttp\Client $http_client
   *   Guzzle.
   */
  public function __construct(
    Client $http_client,
  ) {
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client'),
    );
  }

  /**
   * Builds and returns the response containing the Qiita feed in HTML format.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response with HTML content or a 403 status code on failure.
   */
  public function __invoke(string $id) {
    $res = new JsonResponse();
    $rss_url = 'https://qiita.com/' . $id . '/feed';
    try {
      $http_request = $this->httpClient->get($rss_url);
      $feed = $http_request->getBody()->getContents();
      if (!$feed) {
        throw new \Exception();
      }
      $data = $this->loadRawFeedData($feed);
      if (!$data) {
        throw new \Exception();
      }
      $data = $this->parseXml($data, $rss_url);
    }
    catch (\Exception $e) {
      return $res->setStatusCode(403);
    }
    return $res->setJson(json_encode($data));
  }

  /**
   * Load raw feed data.
   *
   * @param string $feed
   *   The raw feed data as a string.
   *
   * @return \SimpleXMLElement|false
   *   The SimpleXMLElement object if successful, FALSE otherwise.
   */
  private function loadRawFeedData(string $feed): \SimpleXMLElement|false {
    $data = simplexml_load_string($feed);
    if (assert($data instanceof \SimpleXMLElement)) {
      return $data;
    }
    return FALSE;
  }

  /**
   * Parses XML data and generates list item markup for each feed item.
   *
   * @param \SimpleXMLElement $xml
   *   The XML data.
   * @param string $url
   *   Rss url.
   *
   * @return array
   *   The array for the list items.
   */
  private function parseXml(\SimpleXMLElement $xml, string $url): array {
    $articles = [];
    foreach ($xml->entry as $o) {
      // Get feed item.
      $title = (string) $o->title;
      $link = (string) $o->url;
      $published = (string) $o->published;
      if (!isset($title) || !isset($link) || !isset($published)) {
        continue;
      }
      $published = new DrupalDateTime($published);

      $articles[] = [
        'title' => $title,
        'link' => $link,
        'published' => $published->format('c'),
      ];
    }
    return [
      'title' => (string) $xml->title,
      'link' => $url,
      'data' => $articles,
    ];
  }

}
