<?php

namespace Drupal\autocomplete\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Autocomplete entities.
 *
 * @ingroup autocomplete
 */
interface AutocompleteEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Autocomplete entity name.
   *
   * @return string
   *   Name of the Autocomplete entity.
   */
  // Add get/set methods for your configuration properties here.
  public function getName();

  /**
   * Sets the Autocomplete entity name.
   *
   * @param string $name
   *   The Autocomplete entity name.
   *
   * @return \Drupal\autocomplete\Entity\AutocompleteEntityInterface
   *   The called Autocomplete entity entity.
   */
  public function setName($name);

  /**
   * Gets the Autocomplete entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Autocomplete entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Autocomplete entity creation timestamp.
   *
   * @param int $timestamp
   *   The Autocomplete entity creation timestamp.
   *
   * @return \Drupal\autocomplete\Entity\AutocompleteEntityInterface
   *   The called Autocomplete entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Autocomplete entity published status indicator.
   *
   * Unpublished Autocomplete entity are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Autocomplete entity is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Autocomplete entity.
   *
   * @param bool $published
   *   TRUE to set this Autocomplete entity to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\autocomplete\Entity\AutocompleteEntityInterface
   *   The called Autocomplete entity entity.
   */
  public function setPublished($published);

}
