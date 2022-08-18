<?php

namespace Drupal\site_location_time\Services;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Class that will return current time for selected timezone.
 */
class SiteLocationTimezoneManager {

  /**
   * The Drupal Config Factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private $configFactoryService;

  /**
   * Constructs a new CurrentTime object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactoryService
   *   The Drupal Config Factory service.
   */
  public function __construct(ConfigFactoryInterface $configFactoryService) {
    $this->configFactoryService = $configFactoryService;
  }

  /**
   * Function to get current time for selected timezone.
   *
   * @return string
   *   Current Time in desired format.
   */
  public function getCurrentTimeForTimezone() {
    // Fetch site location settings.
    $getLocationSettings = $this->configFactoryService->getEditable('site_location.settings');
    // Get selected timezone value.
    $timezone = $getLocationSettings->get('timezone');

    // Proceed only if timezone is selected.
    if (!empty($timezone)) {
      // Drupal date time object.
      $date = new DrupalDateTime();
      // Set selected timezone as current timezone.
      $date->setTimezone(new \DateTimeZone($timezone));
      // Set date format as required like 29th Dec 2020 - 11:59 PM.
      $timezone_based_time = $date->format('dS M Y - h:i A');
    }
    else {
      $timezone_based_time = "No timezone is selected.";
    }
    return $timezone_based_time;
  }

  /**
   * Function to get current city and country seperately.
   *
   * @return string
   *   String having country and city name seperated by comma.
   */
  public function getCurrentCityWithCountry() {
    // Fetch site location settings.
    $getLocationSettings = $this->configFactoryService->getEditable('site_location.settings');
    // Get country value.
    $country = !empty($getLocationSettings->get('country')) ? $getLocationSettings->get('country') : '';
    // Get city value.
    $city = !empty($getLocationSettings->get('city')) ? $getLocationSettings->get('city') : '';

    // Combine city and country name.
    $location = $city . ',' . $country;
    // Return location.
    return trim($location, ',');
  }

}
