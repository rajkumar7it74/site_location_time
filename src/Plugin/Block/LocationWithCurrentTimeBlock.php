<?php

namespace Drupal\site_location_time\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\site_location_time\Services\SiteLocationTimezoneManager;

/**
 * Provides a 'Location with Current Time' block.
 *
 * @Block(
 *   id = "location_current_time_block",
 *   admin_label = @Translation("Location with Current Time Block"),
 *   category = @Translation("Site Location Time")
 * )
 */
class LocationWithCurrentTimeBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $routeMatch;

  /**
   * The current time service.
   *
   * @var \Drupal\site_location_time\Services\SiteLocationTimezoneManager
   */
  protected $currenttimeService;

  /**
   * Constructs a LocationWithCurrentTimeBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param Drupal\Core\Routing\CurrentRouteMatch $route_match
   *   The route manager service.
   * @param Drupal\site_location_time\Services\SiteLocationTimezoneManager $currenttimeService
   *   The current time service.
   */
  public function __construct(
        array $configuration,
        $plugin_id,
        array $plugin_definition,
        CurrentRouteMatch $route_match,
        SiteLocationTimezoneManager $currenttimeService
    ) {

    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $route_match;
    $this->currenttimeService = $currenttimeService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
          $configuration,
          $plugin_id,
          $plugin_definition,
          $container->get('current_route_match'),
          $container->get('site_location_time.manager')
      );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Get current time for selected timezone.
    $current_time = $this->currenttimeService->getCurrentTimeForTimezone();
    // Get current location city and country saved in site location settings.
    $current_location = $this->currenttimeService->getCurrentCityWithCountry();

    // Set build to build block.
    $build = [
      '#theme' => 'location_with_current_time_block',
      '#current_location' => $current_location,
      '#location_current_time' => $current_time,
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    // If I'll set max-age to 0 that means caching is set to
    // never cache this block, so I am setting max-age to 60 seconds so that
    // it can be invalidated within 60 secs.
    return 60;
  }

}
