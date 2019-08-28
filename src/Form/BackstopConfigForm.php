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
    $form['config']['remote_server_url'] = [
      '#type' => 'textfield',
      '#title' => 'Remote backstop server url',
      '#default_value' => $config->get('remote_server_url'),
      '#description' => 'From <pathtomodule>/backstop run "npm install && node server.js" to launch a local backstop server',
    ];
    $form['config']['server_url'] = [
      '#type' => 'textfield',
      '#title' => 'Local backstop server url',
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
