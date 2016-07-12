<?php

namespace Drupal\nvli_annotation_services;

use Solarium\Client;


/**
 * Class AddAnnotationService.
 *
 * @package Drupal\nvli_annotation_services
 */
class AddAnnotationService implements AddAnnotationServiceInterface {

  /**
   * Constructor.
   */
  public function __construct() {

  }

  public function addAnnotation($server, $id, $fields) {
    $backend_config = \Drupal::config('search_api.server.' . $server)
      ->get('backend_config');
    $old_doc = \Drupal::service('custom_solr_search.search')
      ->basicSearch('id:' . $id, 0, 1, $server);

    foreach ($fields as $key => $value) {
      $diff = array_diff($value, $old_doc[0]->$key);
      if ($diff) {
        $addFields[$key] = $diff;
      }
    }
    $client = new Client();
    $client->createEndpoint($backend_config + ['key' => 'core'], TRUE);
    // get an update query instance
    $update = $client->createUpdate();
    // create a new document for the data
    $doc = $update->createDocument();
    $doc->setKey('id');
    $doc->setField('id', $id);
    foreach ($addFields as $key => $field) {
      $doc->setField($key, $field, NULL, 'add');
    }
    $update->addDocuments(array($doc));
    $update->addCommit(TRUE);
    $result = $client->update($update);
    return $result;
  }

}
