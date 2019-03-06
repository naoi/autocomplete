<?php

namespace Drupal\autocomplete\Form;

// Updated by yas 2018/10/23.
// Updated by yas 2018/10/21.
// Updated by yas 2018/10/15.
// Updated by yas 2018/10/09.
// Created by yas 2018/10/07.

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\autocomplete\GCP;

/**
 * Form controller for Product entity edit forms.
 *
 * @ingroup autocomplete
 */
class ProductEntityCloudSqlForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'product_entity_cloud_sql_form';
  }

  /**
   * {@inheritdoc}10:4310:4310:4310:4310:43
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\autocomplete\Entity\ProductEntity */

    $config = \Drupal::config('autocomplete.admin_settings');

    $form['gcp'] = [
      '#type' => 'details',
      '#title' => $this->t('Cloud SQL'),
      '#open' => TRUE,
    ];

    $form['gcp']['name'] = [
        '#title' => $this->t('Product Name (Cloud SQL | Drupal SQL API)'),
        '#type' => 'textfield',
        '#autocomplete_route_name' => 'entity.product_entity.autocomplete',
        '#autocomplete_route_parameters' => [
        'storage_type' => GCP::CLOUD_SQL,
        'count' => $config->get('AUTOCOMPLETE_RESULT_MAX_COUNT') ?: 10,
      ],
      '#required' => TRUE,
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

    $entities = \Drupal::entityTypeManager()
              ->getStorage('product_entity')
              ->loadByProperties(['name' => $form['gcp']['name']['#value']]);
    if ($entity = array_shift($entities)) {
      $form_state->setRedirect('entity.product_entity.canonical', ['product_entity' => $entity->id()]);
    }
  }

}
