<?php

namespace Drupal\site_location_time\EventSubscriber;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableResponseInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Page response subscriber to set appropriate headers on anonymous requests.
 */
class InvalidateBlockCache implements EventSubscriberInterface {

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $user;

  /**
   * Class constructor.
   *
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The Time service.
   * @param \Drupal\Core\Session\AccountInterface $user
   *   Current user.
   */
  public function __construct(TimeInterface $time, AccountInterface $user) {
    $this->time = $time;
    $this->user = $user;
  }

  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE][] = ['onResponse'];
    return $events;
  }

  /**
   *
   */
  public function onResponse(FilterResponseEvent $event) {
    // Don't bother proceeding on sub-requests.
    if (!$event->isMasterRequest()) {
      return;
    }
    $response = $event->getResponse();

    // Nothing to here if there isn't cacheable metadata available.
    if (!($response instanceof CacheableResponseInterface)) {
      return;
    }

    // Bail out early if this isn't an anonymous request.
    if ($this->user->isAuthenticated()) {
      return;
    }

    // Customized bussiness logic.
    $max_age = (int) $response->getCacheableMetadata()->getCacheMaxAge();
    if ($max_age !== Cache::PERMANENT) {
      $response->setMaxAge($max_age);
      $date = new \DateTime('@' . ($this->time->getRequestTime() + $max_age));
      $response->setExpires($date);
    }
  }

}
