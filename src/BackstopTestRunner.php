<?php

namespace Drupal\backstop;

use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Class BackstopTestRunner.
 */
class BackstopTestRunner {

  /**
   * Drupal\Core\Config\ConfigManagerInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigManagerInterface
   */
  protected $configManager;

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Drupal\backstop\BackstopGenerator definition.
   *
   * @var \Drupal\backstop\BackstopGenerator
   */
  protected $backstopGenerator;

  /**
   * Constructs a new BackstopTestRunner object.
   */
  public function __construct(ConfigManagerInterface $config_manager, ConfigFactoryInterface $config_factory, BackstopGenerator $backstop_generator) {
    $this->configManager = $config_manager;
    $this->configFactory = $config_factory;
    $this->backstopGenerator = $backstop_generator;
  }

  /**
   *
   */
  public function run() {
    $module_path = drupal_get_path('module', 'backstop') . '/backstopjs/backstop.js';
    $command = 'node ' . DRUPAL_ROOT . '/' . $module_path . ' test basic --url="http://vertexinc.docksal"';
    print $command;
    $a = popen($command, 'r');
    while ($b = fgets($a)) {
      echo $b . "<br>\n";
      ob_flush();
      flush();
    }
    pclose($a);
  }

}
