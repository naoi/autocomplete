<?php

/**
 * @file
 * Contains category_entity.page.inc.
 *
 * Page callback for Category entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Category entity templates.
 *
 * Default template: category_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_category_entity(array &$variables) {
  // Fetch CategoryEntity Entity Object.
  // $category_entity = $variables['elements']['#category_entity'];
  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
