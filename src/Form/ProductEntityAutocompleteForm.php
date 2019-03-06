<?php

namespace Drupal\autocomplete\Form;

// Updated by yas 2018/11/06.
// Updated by yas 2018/10/29.
// Updated by yas 2018/10/23.
// Updated by yas 2018/10/21.
// Updated by yas 2018/10/20.
// Updated by yas 2018/10/15.
// Updated by yas 2018/10/09.
// Updated by yas 2018/10/08.
// Created by yas 2018/10/07.

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\autocomplete\GCP;

/**
 * Form controller for Product entity edit forms.
 *
 * @ingroup autocomplete
 */
class ProductEntityAutocompleteForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'product_entity_autocomplete_form';
  }

  /**
   * {@inheritdoc}10:4310:4310:4310:4310:43
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\autocomplete\Entity\ProductEntity */

    $config = \Drupal::config('autocomplete.admin_settings');
    $result_max_count = $config->get('AUTOCOMPLETE_RESULT_MAX_COUNT') ?: 10;

    $form['drupal'] = [
      '#type' => 'details',
      '#title' => t('Drupal'),
      '#open' => FALSE,
      '#tree' => TRUE,
    ];

    $form['drupal']['entity_autocomplete'] = [
      '#title' => $this->t('Product Name (Cloud SQL | Drupal Autocomplete API)'),
      '#type' => 'entity_autocomplete',
      '#target_type' => 'product_entity',
    ];

    $form['gcp'] = [
      '#type' => 'details',
      '#title' => t('Serverless'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    $form['gcp'][(string) "GCP::CLOUD_SQL"] = [
      '#title' => $this->t('Product Name (Cloud Functions | Cloud SQL)'),
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
      '#title' => $this->t('Product Name (Cloud Functions | Cloud Datatstore)'),
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
      '#title' => $this->t('Product Name (Cloud Functions | Bigquery)'),
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

    $form['cloud_lamp'] = [
      '#type' => 'details',
      '#title' => t('Cloud LAMP'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    $form['cloud_lamp'][(string) "GCP::CLOUD_SQL"] = [
      '#title' => $this->t('Product Name (Cloud SQL | Drupal SQL API)'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'entity.product_entity.autocomplete',
      '#autocomplete_route_parameters' => [
        'storage_type' => GCP::CLOUD_SQL,
        'count' => $result_max_count,
      ],
    ];

    $form['cloud_lamp'][(string) "GCP::CLOUD_DATASTORE"] = [
      '#title' => $this->t('Product Name (Cloud Datastore | GQL)'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'entity.product_entity.autocomplete',
      '#autocomplete_route_parameters' => [
        'storage_type' => GCP::CLOUD_DATASTORE,
        'count' => $result_max_count,
      ],
    ];

    $form['cloud_lamp'][(string) "GCP::BIGQUERY"] = [
      '#title' => $this->t('Product Name (BigQuery | SQL)'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'entity.product_entity.autocomplete',
      '#autocomplete_route_parameters' => [
        'storage_type' => GCP::BIGQUERY,
        'count' => $result_max_count,
      ],
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
