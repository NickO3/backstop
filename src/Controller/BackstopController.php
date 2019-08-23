<?php

namespace Drupal\backstop\Controller;

use Drupal\backstop\Entity\BackstopScenario;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\backstop\BackstopGenerator;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\Core\Config\ConfigManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BackstopController.
 */
class BackstopController extends ControllerBase {

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
   * Constructs a new BackstopController object.
   */
  public function __construct(BackstopGenerator $backstop_generator, EntityManagerInterface $entity_manager, Connection $database, ConfigManagerInterface $config_manager, ContainerAwareInterface $entity_query, EntityTypeManagerInterface $entity_type_manager) {
    $this->backstopGenerator = $backstop_generator;
    $this->entityManager = $entity_manager;
    $this->database = $database;
    $this->configManager = $config_manager;
    $this->entityQuery = $entity_query;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('backstop.generator'),
      $container->get('entity.manager'),
      $container->get('database'),
      $container->get('config.manager'),
      $container->get('entity.query'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function jsonScenario(BackstopScenario $backstop_scenario) {
    // print_r($backstop_scenario);
    $response = new Response();
    $response->setContent(json_encode($backstop_scenario->generateJson()));
    $response->headers->set('Content-Type', 'application/json');
    return $response;
  }

}
