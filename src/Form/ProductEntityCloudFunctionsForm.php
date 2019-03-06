<?php

namespace Drupal\autocomplete\Form;

// Updated by yas 2018/11/06.
// Updated by yas 2018/10/29.
// Updated by yas 2018/10/28.
// Updated by yas 2018/10/23.
// Updated by yas 2018/10/21.
// Created by yas 2018/10/20.

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\autocomplete\GCP;

/**
 * Form controller for Product entity edit forms.
 *
 * @ingroup autocomplete
 */
class ProductEntityCloudFunctionsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'product_entity_cloud_funcsions_form';
  }

  /**
   * {@inheritdoc}10:4310:4310:4310:4310:43
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\autocomplete\Entity\ProductEntity */

    $form['gcp'] = [
      '#type' => 'details',
      '#title' => $this->t('Cloud Functions'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    $form['gcp'][(string) "GCP::CLOUD_SQL"] = [
      '#title' => $this->t('Product Name (Cloud SQL)'),
      '#type' => 'textfield',
      '#attributes' => ['id'    => 'cloud_sql',
                        'class' => ['form-autocomplete',
                                    'form-text',
                                    'required',
                                    'form-control',
                                    'ui-autocomplete-input',
                                   ],
                       ],
      '#field_prefix' => '<div class="input-group">',
      '#field_suffix' => '<span class="input-group-addon"><span class="icon glyphicon glyphicon-refresh ajax-progress ajax-progress-throbber" aria-hidden="true"></span></span></div>',
    ];

    $form['gcp'][(string) "GCP::CLOUD_DATASTORE"] = [
      '#title' => $this->t('Product Name (Cloud Datastore)'),
      '#type' => 'textfield',
      '#attributes' => ['id'    => 'cloud_datastore',
                        'class' => ['form-autocomplete',
                                    'form-text',
                                    'required',
                                    'form-control',
                                    'ui-autocomplete-input',
                                   ],
                       ],
      '#field_prefix' => '<div class="input-group">',
      '#field_suffix' => '<span class="input-group-addon"><span class="icon glyphicon glyphicon-refresh ajax-progress ajax-progress-throbber" aria-hidden="true"></span></span></div>',
    ];

    $form['gcp'][(string) "GCP::BIGQUERY"] = [
      '#title' => $this->t('Product Name (Bigquery)'),
      '#type' => 'textfield',
      '#attributes' => ['id'    => 'bigquery',
                        'class' => ['form-autocomplete',
                                    'form-text',
                                    'required',
                                    'form-control',
                                    'ui-autocomplete-input',
                                   ],
                       ],
      '#field_prefix' => '<div class="input-group">',
      '#field_suffix' => '<span class="input-group-addon"><span class="icon glyphicon glyphicon-refresh ajax-progress ajax-progress-throbber" aria-hidden="true"></span></span></div>',
    ];

    $form['actions']['#type'] = 'actions';
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $storage_types = [
      'drupal'     => ['entity_autocomplete'],
      'gcp'        => [(string) "GCP::CLOUD_SQL",
                       (string) "GCP::CLOUD_DATASTORE",
                       (string) "GCP::BIGQUERY"],
      'cloud_lamp' => [(string) "GCP::CLOUD_SQL",
                       (string) "GCP::CLOUD_DATASTORE",
                       (string) "GCP::BIGQUERY"],
    ];

    $name = '';
    foreach ($storage_types as $fieldset => $items) {
      foreach ($items as $storage_type) {
        if (!empty($form_state->getValue([$fieldset, $storage_type]))) {
          $name = $form_state->getValue([$fieldset, $storage_type]);
          break 2;
        }
      }
    }

    $entities = \Drupal::entityTypeManager()
              ->getStorage('product_entity')
              ->loadByProperties(['name' => $name]);
    if ($entity = array_shift($entities)) {
      $form_state->setRedirect('entity.product_entity.canonical', ['product_entity' => $entity->id()]);
    }
  }

}
