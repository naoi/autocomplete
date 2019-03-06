/**
 * @file
 * Extends autocomplete based on jQuery UI.
 *
 */

// Updated by yas 2018/11/07.
// Updated by yas 2018/10/31.
// Updated by yas 2018/10/29.
// Updated by yas 2018/10/22.
// Created by yas 2018/10/21.
(function ($, Drupal) {

  'use strict';

  var STORAGE_TYPES = {
    '#cloud_sql'      : {source: drupalSettings.autocomplete.autocomplete.cloud_functions.cloud_sql      },
    '#cloud_datastore': {source: drupalSettings.autocomplete.autocomplete.cloud_functions.cloud_datastore},
    '#bigquery'       : {source: drupalSettings.autocomplete.autocomplete.cloud_functions.bigquery       },
  };

  // (a) put default input focus on the state field
  $(document).ready(function(){
    $('#cloud_sql').focus();
  });

  // (b) jquery ajax autocomplete implementation
  $(document).ready(function() {
    // tell the autocomplete function to get its data from our php script
    Object.keys(STORAGE_TYPES).forEach(function(value) {
      $(value).autocomplete(this[value]);
    }, STORAGE_TYPES)
  });
})(jQuery, Drupal, drupalSettings);
