<?php

namespace Drupal\notfound\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Not found controller.
 */
final class NotFoundController extends ControllerBase {


  /**
   * The current request.
   */
  protected Request $request;

  /**
   * Constructor.
   *
   * {@inheritdoc}
   */
  public function __construct(
    RequestStack $request_stack,
  ) {
    $this->request = $request_stack->getCurrentRequest();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
    );
  }

  /**
   * Return.
   */
  public function return() {
    // If accessed by Ajax, return immediately.
    if ($this->request->isXmlHttpRequest()) {
      return new JsonResponse(['message' => 'Not found'], 404);
    }

    // Build render array.
    $build = [
      '#title' => $this->t('Not Found'),
      '#type' => 'markup',
      '#markup' => $this->t('ページが見つかりません。<a href=":login_url">ログインしてください。</a>', [':login_url' => '/user/login']),
      '#allowed_tags' => ['a'],
    ];
    return $build;
  }

}
