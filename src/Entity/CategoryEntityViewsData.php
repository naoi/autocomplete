<?php

namespace Drupal\autocomplete\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Category entities.
 */
class CategoryEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
