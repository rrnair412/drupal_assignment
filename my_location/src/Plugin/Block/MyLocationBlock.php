<?php

namespace Drupal\my_location\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Cache\Cache;
use Drupal\my_location\Service\GetDateTime;

/**
 * Display Location and Current time as per Time Zone Selected from Configuration Form.
 *
 * @Block(
 *   id = "my_location_block",
 *   admin_label = @Translation("Display User Current Time"),
 * )
 */
class MyLocationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Configuration Factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Block class constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Drupal config factory service.
   * @param Drupal\my_location\Service\GetDateTime $get_datetime
   *   Custom User location service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $configFactory, GetDateTime $get_datetime) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $configFactory;
    $this->get_datetime = $get_datetime;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('my_location.get_date_time')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $city = $this->configFactory->get('my_location.settings')->get('city');
    $country = $this->configFactory->get('my_location.settings')->get('country');
    $renderable['result'] = [
      '#lazy_builder' => ['my_location.get_date_time:getTimeDate', [
        $city,
        $country,
      ]
    ],
      '#create_placeholder' => TRUE,
    ];
    return $renderable;
  }

  /**
   * Invalidate cache on config update.
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), [
      'config:my_location.settings',
    ]);
  }

}
