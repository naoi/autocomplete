<?php

namespace Drupal\autocomplete\Form;

// Updated by yas 2018/11/06.
// Updated by yas 2018/10/29.
// Updated by yas 2018/10/28.
// Updated by yas 2018/10/23.
// Updated by yas 2018/10/21.
// Updated by yas 2018/10/16.
// Updated by yas 2018/10/14.
// Updated by yas 2018/10/10.
// Created by yas 2018/10/08.
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;

/**
 * Class Autocomplete Admin Settings Form.
 */
class AutocompleteAdminSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'autocomplete_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['autocomplete.admin_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = \Drupal::config('autocomplete.admin_settings');

    $form['GCP'] = [
      '#type' => 'details',
      '#title' => $this->t('GCP'),
      '#open' => TRUE,
    ];

    $form['GCP']['GCP_KEY_FILE_PATH'] = [
      '#type' => 'textfield',
      '#title' => $this->t('GCP Keyfile Full Path and Filename'),
      '#default_value' => $config->get('GCP_KEY_FILE_PATH'),
      '#description' => $this->t('GCP Key File Path. e.g /home/yourname/&lt;YOUR_PROJECT-ID&gt;.json'),
      '#required' => TRUE,
    ];

    $form['GCP']['GCP_PROJECT_ID'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cloud Datastore Project ID'),
      '#default_value' => $config->get('GCP_PROJECT_ID'),
      '#description' => $this->t('Cloud Datastore Project ID.'),
      '#required' => TRUE,
    ];

    $form['GCP']['GCP_CLOUD_DATASTORE_KIND'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cloud Datastore Kind'),
      '#default_value' => $config->get('GCP_CLOUD_DATASTORE_KIND'),
      '#description' => $this->t('Cloud Datastore Kind.'),
      '#required' => TRUE,
    ];

    $form['GCP']['GCP_BIGQUERY_DATASET'] = [
      '#type' => 'textfield',
      '#title' => $this->t('BigQuery Dataset'),
      '#default_value' => $config->get('GCP_BIGQUERY_DATASET'),
      '#description' => $this->t('BigQuery Dataset.'),
      '#required' => TRUE,
    ];

    $form['GCP']['GCP_BIGQUERY_TABLE'] = [
      '#type' => 'textfield',
      '#title' => $this->t('BigQuery Table'),
      '#default_value' => $config->get('GCP_BIGQUERY_TABLE'),
      '#description' => $this->t('BigQuery Table.'),
      '#required' => TRUE,
    ];

    $form['GCP']['GCP_CLOUD_FUNCTIONS_CLOUD_SQL_URL'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cloud Functions Trigger Cloud SQL Endpoint URL'),
      '#default_value' => $config->get('GCP_CLOUD_FUNCTIONS_CLOUD_SQL_URL'),
      '#description' => $this->t('Cloud Functions Trigger Cloud SQL Endpoint URL.'),
      '#required' => TRUE,
    ];

    $form['GCP']['GCP_CLOUD_FUNCTIONS_CLOUD_DATASTORE_URL'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cloud Functions Trigger Cloud Datastore Endpoint URL'),
      '#default_value' => $config->get('GCP_CLOUD_FUNCTIONS_CLOUD_DATASTORE_URL'),
      '#description' => $this->t('Cloud Functions Trigger Cloud Datastore Endpoint URL.'),
      '#required' => TRUE,
    ];

    $form['GCP']['GCP_CLOUD_FUNCTIONS_BIGQUERY_URL'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Cloud Functions Trigger Bigquery Endpoint URL'),
      '#default_value' => $config->get('GCP_CLOUD_FUNCTIONS_BIGQUERY_URL'),
      '#description' => $this->t('Cloud Functions Trigger Bigquery Endpoint URL.'),
      '#required' => TRUE,
    ];

    $form['DATABASE'] = [
      '#type' => 'details',
      '#title' => $this->t('Database'),
      '#open' => TRUE,
    ];

    $form['DATABASE']['AUTOCOMPLETE_RESULT_MAX_COUNT'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Autocomplete Result Max Count'),
      '#default_value' => $config->get('AUTOCOMPLETE_RESULT_MAX_COUNT'),
      '#description' => $this->t('Autocomplete Result Max Count.'),
      '#required' => TRUE,
    ];

    $form['DATABASE']['PRODUCTS_RECORD_CREATE_TOTAL'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Products Create Record Total Count'),
      '#default_value' => number_format($config->get('PRODUCTS_RECORD_CREATE_TOTAL')),
      '#description' => $this->t('Products Create Record Total Count.'),
      '#required' => TRUE,
    ];

    $form['DATABASE']['PRODUCTS_COUNT_LIMIT'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Product Data Count Limit'),
      '#default_value' => $config->get('PRODUCTS_COUNT_LIMIT'),
      '#description' => $this->t('Products data count limit.'),
      '#required' => TRUE,
    ];

    $form['DATABASE']['PRODUCTS_JSON_URL'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Projects JSON URL'),
      '#default_value' => $config->get('PRODUCTS_JSON_URL'),
      '#description' => $this->t('Products JSON URL.'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = \Drupal::configFactory()->getEditable('autocomplete.admin_settings');
    $form_state->cleanValues();

    if (!file_exists($form_state->getValue('GCP_KEY_FILE_PATH'))) {
      $error_message = $this->t("Couldn't find GCP Keyfie.");
      drupal_set_message($error_message, 'error');
    }

    foreach ($form_state->getValues() as $key => $value) {
      if ($key === 'PRODUCTS_RECORD_CREATE_TOTAL') {
        $value = str_replace(',', '', $value);
      }
      $config->set($key, Html::escape($value));
    }
    $config->save();
  }

}
