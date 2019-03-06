<?php

namespace Drupal\autocomplete\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Product entities.
 *
 * @ingroup autocomplete
 */
interface ProductEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Product entity name.
   *
   * @return string
   *   Name of the Product entity.
   */
  /**
   * Add get/set methods for your configuration properties here.
   */
  public function getName();

  /**
   * Sets the Product entity name.
   *
   * @param string $name
   *   The Product entity name.
   *
   * @return \Drupal\autocomplete\Entity\ProductEntityInterface
   *   The called Product entity entity.
   */
  public function setName($name);

  /**
   * Gets the Product entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Product entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Product entity creation timestamp.
   *
   * @param int $timestamp
   *   The Product entity creation timestamp.
   *
   * @return \Drupal\autocomplete\Entity\ProductEntityInterface
   *   The called Product entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Product entity published status indicator.
   *
   * Unpublished Product entity are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Product entity is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Product entity.
   *
   * @param bool $published
   *   TRUE to set this Product entity to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\autocomplete\Entity\ProductEntityInterface
   *   The called Product entity entity.
   */
  public function setPublished($published);

  /**
   * Gets the Product entity sku.
   *
   * @return string
   *   Sku of the Product entity.
   */
  public function getSku();

  /**
   * Sets the Product entity sku.
   *
   * @param string $sku
   *   The Product entity sku.
   *
   * @return \Drupal\autocomplete\Entity\ProductEntityInterface
   *   The called Product entity entity.
   */
  public function setSku($sku);

  /**
   * Gets the Product entity type.
   *
   * @return string
   *   Type of the Product entity.
   */
  public function getType();

  /**
   * Sets the Product entity type.
   *
   * @param string $type
   *   The Product entity type.
   *
   * @return \Drupal\autocomplete\Entity\ProductEntityInterface
   *   The called Product entity entity.
   */
  public function setType($type);

  /**
   * Gets the Product entity price.
   *
   * @return string
   *   Price of the Product entity.
   */
  public function getPrice();

  /**
   * Sets the Product entity price.
   *
   * @param string $price
   *   The Product entity price.
   *
   * @return \Drupal\autocomplete\Entity\ProductEntityInterface
   *   The called Product entity entity.
   */
  public function setPrice($price);

  /**
   * Gets the Product entity upc.
   *
   * @return string
   *   Upc of the Product entity.
   */
  public function getUpc();

  /**
   * Sets the Product entity upc.
   *
   * @param string $upc
   *   The Product entity upc.
   *
   * @return \Drupal\autocomplete\Entity\ProductEntityInterface
   *   The called Product entity entity.
   */
  public function setUpc($upc);

  /**
   * Gets the Product entity categories.
   *
   * @return string
   *   Categories of the Product entity.
   */
  public function getCategories();

  /**
   * Sets the Product entity categories.
   *
   * @param string $categories
   *   The Product entity categories.
   *
   * @return \Drupal\autocomplete\Entity\ProductEntityInterface
   *   The called Product entity entity.
   */
  public function setCategories($categories);

  /**
   * Gets the Product entity shipping.
   *
   * @return string
   *   Shipping of the Product entity.
   */
  public function getShipping();

  /**
   * Sets the Product entity shipping.
   *
   * @param string $shipping
   *   The Product entity shipping.
   *
   * @return \Drupal\autocomplete\Entity\ProductEntityInterface
   *   The called Product entity entity.
   */
  public function setShipping($shipping);

  /**
   * Gets the Product entity description.
   *
   * @return string
   *   Description of the Product entity.
   */
  public function getDescription();

  /**
   * Sets the Product entity description.
   *
   * @param string $description
   *   The Product entity description.
   *
   * @return \Drupal\autocomplete\Entity\ProductEntityInterface
   *   The called Product entity entity.
   */
  public function setDescription($description);

  /**
   * Gets the Product entity manufacturer.
   *
   * @return string
   *   Manufacturer of the Product entity.
   */
  public function getManufacturer();

  /**
   * Sets the Product entity manufacturer.
   *
   * @param string $manufacturer
   *   The Product entity manufacturer.
   *
   * @return \Drupal\autocomplete\Entity\ProductEntityInterface
   *   The called Product entity entity.
   */
  public function setManufacturer($manufacturer);

  /**
   * Gets the Product entity model.
   *
   * @return string
   *   Model of the Product entity.
   */
  public function getModel();

  /**
   * Sets the Product entity model.
   *
   * @param string $model
   *   The Product entity model.
   *
   * @return \Drupal\autocomplete\Entity\ProductEntityInterface
   *   The called Product entity entity.
   */
  public function setModel($model);

  /**
   * Gets the Product entity url.
   *
   * @return string
   *   Url of the Product entity.
   */
  public function getUrl();

  /**
   * Sets the Product entity url.
   *
   * @param string $url
   *   The Product entity url.
   *
   * @return \Drupal\autocomplete\Entity\ProductEntityInterface
   *   The called Product entity entity.
   */
  public function setUrl($url);

  /**
   * Gets the Product entity image.
   *
   * @return string
   *   Image of the Product entity.
   */
  public function getImage();

  /**
   * Sets the Product entity image.
   *
   * @param string $image
   *   The Product entity image.
   *
   * @return \Drupal\autocomplete\Entity\ProductEntityInterface
   *   The called Product entity entity.
   */
  public function setImage($image);

}
