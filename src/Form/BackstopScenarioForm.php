<?php

namespace Drupal\backstop\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class BackstopScenarioForm.
 */
class BackstopScenarioForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $generator = \Drupal::service('backstop.generator');
    $backstop_scenario = $this->entity;
    $backstop_scenario->sendRemoteCommand('test');
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $backstop_scenario->label(),
      '#description' => $this->t("Label for the Backstop test entity."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $backstop_scenario->id(),
      '#machine_name' => [
        'exists' => '\Drupal\backstop\Entity\BackstopScenario::load',
      ],
      '#disabled' => !$backstop_scenario->isNew(),
    ];
    $form['backstop'] = ['#tree' => 1];
    $form['backstop']['reference_url_override'] = [
      '#type' => 'checkbox',
      '#title' => 'Override reference Url',
      '#default_value' => $backstop_scenario->referenceUrlOverride(),
    ];
    $form['backstop']['reference_url'] = [
      '#type' => 'textfield',
      '#title' => 'Reference Url',
      '#default_value' => $backstop_scenario->getReferenceUrl(),
      '#states' => [
        // Show this textfield only if the radio 'other' is selected above.
        'visible' => [
          'input[name="backstop[reference_url_override]"]' => ['checked' => TRUE],
        ],
      ],
    ];
    $form['backstop']['test_url_override'] = [
      '#type' => 'checkbox',
      '#title' => 'Override test Url',
      '#default_value' => $backstop_scenario->testUrlOverride(),

    ];
    $form['backstop']['test_url'] = [
      '#type' => 'textfield',
      '#title' => 'test Url',
      '#default_value' => $backstop_scenario->getTestUrl(),
      '#states' => [
        // Show this textfield only if the radio 'other' is selected above.
        'visible' => [
          'input[name="backstop[test_url_override]"]' => ['checked' => TRUE],
        ],
      ],
    ];
    $form['backstop']['json_override'] = [
      '#type' => 'checkbox',
      '#title' => 'Override default boilerplate',
      '#default_value' => $backstop_scenario->jsonOverride(),
    ];
    $form['backstop']['json'] = [
      '#title' => 'Custom Json',
      '#type' => 'textarea',

      '#default_value' => $backstop_scenario->getJson(),
      '#states' => [
        // Show this textfield only if the radio 'other' is selected above.
        'visible' => [
          'input[name="backstop[json_override]"]' => ['checked' => TRUE],
        ],
      ],
    ];
    $form['backstop']['sources']['node_type_label']['#markup'] = '<h3>Node type url sources</h3>';
    $form['backstop']['sources']['node'] = [
      '#type' => 'table',
      '#header' => ['Type (total)' , 'Enabled', 'Max Number of to Include (enter 0 for no limit)'],
      '#empty' => t('No users found'),
    ];
    $sources = $backstop_scenario->getSources();
    foreach ($generator->getNodeOptions() as $key => $label) {
      $row = [];
      $row['type']['#markup'] = $label;
      $row['enabled'] = [
        '#type' => 'checkbox',
        '#default_value' => $sources['node'][$key]['enabled'],
      ];
      $row['limit'] = [
        '#type' => 'textfield',
        '#default_value' => $sources['node'][$key]['limit'],
      ];
      $form['backstop']['sources']['node'][$key] = $row;
    }

    $form['backstop']['sources']['menu']['#markup'] = '<h3>Menu sources</h3>';
    $form['backstop']['sources']['menu'] = [
      '#type' => 'table',
      '#header' => ['Menu name', 'enabled'],
      '#empty' => t('No users found'),
    ];
    foreach ($generator->getMenuOptions() as $key => $label) {
      $row = [];
      $row['type']['#markup'] = $label;
      $row['enabled'] = [
        '#type' => 'checkbox',
        '#default_value' => $sources['menu'][$key]['enabled'],
      ];
      $form['backstop']['sources']['menu'][$key] = $row;
    }
    $form['backstop']['sources']['custom'] = [
      '#type' => 'textarea',
      '#title' => 'Custom paths',
      '#default_value' => $sources['custom'],
      '#description' => 'Enter urls seperated by a new line. E.g. "/solutions/money" Not "http://example.com/solutions/money"',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $backstop_scenario = $this->entity;
    foreach ($form_state->getValue('backstop') as $key => $value) {
      $this->entity->setThirdPartySetting('backstop', $key, $value);
    }
    $status = $backstop_scenario->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Backstop scenario.', [
          '%label' => $backstop_scenario->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Backstop scenario.', [
          '%label' => $backstop_scenario->label(),
        ]));
    }
    $form_state->setRedirectUrl($backstop_scenario->toUrl('collection'));
  }

}
