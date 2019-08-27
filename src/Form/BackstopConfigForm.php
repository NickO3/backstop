<?php

namespace Drupal\backstop\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class BackstopConfigForm.
 */
class BackstopConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'backstop.config',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'backstop_config_form';
  }

  /**
   * Implements menu_trail_by_path_get_active_link_alter().
   */
  public function vertexinc_other_menu_trail_by_path_get_active_link_alter(&$menu_link) {
    // Check if a menu link is active.
    if ($menu_link) {
      // Get route Node, if available.
      $node = \Drupal::routeMatch()->getParameter('node');
      if ($node) {
        print 'test';
        $id = FALSE;
        // Give priority to the default menu.
        $menu_name = 'primary';
        $path = \Drupal::service('path.current')->getPath();
        $query = \Drupal::database()->select('menu_link_content')
          ->condition('link.uri', 'entity:node/' . $node->id())
          ->condition('menu_name', $menu_name)
          ->sort('id', 'ASC')
          ->range(0, 1);
        $result = $query->execute();
        $id = (!empty($result)) ? reset($result) : FALSE;
        dpm($id);
        if ($id) {
          $menu_link = MenuLinkContent::load($id);
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('backstop.config');
    $form['config']['#tree'] = 1;
    $form['config']['reference_url'] = [
      '#type' => 'textfield',
      '#title' => 'Reference URL',
      '#default_value' => $config->get('reference_url'),
      '#description' => 'The site you want to test changes against. Typically the live site.',
    ];
    $form['config']['test_url'] = [
      '#type' => 'textfield',
      '#title' => 'Test URL',
      '#default_value' => $config->get('test_url'),
      '#description' => 'The site you want to test. Typically the dev site.',
    ];
    $form['config']['server_url'] = [
      '#type' => 'textfield',
      '#title' => 'Backstop server url',
      '#default_value' => $config->get('server_url'),
      '#description' => 'From <pathtomodule>/backstop run "npm install && node server.js" to launch a local backstop server',
    ];
    $form['config']['json'] = [
      '#type' => 'textarea',
      '#title' => 'Json Boilerplate',
      '#default_value' => $config->get('json'),
      '#description' => 'Default boilerplate',
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $values = $form_state->getValue('config');

    foreach ($values as $key => $value) {
      $this->config('backstop.config')->set($key, $value)->save();
    }
  }

}
