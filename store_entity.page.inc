<?php

/**
 * @file
 * Contains store_entity.page.inc.
 *
 * Page callback for Store entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Store entity templates.
 *
 * Default template: store_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_store_entity(array &$variables) {
  // Fetch StoreEntity Entity Object.
  // $store_entity = $variables['elements']['#store_entity'];
  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
