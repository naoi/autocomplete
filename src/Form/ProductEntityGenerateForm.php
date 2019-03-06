<?php

namespace Drupal\autocomplete\Form;

// Updated by yas 2018/10/30.
// Updated by yas 2018/10/23.
// Updated by yas 2018/10/21.
// Updated by yas 2018/10/18.
// Updated by yas 2018/10/16.
// Updated by yas 2018/10/15.
// Updated by yas 2018/10/14.
// Updated by yas 2018/10/13.
// Updated by yas 2018/10/10.
// Updated by yas 2018/10/09.
// Updated by yas 2018/10/08.
// Updated by yas 2018/10/07.
// Updated by yas 2018/10/06.
// Updated by yas 2018/10/05.
// Created by yas 2018/10/04.
// Google Cloud Services.
$AUTOLOAD_PHP = DRUPAL_ROOT . '/vendor/autoload.php';
if (file_exists($AUTOLOAD_PHP)) {
  require_once $AUTOLOAD_PHP;
}

$AUTOLOAD_PHP = DRUPAL_ROOT . '/../vendor/autoload.php';
if (file_exists($AUTOLOAD_PHP)) {
  require_once $AUTOLOAD_PHP;
}

use Google\Cloud\ServiceBuilder;
use Google\Cloud\Core\ExponentialBackoff;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Component\Utility\Unicode;
use Drupal\autocomplete\GCP;
use Drupal\autocomplete\Entity\ProductEntity;

/**
 * ProductEntityGenerateForm class.
 */
class ProductEntityGenerateForm extends FormBase {

  private $config = NULL;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'product_entity_generate_form';
  }

  /**
   * {@inheritdoc}
   *
   * @todo, displays last backup timestamp
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $count[GCP::CLOUD_SQL] = 0;
    $count[GCP::CLOUD_DATASTORE] = 0;
    $count[GCP::BIGQUERY] = 0;

    // The # of record count in Cloud SQL.
    $query = \Drupal::entityQuery('product_entity')
           ->count();  // Count.
    $count[GCP::CLOUD_SQL] = $query->execute();

    $config = \Drupal::config('autocomplete.admin_settings');
    $conf['GCP_KEY_FILE_PATH']            = $config->get('GCP_KEY_FILE_PATH');
    $conf['GCP_PROJECT_ID']               = $config->get('GCP_PROJECT_ID');
    $conf['GCP_CLOUD_DATASTORE_KIND']     = $config->get('GCP_CLOUD_DATASTORE_KIND');
    $conf['GCP_BIGQUERY_DATASET']         = $config->get('GCP_BIGQUERY_DATASET');
    $conf['GCP_BIGQUERY_TABLE']           = $config->get('GCP_BIGQUERY_TABLE');
    $conf['PRODUCTS_RECORD_CREATE_TOTAL'] = $config->get('PRODUCTS_RECORD_CREATE_TOTAL');
    $conf['PRODUCTS_COUNT_LIMIT']         = $config->get('PRODUCTS_COUNT_LIMIT');

    if (!file_exists($conf['GCP_KEY_FILE_PATH'])) {
      $error_message = $this->t("Couldn't find GCP Keyfie.");
      drupal_set_message($error_message, 'error');
      return new RedirectResponse(\Drupal\Core\Url::fromRoute('autocomplete.admin_settings')->toString());
    }

    // Instantiates a client.
    $cloud = new ServiceBuilder([
      'keyFilePath' => $conf['GCP_KEY_FILE_PATH'],
      'projectId'   => $conf['GCP_PROJECT_ID'],
    ]);

    // The # of record count in Cloud Datastore.
    $datastore = $cloud->datastore();
    $query = $datastore->query()->kind('__Stat_Kind__')
                       ->filter('kind_name', '=', $conf['GCP_CLOUD_DATASTORE_KIND']);
    $results = $datastore->runQuery($query);
    foreach ($results as $result) {
      $count[GCP::CLOUD_DATASTORE] = $result['count'];
    }

// _OR_ The following code works.
/**
    $query = $datastore->gqlQuery('SELECT count FROM __Stat_Kind__');
    $results = $datastore->runQuery($query);
    foreach ($results as $result) {
      $path = $result->key()->path();
      if ($path[0]['name'] !== $conf['GCP_CLOUD_DATASTORE_KIND']) {
        continue;
      }
      $count[GCP::CLOUD_DATASTORE] = $result['count'];
    }
*/

    // The # of record count in BigQuery.
    $project_id = $conf['GCP_PROJECT_ID'];
    $dataset    = $conf['GCP_BIGQUERY_DATASET'];
    $table      = $conf['GCP_BIGQUERY_TABLE'];
    $query      = "SELECT COUNT(*) as total FROM `$project_id.$dataset.$table`";

    $bigQuery   = $cloud->bigQuery();
    $job_config = $bigQuery->query($query)->useLegacySql(FALSE);
    $job        = $bigQuery->startQuery($job_config);

    $backoff = new ExponentialBackoff(20);  // Max retries
    $backoff->execute(function () use ($job, $query) {
      $job->reload();
      if (!$job->isComplete()) {
        \Drupal::logger('autocomplete')->error("BigQuery Error - Job has NOT yet completed: $query");
        throw new \Exception('Job has not yet completed', 500);
      }
    });
    $query_results = $job->queryResults();

    if ($query_results->isComplete()) {
      $results = $query_results->rows();
      foreach ($results as $result) {
        $count[GCP::BIGQUERY] = $result['total'];
      }
    }
    else {
      \Drupal::logger('autocomplete')->error("BigQuery Error - The query failed to complete: $query");
      throw new \Exception('The query failed to complete');

    }

    $form['total'] = [
      '#type' => 'details',
      '#title' => t('Statistics'),
      '#open' => TRUE,
    ];

    $form['total']['cloud_sql_count'] = [
      '#type' => 'item',
      '#markup' => 'Cloud SQL: <strong>' . number_format($count[GCP::CLOUD_SQL]) . '</strong>',
    ];

    $form['total']['cloud_datastore'] = [
      '#type' => 'item',
      '#markup' => 'Cloud Datastore: <strong>' . number_format($count[GCP::CLOUD_DATASTORE]) . '</strong>',
    ];

    $form['total']['bigquery_count'] = [
      '#type' => 'item',
      '#markup' => 'BigQuery: <strong>' . number_format($count[GCP::BIGQUERY]) . '</strong>',
    ];

    $form['db_settings'] = [
      '#type' => 'details',
      '#title' => t('Database Settings'),
      '#open' => TRUE,
    ];

    $form['db_settings']['products_count_limit'] = [
      '#type' => 'item',
      '#markup' => 'Max limit of Products database record count <= <strong>' . number_format($conf['PRODUCTS_COUNT_LIMIT']) . '</strong>',
    ];

    $form['db_settings']['offset'] = [
      '#title' => $this->t('The offset of start record'),
      '#type' => 'textfield',
      '#description' => $this->t('The offset of start record.'),
      '#default_value' => '0',
      '#maxlength' => 7,
      '#size' => 7,
    ];

    $form['db_settings']['total'] = [
      '#title' => $this->t('The number of data to generate'),
      '#type' => 'textfield',
      '#description' => $this->t('The total number of the additional data to generate.'),
      '#default_value' => number_format($conf['PRODUCTS_RECORD_CREATE_TOTAL']),
      '#maxlength' => 7,
      '#size' => 7,
    ];

    $form['db_settings']['storage_type'] = [
      '#title' => $this->t('Storage'),
      '#type' => 'radios',
      '#description' => $this->t('Select the Cloud Datastore.'),
      '#options' => [
        'Cloud SQL - MySQL (RDB)', // 0.
        'Cloud Datastore (NoSQL)', // 1.
        'BigQuery'               , // 2.
      ],
    ];

    for ($i = 0; $i < count($count); $i++) {
      $form['db_settings']['storage_type'][$i] = [
        '#disabled' => FALSE,
      ];
      if ($count[$i] < $conf['PRODUCTS_COUNT_LIMIT']) {
        continue;
      }
      $form['db_settings']['storage_type'][$i] = [
        '#disabled' => TRUE,
      ];
    }

    $form['db_settings']['storage_type']['#default_value'] = GCP::CLOUD_SQL;
    for ($i = 0; $i < count($count); $i++) {
      if ($count[$i] < $conf['PRODUCTS_COUNT_LIMIT']) {
        $form['db_settings']['storage_type']['#default_value'] = $i;
      }
    }

    $form['db_settings']['is_new'] = [
      '#title' => $this->t('Create from new data'),
      '#type' => 'checkbox',
      '#description' => $this->t('Select the Cloud Datastore.'),
      '#default_value' => $form['db_settings']['storage_type']['#default_value'] === GCP::CLOUD_SQL
                                                                    ? TRUE : FALSE,
      '#disabled' => $form['db_settings']['storage_type'][GCP::CLOUD_DATASTORE]
                  || $form['db_settings']['storage_type'][GCP::BIGQUERY]
                  ?: FALSE,
    ];

    $form['actions']['#type'] = 'actions';
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Generate'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = \Drupal::config('autocomplete.admin_settings');
    $conf['GCP_KEY_FILE_PATH']        = $config->get('GCP_KEY_FILE_PATH');
    $conf['GCP_PROJECT_ID']           = $config->get('GCP_PROJECT_ID');
    $conf['GCP_CLOUD_DATASTORE_KIND'] = $config->get('GCP_CLOUD_DATASTORE_KIND');
    $conf['GCP_BIGQUERY_DATASET']     = $config->get('GCP_BIGQUERY_DATASET');
    $conf['GCP_BIGQUERY_TABLE']       = $config->get('GCP_BIGQUERY_TABLE');
    $conf['PRODUCTS_JSON_URL']        = $config->get('PRODUCTS_JSON_URL');

    try {

      $storage_type = $form['db_settings']['storage_type']['#value'];
      $is_new = !empty($form['db_settings']['is_new']['#value']);
      $operations = [];
      $last_row = 0;

      // If there are no data.
      if ($is_new) {
        $message = $this->t('@storage_name - Product data will be imported from products.json into Product Entities.', [
          '@storage_name' => GCP::STORAGE_NAME[$storage_type],
        ]);
        \Drupal::logger('autocomplete')->notice($message);
        $json = file_get_contents($conf['PRODUCTS_JSON_URL']);
        $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $results = json_decode($json, TRUE);
        $last_row = count($results);

      }
      else {

        $offset = preg_replace('/[^0-9]/', '', $form['db_settings']['offset']['#value'] ?: 0);
        $total  = preg_replace('/[^0-9]/', '', $form['db_settings']['total']['#value']);
        $database = db_select('product_entity');
        $query    = $database->range($offset, $total);
        $query    = $query->fields('product_entity', ['id']);
        $results  = $query->execute()->fetchAll();
        $last_row = count($results);

        $message = $this->t('@storage_name - All entities mark for re-generate: @count records (offset: @offset)', [
          '@storage_name' => GCP::STORAGE_NAME[$storage_type],
          '@count' => number_format($last_row),
          '@offset' => number_format($offset),
        ]);
        \Drupal::logger('autocomplete')->notice($message);
      }

      // Create batch of 1000 nodes.
      $count = 1;
      $items = [];
      foreach ($results as $row) {
        // JSON Imported data.
        if ($is_new) {
          $items[] = $row;

        }
        // Cloud SQL.
        else {
          $items[] = $row->id;
        }

        if ($count % 1000 === 0 || $count === $last_row) {
          $operations[] = [
            [
              get_class($this),
              'processBatch',
            ], [
              $items, GCP::STORAGE_NAME[$storage_type], $storage_type, $is_new
            ],
          ];
          $items = [];
        }
        ++$count;
      }

      // Set up the Batch API.
      $batch = [
        'operations' => $operations,
        'finished' => [get_class($this), 'productEntityGenerateFinishedBatch'],
        'title' => $this->t('Product Entity Generator - Batch Processing (@storageName)', [
          '@storageName' => GCP::STORAGE_NAME[$storage_type],
        ]),

        'init_message' => $this->t('Starting Product Entity Generation for @storageName...', [
          '@storageName' => GCP::STORAGE_NAME[$storage_type],
        ]),

        'progress_message' => $this->t('@storageName: Completed @current step of @total.', [
          '@storageName' => GCP::STORAGE_NAME[$storage_type],
        ]),

        'error_message' => $this->t('Product Entity Generate has encountered an error (@storageName).', [
          '@storageName' => GCP::STORAGE_NAME[$storage_type],
        ]),
      ];

      batch_set($batch);
    }
    catch (Exception $e) {
      foreach ($e->getErrors() as $error_message) {
        drupal_set_message($error_message, 'error');
      }
    }
  }

  /**
   * Processes the Product Entity Generate.
   *
   * @param array $ids
   *   Product id.
   * @param array $context
   *   The batch context.
   */
  public static function processBatch(array $items, $storage_name, $storage_type, $is_new, array &$context) {

    $start_time = microtime(TRUE);

    $config = \Drupal::config('autocomplete.admin_settings');
    $conf['GCP_KEY_FILE_PATH']        = $config->get('GCP_KEY_FILE_PATH');
    $conf['GCP_PROJECT_ID']           = $config->get('GCP_PROJECT_ID');
    $conf['GCP_CLOUD_DATASTORE_KIND'] = $config->get('GCP_CLOUD_DATASTORE_KIND');
    $conf['GCP_BIGQUERY_DATASET']     = $config->get('GCP_BIGQUERY_DATASET');
    $conf['GCP_BIGQUERY_TABLE']       = $config->get('GCP_BIGQUERY_TABLE');

    $key = 'Product Entity Generator';
    $time = time();
    $hash_mac = hash_hmac('sha256', $time, $key);
    $hash = !$is_new ? ' ' . Unicode::strtoupper(substr($hash_mac, 0, 5)) : '';
    $cloud = NULL;
    $storage = NULL;

    // Cloud Datastore or BigQuery.
    if ($storage_type) {
      // Instantiates a client.
      $cloud = new ServiceBuilder([
        'keyFilePath' => $conf['GCP_KEY_FILE_PATH'],
        'projectId'   => $conf['GCP_PROJECT_ID'],
      ]);

      // Cloud Datastore.
      switch ($storage_type) {
        case GCP::CLOUD_DATASTORE:
          $storage = $cloud->datastore();
          break;

        case GCP::BIGQUERY:
          $storage = $cloud->bigQuery();
          break;

        default:
          break;
      }  // End of switch.
    } // End of if.
    // Cloud Datastore or Cloud SQL.
    foreach ($items as $item) {

      // Import data from JSON file or Datastore.
      if ($is_new) {
        $properties = [
          'sku'          => $item['sku'] ?: $hash,
          'name'         => $item['name'] . $hash,
          'type'         => $item['type'],
          'price'        => $item['price'],
          'upc'          => $item['upc'],
          'categories'   => $storage_type === GCP::BIGQUERY
                                            ? "dummy $hash"
                                            : $storage_type === GCP::CLOUD_SQL
                                            ? $item['category']
                                            : '',
          'shipping'     => $item['shipping'] ?: 0.00,
          // 'description' => $product['description'],
          'description'  => 'dummy',
          'manufacturer' => $item['manufacturer'],
          'model'        => $item['model'],
          'url'          => $item['url'],
          'image'        => $item['image'],
        ];

      }
      // Re-use data from storage.
      else {
        $product = ProductEntity::load($item);
        if (empty($product)) {
          continue;
        }

        $properties = [
          'id'               => $product->id(),
          'langcode'         => $product->language()->getId(),
          'user_id'          => $product->getOwnerId(),
          'status'           => $product->isPublished(),
          'created'          => $product->getCreatedTime(),
          'changed'          => $product->getChangedTime(),
          'default_language' => \Drupal::service('language.default')
                                        ->get()->getId(),
          'upc'              => $product->getUpc(),
          'categories'       => $storage_type === GCP::BIGQUERY
                                                ? "dummy $hash"
                                                : $product->getCategories(),
          'shipping'         => $product->getShipping() ?: 0.00,
          'manufacturer'     => $product->getManufacturer(),
          'model'            => $product->getModel(),
          'description'      => $product->getDescription(),
          'url'              => $product->getUrl(),
          'image'            => $product->getImage(),
          'name'             => $product->getName() . $hash,
          'sku'              => $product->getSku() ?: $hash,
          'type'             => $product->getType(),
          'price'            => $product->getPrice(),
        ];
      }

      // Store/Save data.
      switch ($storage_type) {

        case GCP::CLOUD_DATASTORE:  // Cloud Datastore.
          $taskKey = $storage->key($conf['GCP_CLOUD_DATASTORE_KIND']);  // The Cloud Datastore key for the new entity.
          $task = $storage->entity($taskKey, $properties);     // The name/ID for the new entity.
          $storage->upsert($task);                             // Saves the entity.
          break;

        // Cloud SQL.
        case GCP::CLOUD_SQL:
          $product_entity = ProductEntity::create($properties);
          $product_entity->save();
          break;

        // BigQuery.
        case GCP::BIGQUERY:
          $dataset = $storage->dataset($conf['GCP_BIGQUERY_DATASET']);
          $table = $dataset->table($conf['GCP_BIGQUERY_TABLE']);
          $insertResponse = $table->insertRow($properties);

          if (!$insertResponse->isSuccessful()) {
            $message = '';
            foreach ($insertResponse->failedRows() as $row) {
              $message .= serialize($row['rowData']);
              foreach ($row['errors'] as $error) {
                $message .= serialize($error['reason'] . ': ' . $error['message']);
              }
            }
            \Drupal::logger('autocomplete')->error("Error: $message");
          }
          break;

        default:
          break;
      } // End of switch.
    }

    // Performance measurement.
    $count = count($items);
    $diff = microtime(TRUE) - $start_time;
    $processing_time = gmdate('i:s', $diff);
    $processing_time_per_record = sprintf('%.3f', $diff / $count);
    \Drupal::logger('autocomplete')->notice(
      t('@storageName - Processing Time: @processing_time_per_record sec / record '
        . '(Total: @processing_time / @count record) - ProductEntityGenerateForm::processBatch', [
        '@storageName'                => $storage_name,
        '@processing_time_per_record' => $processing_time_per_record,
        '@processing_time'            => $processing_time,
        '@count'                      => number_format($count),
      ])
    );

  }

  /**
   * Batch finish function.
   *
   * This function is called by the batch 'finished' parameter.
   * The cache must not be cleared as the last batch operation,
   * but after the batch is finished.
   *
   * @param bool $success
   *   Indicates if the batch was successfully finished.
   * @param array $results
   *   The value of the results item from the context variable used in the batch
   *   processing.
   * @param array $operations
   *   If the success parameter is false then this is a list of the operations
   *   that haven't completed yet.
   */
  public static function productEntityGenerateFinishedBatch($success, array $results, array $operations) {
    drupal_flush_all_caches();
    $message = $success ? t('Product Entity Generator performed successfully.')
                        : t('Product Entity Generator has NOT been finished successfully.');
    \Drupal::logger('autocomplete')->notice($message);
    drupal_set_message($message);
  }

}
