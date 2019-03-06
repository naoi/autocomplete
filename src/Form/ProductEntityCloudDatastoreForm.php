<?php

namespace Drupal\autocomplete\Form;

// Updated by yas 2018/10/23.
// Updated by yas 2018/10/22.
// Updated by yas 2018/10/21.
// Updated by yas 2018/10/15.
// Updated by yas 2018/10/08.
// Updated by yas 2018/10/06.
// Created by yas 2018/10/05.

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\autocomplete\GCP;

/**
 * Form controller for Product entity edit forms.
 *
 * @ingroup autocomplete
 */
class ProductEntityCloudDatastoreForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'product_entity_cloud_datastore_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\autocomplete\Entity\ProductEntity */

    $config = \Drupal::config('autocomplete.admin_settings');
    $result_max_count = $config->get('AUTOCOMPLETE_RESULT_MAX_COUNT') ?: 10;

    $form['gcp'] = [
      '#type' => 'details',
      '#title' => $this->t('Cloud Datastore (GQL)'),
      '#open' => TRUE,
    ];

    $form['gcp']['name'] = [
      '#title' => $this->t('Product Name'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'entity.product_entity.autocomplete',
      '#autocomplete_route_parameters' => [
        'storage_type' => GCP::CLOUD_DATASTORE,
        'count' => $result_max_count,
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
