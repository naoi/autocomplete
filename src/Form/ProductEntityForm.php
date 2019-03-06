<?php

namespace Drupal\autocomplete\Form;

// Updated by yas 2018/10/09.
// Created by yas 2018/10/03.
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Product entity edit forms.
 *
 * @ingroup autocomplete
 */
class ProductEntityForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\autocomplete\Entity\ProductEntity */

    $entity = $this->entity;

    $form = parent::buildForm($form, $form_state);
/** // entity_autocomplete gives an error
    $form['name'] = [
      '#title' => $this->t('Product Name (@label)', ['@label' => 'Cloud SQL (Drupal)']),
      '#type' => 'entity_autocomplete',
      '#target_type' => 'product_entity',
      '#required' => TRUE,
      '#default_value' => !$entity->isNew() ? $entity : '',
    ];
*/
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Product entity.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Product entity.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.product_entity.canonical', ['product_entity' => $entity->id()]);
  }

}
