<?php

/**
 * @file
 * Contains autocomplete_entity.page.inc.
 *
 * Page callback for Autocomplete entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Autocomplete entity templates.
 *
 * Default template: autocomplete_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_autocomplete_entity(array &$variables) {
  // Fetch AutocompleteEntity Entity Object.
  // :wq$autocomplete_entity = $variables['elements']['#autocomplete_entity'];
  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
