<?php

namespace Drupal\autocomplete\Controller;

// Updated by yas 2018/11/12.
// Updated by yas 2018/10/22.
// Updated by yas 2018/10/21.
// Updated by yas 2018/10/16.
// Updated by yas 2018/10/15.
// Updated by yas 2018/10/14.
// Updated by yas 2018/10/10.
// Updated by yas 2018/10/09.
// Updated by yas 2018/10/08.
// Updated by yas 2018/10/05.
// Created by yas 2018/10/03.
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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Messenger\Messenger;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Component\Utility\Tags;
use Drupal\Component\Utility\Unicode;
use Drupal\autocomplete\GCP;
use Drupal\autocomplete\Entity\ProductEntity;

class ProductEntityController extends ControllerBase {

  private $conf = [];
  private $START_TIME = 0;

  /**
   * The Messenger service.
   *
   * @var \Drupal\Core\Messenger\Messenger
   */
  protected $messenger;

  /**
   * The Logger service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $logger;

  /**
   * The Entity Query service.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entity_query;

  /**
   * @param \Drupal\Core\Entity\Query\QueryFactory $entity_query
   * EntityQuery Object
   * ProductEntityController constructor.
   * @param \Drupal\Core\Messenger\Messenger $messenger
   * Messanger Object
   * @param \Drupal\Core\Logger\LoggerChannel $looger
   * Logger Object
   */
  public function __construct(QueryFactory $entity_query, Messenger $messenger, LoggerChannelFactoryInterface $logger) {

$this->START_TIME = microtime(TRUE);

    $this->entity_query = $entity_query;
    $this->messenger = $messenger;
    $this->logger = $logger;

    $config = \Drupal::config('autocomplete.admin_settings');
    $this->conf['GCP_KEY_FILE_PATH']        = $config->get('GCP_KEY_FILE_PATH');
    $this->conf['GCP_PROJECT_ID']           = $config->get('GCP_PROJECT_ID');
    $this->conf['GCP_CLOUD_DATASTORE_KIND'] = $config->get('GCP_CLOUD_DATASTORE_KIND');
    $this->conf['GCP_BIGQUERY_DATASET']     = $config->get('GCP_BIGQUERY_DATASET');
    $this->conf['GCP_BIGQUERY_TABLE']       = $config->get('GCP_BIGQUERY_TABLE');
    $this->conf['PRODUCTS_JSON_URL']        = $config->get('PRODUCTS_JSON_URL');
    $this->conf['REDIS_HOSTNAME']           = $config->get('REDIS_HOSTNAME');
    $this->conf['REDIS_PORT']               = $config->get('REDIS_PORT');

  }

  /**
   * Dependency Injection.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.query'),
      $container->get('messenger'),
      $container->get('logger.factory')
    );
  }

  /**
   * Import Products data from Json.
   */
  public function importJson() {

    // Validate admin settings.
    $json = file_get_contents($this->conf['PRODUCTS_JSON_URL']);
    $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
    $products = json_decode($json, TRUE);

    foreach ($products as $item) {
      $product = ProductEntity::create([
        'sku'          => $item['sku'],
        'name'         => $item['name'],
        'type'         => $item['type'],
        'price'        => $item['price'],
        'upc'          => $item['upc'],
        'categories'   => $item['category'],
        'shipping'     => $item['shipping'],
        // 'description' => $item['description'],.
        'description'  => 'dummy',
        'manufacturer' => $item['manufacturer'],
        'model'        => $item['model'],
        'url'          => $item['url'],
        'image'        => $item['image'],
      ]);

      $product->save();
    }

    $response = ['status' => 'OK'];

    return new JsonResponse($response);
  }

  /**
   * {@inheritdoc}
   */
  public function handleAutocomplete(Request $request, $storage_type, $count) {

    $matches = [];

    // Get the typed string from the URL, if it exists.
    if (empty($input = $request->query->get('q'))) {
      return new JsonResponse([
        'value' => '',
        'label' => '',
      ]);
    }

    $typed_string = Tags::explode($input);
    $typed_string = Unicode::strtolower(array_pop($typed_string));
    $typed_string = $input;

    // Instantiates a client.
    if ($storage_type !== GCP::CLOUD_SQL) {
      $cloud = new ServiceBuilder([
        'keyFilePath' => $this->conf['GCP_KEY_FILE_PATH'],
        'projectId'   => $this->conf['GCP_PROJECT_ID'],
      ]);
    }

    $results = [];
    $redis = new \Redis();
    $redis->connect($this->conf['REDIS_HOSTNAME'], $this->conf['REDIS_PORT']);
    $redis_key = md5($typed_string);
    // $redis->del($redis_key);

    $cached_results = json_decode($redis->get($redis_key), true);

    if (!empty($cached_results)
    && array_key_exists('label', $cached_results[0])
    && !empty($cached_results[0]['label'])) {

// Performance measurement.
$processing_time = sprintf('%.3f', microtime(TRUE) - $this->START_TIME);
$this->logger->get('autocomplete')->notice(
  $this->t('@storageName: @processing_time sec (cached) [ @typed_string ] => [ @name ] - ProductEntityController::handleAutocomplete', [
    '@storageName'    => GCP::STORAGE_NAME[$storage_type],
    '@processing_time' => $processing_time,
    '@typed_string'    => $typed_string,
    '@name'            => $cached_results[0]['label'],
  ])
);

      return new JsonResponse($cached_results);
    }

    switch ($storage_type) {

      case GCP::CLOUD_SQL:

        $query = $this->entity_query->get('product_entity')
               ->condition('name', "$typed_string%", 'LIKE')
               ->range(0, $count)
               ->sort('name', 'ASC');

        $query_results = $query->execute();

        foreach ($query_results as $id) {
          $product = ProductEntity::load($id);
          $results[] = [
            'key'  => $product->getName(),
            'name' => $product->getName(),
          ];
        }
        break;

      case GCP::CLOUD_DATASTORE:

        $storage = $cloud->datastore();
        $query = $storage->query()
                         ->kind($this->conf['GCP_CLOUD_DATASTORE_KIND'])
                         ->filter('name', '>=', $typed_string)
//                       ->filter('name', '>=', preg_replace('/ /', '\\ ', $typed_string))
//                       ->filter('name', '<', $typed_string . '"\ufffd"')
                         ->limit($count)
                         ->order('name');

/**
//      $query = $storage->gqlQuery('SELECT name FROM Products WHERE name >= @typed_string_1 AND name < @typed_string_2 LIMIT @limit', [
        $query = $storage->gqlQuery('SELECT name FROM Products WHERE name >= @typed_string_1 LIMIT @limit', [
          'bindings' => [
            'typed_string_1' => $typed_string,
//          'typed_string_2' => $typed_string . '"\ufffd",
            'limit' => $count,
          ]
        ]);
*/
        $results = $storage->runQuery($query);
        break;

      case GCP::BIGQUERY:

        // Instantiates a client.
        $storage    = $cloud->bigQuery();
        $project_id = $this->conf['GCP_PROJECT_ID'];
        $dataset    = $this->conf['GCP_BIGQUERY_DATASET'];
        $table      = $this->conf['GCP_BIGQUERY_TABLE'];
        $query = "SELECT `name` FROM `$project_id.$dataset.$table` WHERE `name` LIKE '$typed_string%' ORDER BY `name` ASC LIMIT $count";

        $job_config = $storage->query($query)
                              ->useLegacySql(FALSE);
        $job = $storage->startQuery($job_config);

        $backoff = new ExponentialBackoff(10);  // Max retries
        $backoff->execute(function () use ($job, $query) {
          $job->reload();
          if (!$job->isComplete()) {
            $this->logger->get('autocomplete')->error("BigQuery Error - Job has NOT yet completed: $query");
            throw new \Exception('Job has not yet completed', 500);
          }
        });
        $query_results = $job->queryResults();

        if ($query_results->isComplete()) {
          $results = $query_results->rows();
        }
        else {
          $this->logger->get('autocomplete')->error("BigQuery Error - The query failed to complete: $query");
          throw new \Exception('The query failed to complete', 500);
        }
        break;

      default:
        break;
    }

    // $this->logger->get('autocomplete')->notice("serialize: " . serialize($results));
    foreach ($results as $result) {

      if (empty($result)) {
        return new JsonResponse([
          'value' => 'No results',
          'label' => 'No results',
        ]);
      }

      $matches[] = [
        'value' => $result['name'],
        'label' => $result['name'],
      ];
    }

    if (!empty($matches)) {
      $redis->set($redis_key, json_encode($matches));
    }

    $redis->close();

// Performance measurement.
$processing_time = sprintf('%.3f', microtime(TRUE) - $this->START_TIME);
$this->logger->get('autocomplete')->notice(
  $this->t('@storageName: @processing_time sec [ @typed_string ] => [ @name ] - ProductEntityController::handleAutocomplete', [
    '@storageName'    => GCP::STORAGE_NAME[$storage_type],
    '@processing_time' => $processing_time,
    '@typed_string'    => $typed_string,
    '@name'            => !empty($matches[0]['label'])
                       ?         $matches[0]['label']
                       :  'N/A',
  ])
);
    return new JsonResponse($matches);
  }

}
