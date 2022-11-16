<?php

namespace Drupal\my_location\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Security\TrustedCallbackInterface;

/**
 * Class GetDateTime.
 *
 * Provide current time and date based on the time zone selection from admin configuration form.
 */
class GetDateTime implements TrustedCallbackInterface {

  /**
   * Configuration Factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Service constructor.
   *
   * @param Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Drupal config factory service.
   */
  public function __construct(ConfigFactoryInterface $configFactory) {
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public function getTimeDate($city, $country) {
    $data = [];
    $timezone = $this->configFactory->get('my_location.settings')->get('timezone');
    $date = new \DateTime("now", new \DateTimeZone($timezone));
    $time = $date->format('g:i a');
    $todayDate = $date->format('d F Y');
    $day = $date->format('l');

    $date_info = [
      'time' => $time,
      'date' => $todayDate,
      'day' => $day
    ];

    $data = [
      'date_info' => $date_info,
      'city' => $city,
      'country' => $country,
    ];
    $renderable = [
      '#theme' => 'my-location',
      '#data' => $data,
    ];

    return $renderable;

  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return [
      'getTimeDate'
    ];
  }

}
