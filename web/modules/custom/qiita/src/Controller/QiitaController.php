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
  public function __invoke() {
    $res = new JsonResponse();
    try {
      $http_request = $this->httpClient->get('https://qiita.com/umekikazuya/feed');
      $feed = $http_request->getBody()->getContents();
      if (!$feed) {
        throw new \Exception();
      }
      $data = $this->loadRawFeedData($feed);
      if (!$data) {
        throw new \Exception();
      }
      $markup_li = $this->parseXml($data);

      $html = <<<HTML
      <div class="p-widget__header"><h3>Qiita</h3>umekikazuya</div>
      <div class="p-rss-feed"><ul class="p-rss-feed__wrapper">{$markup_li}</ul></div>
      HTML;
    }
    catch (\Exception $e) {
      return $res->setStatusCode(403);
    }
    return $res->setJson(json_encode($html));
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
   * @param \SimpleXMLElement $data
   *   The XML data.
   *
   * @return string
   *   The HTML markup for the list items.
   */
  private function parseXml(\SimpleXMLElement $data) {
    $markup_li = '';
    foreach ($data->entry as $o) {
      // Get feed item.
      $title = $o->title;
      $link = $o->url;
      $published = $o->published;
      if (!isset($title) || !isset($link) || !isset($published)) {
        continue;
      }
      $pub_date = new DrupalDateTime($o->published);

      // Markup.
      $markup_li .= <<<HTML
      <li class="p-rss-feed__item">
        <a class="p-rss-feed__item-link" href="{$link}" target="_blank">
          <span class="p-rss-feed__title">{$title}</span>
          <time datatime="{$pub_date->format('Y-m-d')}" class="p-rss-feed__item-date">{$pub_date->format('Y年n月j日')}</time>
        </a>
      </li>
      HTML;
    }
    return $markup_li;
  }

}
