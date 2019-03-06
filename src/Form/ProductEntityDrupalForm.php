<?php

namespace Drupal\autocomplete\Form;

// Updated by yas 2018/10/23.
// Updated by yas 2018/10/15.
// Created by yas 2018/10/07.
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;

/**
 * Form controller for Product entity edit forms.
 *
 * @ingroup autocomplete
 */
class ProductEntityDrupalForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'product_entity_drupal_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\autocomplete\Entity\ProductEntity */

    // $product_entity = ProductEntity::create([]);
    // $form = parent::buildForm($form, $form_state);
    // $build = \Drupal::service('entity.form_builder')->getForm($product_entity);

    $form['drupal'] = [
      '#type' => 'details',
      '#title' => $this->t('Cloud SQL'),
      '#open' => TRUE,
    ];

    $form['drupal']['name'] = [
      '#title' => $this->t('Product Name (Cloud SQL | Drupal Autocomplete API)'),
      '#type' => 'entity_autocomplete',
      '#target_type' => 'product_entity',
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
              ->loadByProperties(['name' => $form['drupal']['name']['#value']]);
    if ($entity = array_shift($entities)) {
      $form_state->setRedirect('entity.product_entity.canonical', ['product_entity' => $entity->id()]);
    }
  }

}
