<?php

namespace Drupal\app\Controller\Exception;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
class NotFoundPageController extends ControllerBase {

  /**
   * Renderer.
   */
  protected RendererInterface $renderer;

  /**
   * Constructs.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   Renderer Service.
   */
  public function __construct(RendererInterface $renderer) {
    $this->renderer = $renderer;
  }

  /**
   * Renders the 404 page content.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The rendered 404 page response.
   */
  public function __invoke(): Response {
    $build = [
      '#theme' => 'page_404',
      '#title' => $this->t('Page Not Found'),
    ];
    $render = $this->renderer->renderRoot($build);
    return new Response($render->__toString(), 404);
  }

}
