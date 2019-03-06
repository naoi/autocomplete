<?php

namespace Drupal\autocomplete\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Store entities.
 *
 * @ingroup autocomplete
 */
interface StoreEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.
  /**
   * Gets the Store entity name.
   *
   * @return string
   *   Name of the Store entity.
   */
  public function getName();

  /**
   * Sets the Store entity name.
   *
   * @param string $name
   *   The Store entity name.
   *
   * @return \Drupal\autocomplete\Entity\StoreEntityInterface
   *   The called Store entity entity.
   */
  public function setName($name);

  /**
   * Gets the Store entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Store entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Store entity creation timestamp.
   *
   * @param int $timestamp
   *   The Store entity creation timestamp.
   *
   * @return \Drupal\autocomplete\Entity\StoreEntityInterface
   *   The called Store entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Store entity published status indicator.
   *
   * Unpublished Store entity are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Store entity is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Store entity.
   *
   * @param bool $published
   *   TRUE to set this Store entity to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\autocomplete\Entity\StoreEntityInterface
   *   The called Store entity entity.
   */
  public function setPublished($published);

}
