<?php

namespace Drupal\autocomplete\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Category entities.
 *
 * @ingroup autocomplete
 */
interface CategoryEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.
  /**
   * Gets the Category entity name.
   *
   * @return string
   *   Name of the Category entity.
   */
  public function getName();

  /**
   * Sets the Category entity name.
   *
   * @param string $name
   *   The Category entity name.
   *
   * @return \Drupal\autocomplete\Entity\CategoryEntityInterface
   *   The called Category entity entity.
   */
  public function setName($name);

  /**
   * Gets the Category entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Category entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Category entity creation timestamp.
   *
   * @param int $timestamp
   *   The Category entity creation timestamp.
   *
   * @return \Drupal\autocomplete\Entity\CategoryEntityInterface
   *   The called Category entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Category entity published status indicator.
   *
   * Unpublished Category entity are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Category entity is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Category entity.
   *
   * @param bool $published
   *   TRUE to set this Category entity to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\autocomplete\Entity\CategoryEntityInterface
   *   The called Category entity entity.
   */
  public function setPublished($published);

}
