<?php

namespace Drupal\backstop\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Defines the Backstop scenario entity.
 *
 * @ConfigEntityType(
 *   id = "backstop_scenario",
 *   label = @Translation("Backstop scenario"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\backstop\BackstopScenarioListBuilder",
 *     "form" = {
 *       "add" = "Drupal\backstop\Form\BackstopScenarioForm",
 *       "edit" = "Drupal\backstop\Form\BackstopScenarioForm",
 *       "delete" = "Drupal\backstop\Form\BackstopScenarioDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\backstop\BackstopScenarioHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "backstop_scenario",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/backstop/{backstop_scenario}",
 *     "add-form" = "/admin/config/development/backstop/backstop_scenario/add",
 *     "edit-form" = "/admin/config/development/backstop/backstop_scenario/{backstop_scenario}/edit",
 *     "delete-form" = "/admin/config/development/backstop/backstop_scenario/{backstop_scenario}/delete",
 *     "collection" = "/admin/config/development/backstop/backstop_scenario"
 *   }
 * )
 */
class BackstopScenario extends ConfigEntityBase implements BackstopScenarioInterface {
  private $backstopGenerator;

  /**
   *
   */
  public function __construct(array $values, $entity_type) {
    parent::__construct($values, $entity_type);
    $this->generator = \Drupal::service('backstop.generator');
    $this->config = \Drupal::config('backstop.config');
  }

  /**
   * The Backstop scenario ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Backstop scenario label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Backstop scenario label.
   *
   * @var string
   */
  protected $sources;

  /**
   * Get url sources.
   */
  public function getSources() {

    return $this->getThirdPartySetting('backstop', 'sources', $this->getDefaultSources());

  }

  /**
   *
   */
  public function getTestLink() {
    return Link::fromTextAndUrl('Generate Test', $this->getServerUrl(['command' => 'test']))->toString();
  }

  /**
   *
   */
  public function getReferenceLink() {
    return Link::fromTextAndUrl('Generate Refernce', $this->getServerUrl(['command' => 'reference']))->toString();
  }

  /**
   *
   */
  public function getServerUrl($options = []) {
    $options['jsonUrl'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'];
    $options['scenario'] = $this->id();
    return Url::fromUri($this->config->get('server_url'), ['absolute' => TRUE, 'query' => $options]);
  }

  /**
   *
   */
  public function siteId() {
    return $this->config->get('siteid');
  }

  /**
   * Get default sources.
   */
  public function getDefaultSources() {
    $sources = [
      'node' => [],
      'menu' => [],
      'custom' => [],
    ];
    foreach ($this->generator->getNodeOptions() as $key => $label) {
      $sources['node'][$key] = ['enabled' => 1, 'limit' => 10];
    }
    foreach ($this->generator->getMenuOptions() as $key => $label) {
      $sources['menu'][$key] = ['enabled' => 1];
    }
    return $sources;
  }

  /**
   *
   */
  public function getJson() {
    if ($this->jsonOverride()) {
      return $this->getThirdPartySetting('backstop', 'json', $this->getDefaultJson());
    }
    else {
      return $this->getDefaultJson();
    }
  }

  /**
   *
   */
  public function getReference() {
    if ($this->referenceOverride()) {
      return $this->getThirdPartySetting('backstop', 'json', $this->config->get('json'));
    }
    else {
      return $this->config->get('json');
    }
  }

  /**
   *
   */
  public function getTestUrl() {
    if ($this->testUrlOverride()) {
      return $this->getThirdPartySetting('backstop', 'test_url', $this->config->get('test_url'));
    }
    else {
      return $this->config->get('test_url');
    }
  }

  /**
   *
   */
  public function getReferenceUrl() {
    if ($this->referenceUrlOverride()) {
      return $this->getThirdPartySetting('backstop', 'reference_url', $this->config->get('reference_url'));
    }
    else {
      return $this->config->get('reference_url');
    }
  }

  /**
   *
   */
  public function jsonOverride() {
    $this->getThirdPartySetting('backstop', 'json_override', 0);
  }

  /**
   *
   */
  public function testUrlOverride() {
    $this->getThirdPartySetting('backstop', 'test_url_override', 0);
  }

  /**
   *
   */
  public function referenceUrlOverride() {
    $this->getThirdPartySetting('backstop', 'reference_url_override', 0);
  }

  /**
   *
   */
  public function getDefaultJson() {
    return $this->config->get('json');
  }

  /**
   *
   */
  private function getScenarioMenus() {
    $ret = [];
    $sources = $this->getSources();
    foreach ($sources['menu'] as $key => $value) {

      if ($value['enabled']) {
        $ret[$key] = $key;
      }
    }
    return $ret;
  }

  /**
   *
   */
  private function getScenarioNodeTypes() {
    $ret = [];
    $sources = $this->getSources();
    foreach ($sources['node'] as $key => $value) {

      if ($value['enabled'] == 1) {

        $ret[$key] = $value['limit'];

      }
    }

    return $ret;
  }

  /**
   *
   */
  public function generateJson() {
    $json = json_decode($this->getJson());

    $test_url = $this->getTestUrl();
    $reference_url = $this->getReferenceUrl();
    if (\Drupal::request()->query->get('testUrl')) {
      $test_url = \Drupal::request()->query->get('testUrl');
    }
    if (\Drupal::request()->query->get('referenceUrl')) {
      $test_url = \Drupal::request()->query->get('referenceUrl');
    }

    $urls = [];

    foreach ($this->getScenarioMenus() as $menu_name) {
      $new_urls = $this->generator->loadMenu($menu_name);
      if (count($new_urls) > 0) {
        $urls = array_merge($urls, $new_urls);
      }
    }

    foreach ($this->getScenarioNodeTypes() as $node_type => $limit) {

      $new_urls = $this->generator->getNodeList($node_type, $limit);

      if (count($new_urls) > 0) {
        $urls = array_merge($urls, $new_urls);
      }

    }
    $default_scenario = (array) $json->scenarios[0];

    $json->id = $this->id;
    foreach ($urls as $url) {
      $items[] = array_merge($default_scenario, [
        'label' => $url,
        'url' => $test_url . $url,
        'referenceUrl' => $reference_url . $url,
      ]);
    }

    $json->scenarios = $items;
    $paths = [
      "bitmaps_reference",
      "bitmaps_test",
      "html_report",
      "ci_report",
    ];
    foreach ($paths as $path) {
      $json->paths->$path = "backstop_data/" . '/' . $this->siteId() . '/' . $this->id() . "/" . $path;
    }

    return $json;
  }

  /**
   *
   */
  public function sendRemoteCommand($command) {
    $uri = $this->config->get('server_url') . '/run/' . $command;

    $response = \Drupal::httpClient()->post(
      $uri, [
        'headers' => ['Content-Type' => "application/json"],
        'body' => json_encode($this->generateJson()),
      ]);

    return $response;
  }

}
