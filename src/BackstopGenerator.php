<?php

namespace Drupal\backstop;

use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;

/**
 * Class BackstopGenerator.
 */
class BackstopGenerator {

  /**
   * Drupal\Core\Entity\EntityManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

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
   * Symfony\Component\DependencyInjection\ContainerAwareInterface definition.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerAwareInterface
   */
  protected $entityQuery;

  /**
   * Drupal\Core\Database\Driver\mysql\Connection definition.
   *
   * @var \Drupal\Core\Database\Driver\mysql\Connection
   */
  protected $database;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Menu\MenuLinkTreeInterface definition.
   *
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface
   */
  protected $menuLinkTree;

  /**
   * Constructs a new BackstopGenerator object.
   */
  public function __construct(EntityManagerInterface $entity_manager, ConfigManagerInterface $config_manager, ConfigFactoryInterface $config_factory, ContainerAwareInterface $entity_query, Connection $database, EntityTypeManagerInterface $entity_type_manager, MenuLinkTreeInterface $menu_link_tree) {
    $this->entityManager = $entity_manager;
    $this->configManager = $config_manager;
    $this->configFactory = $config_factory;
    $this->entityQuery = $entity_query;
    $this->database = $database;
    $this->entityTypeManager = $entity_type_manager;
    $this->menuLinkTree = $menu_link_tree;
  }

  /**
   * Get a list of nodes.
   */
  public function getNodeOptions($count = TRUE) {
    $types = $this->entityTypeManager
      ->getStorage('node_type')
      ->loadMultiple();
    $options = [];
    foreach ($types as $type => $settings) {

      $query = $this->entityQuery->get('node')
        ->condition('status', 1)
        ->condition('type', $type)
        ->count();
      $count = $query->execute();
      $options[$type] = $settings->label() . '(' . $count . ')';
    }
    return $options;
  }

  /**
   * Get a list of nodes.
   */
  public function getMenuOptions() {
    $query = $this->entityQuery->get('menu');

    $result = $query->execute();

    foreach ($result as $menu) {
      $menus[$menu] = $menu;
      // $menus[$menu->getId()] = $menu->getLable();
    }
    return $menus;
  }

  /**
   * Load a list of menu items.
   *
   * @return array
   *   Menu links
   */
  public function loadMenu($name) {
    // Get drupal menu.
    $sub_nav = \Drupal::menuTree()->load($name, new MenuTreeParameters());
    // Generate array.].
    $menu_tree2 = [];
    $this->generateSubMenuTree($menu_tree2, $sub_nav);

    foreach ($menu_tree2 as $key => $value) {
      if ($value) {
        $items[$key] = $value;
      }
    }

    return $items;
  }

  /**
   *
   */
  public function getNodeList($node_type, $limit = 0) {
    $urls = [];
    $user = User::load(0);

    $query = \Drupal::entityQuery('node')->condition('type', $node_type);
    if ($limit > 0) {
      $query->range(0, 10);
    }
    $nids = $query->execute();
    $nodes = Node::loadMultiple($nids);
    foreach ($nodes as $node) {
      $check = $node->access('view', $user);
      if ($check) {
        $url = Url::fromRoute('entity.node.canonical', ['node' => $node->id()]);
        $url_string = $url->toString();
        if (strpos($url_string, '/node/') !== 0) {
          $urls[$url_string] = $url_string;
          if ($node->bundle() == 'resource') {
            $urls[$url_string . '?form=success'] = $url_string . '?form=success';
          }
        }
      }
    }
    return $urls;
  }

  /**
   * A recursive function that goes through submenus.
   */
  private function generateSubMenuTree(&$output, &$input, $parent = FALSE) {
    $input = array_values($input);
    foreach ($input as $key => $item) {
      // If menu element disabled skip this branch.
      if ($item->link->isEnabled()) {
        $key = 'submenu-' . $key;
        $name = $item->link->getTitle();
        $url = $item->link->getUrlObject();
        $url_string = $url->toString();

        // If not root element, add as child.
        if ($parent === FALSE) {
          $output[$url_string] = $url_string;
        }
        else {
          $parent = 'submenu-' . $parent;
          $output[$url_string] = $url_string;

        }

        if ($item->hasChildren) {
          if ($item->depth == 1) {
            $this->generateSubMenuTree($output, $item->subtree, $key);
          }
          else {
            $this->generateSubMenuTree($output, $item->subtree, $key);
          }
        }
      }
    }
  }

}
