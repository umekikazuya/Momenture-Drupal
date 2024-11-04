<?php

namespace Drupal\app\EventSubscriber;

use Drupal\app\Controller\Exception\NotFoundPageController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 *
 */
class HttpExceptionSubscriber implements EventSubscriberInterface {

  protected NotFoundPageController $notFoundPageController;

  /**
   * Constructs.
   */
  public function __construct(NotFoundPageController $controller) {
    $this->notFoundPageController = $controller;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::EXCEPTION][] = ['onException'];
    return $events;
  }

  /**
   * Handles 404 errors by delegating to the 404 page controller.
   */
  public function onException(ExceptionEvent $event) {
    $exception = $event->getThrowable();

    if ($exception instanceof NotFoundHttpException) {
      $response = $this->notFoundPageController->__invoke();
      $event->setResponse($response);
    }
  }

}
