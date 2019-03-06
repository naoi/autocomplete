<?php

namespace Drupal\autocomplete;

// Updated by yas 2018/10/15.
// Updated by yas 2018/10/14.
// Created by yas 2018/10/08.

class GCP {

  // Google Cloud SQL.
  const CLOUD_SQL = 0;

  // Google Cloud Datastore.
  const CLOUD_DATASTORE = 1;

  // Google BigQuery.
  const BIGQUERY = 2;

  // Storage Names.
  const STORAGE_NAME = [
    self::CLOUD_SQL       => 'Cloud SQL',
    self::CLOUD_DATASTORE => 'Cloud Datastore',
    self::BIGQUERY        => 'BigQuery',
  ];

}