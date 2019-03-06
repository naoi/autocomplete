<?php

namespace Drupal\autocomplete\Form;

// Created by yas 2018/10/02.
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Autocomplete entity edit forms.
 *
 * @ingroup autocomplete
 */
class AutocompleteEntityForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\autocomplete\Entity\AutocompleteEntity */
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
        drupal_set_message($this->t('Created the %label Autocomplete entity.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Autocomplete entity.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.autocomplete_entity.canonical', ['autocomplete_entity' => $entity->id()]);
  }

}
