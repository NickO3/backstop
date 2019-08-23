<?php

namespace Drupal\backstop\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\backstop\BackstopGenerator;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\Core\Config\ConfigManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Provides automated tests for the backstop module.
 */
class BackstopControllerTest extends WebTestBase {

  /**
   * Drupal\backstop\BackstopGenerator definition.
   *
   * @var \Drupal\backstop\BackstopGenerator
   */
  protected $backstopGenerator;

  /**
   * Drupal\Core\Entity\EntityManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Drupal\Core\Database\Driver\mysql\Connection definition.
   *
   * @var \Drupal\Core\Database\Driver\mysql\Connection
   */
  protected $database;

  /**
   * Drupal\Core\Config\ConfigManagerInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigManagerInterface
   */
  protected $configManager;

  /**
   * Symfony\Component\DependencyInjection\ContainerAwareInterface definition.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerAwareInterface
   */
  protected $entityQuery;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "backstop BackstopController's controller functionality",
      'description' => 'Test Unit for module backstop and controller BackstopController.',
      'group' => 'Other',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests backstop functionality.
   */
  public function testBackstopController() {
    // Check that the basic functions of module backstop.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
