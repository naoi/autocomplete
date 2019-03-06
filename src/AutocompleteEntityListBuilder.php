<?php

namespace Drupal\autocomplete;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Autocomplete entities.
 *
 * @ingroup autocomplete
 */
class AutocompleteEntityListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Autocomplete entity ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\autocomplete\Entity\AutocompleteEntity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.autocomplete_entity.edit_form',
      ['autocomplete_entity' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
