<?php

namespace Drupal\autocomplete\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Store entity edit forms.
 *
 * @ingroup autocomplete
 */
class StoreEntityForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\autocomplete\Entity\StoreEntity */
    $form = parent::buildForm($form, $form_state);

    // $entity = $this->entity;.
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
        drupal_set_message($this->t('Created the %label Store entity.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Store entity.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.store_entity.canonical', ['store_entity' => $entity->id()]);
  }

}
