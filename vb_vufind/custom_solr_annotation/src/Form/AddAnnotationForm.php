<?php

namespace Drupal\custom_solr_annotation\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\custom_solr_annotation\Entity\SolrAnnotation;
use Solarium\Client;
use Solarium\QueryType\Update\Query\Document\Document;

/**
 * Class AddAnnotationForm.
 *
 * @package Drupal\custom_solr_annotation\Form
 */
class AddAnnotationForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'add_annotation_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL, $server = NULL, $keyword = NULL) {
    if (!empty($id) && $server == 'all') {
      $result = \Drupal::service('custom_solr_search.search_all')->seachAll('id:' . $id);
      $title = $result[0]->title;
    }
    else {
      $result = \Drupal::service('custom_solr_search.search')
        ->basicSearch('id:' . $id, 0, 1, $server);
      $title = $result[0]->title;
    }

    $form['title'] = array(
      '#type' => 'label',
      '#title' => $this->t($title),
    );
    $form['docid'] = array(
      '#type' => 'hidden',
      '#value' => $id,
    );
    $form['server'] = array(
      '#type' => 'hidden',
      '#value' => $server,
    );
    $form['keyword'] = array(
      '#type' => 'hidden',
      '#value' => $keyword,
    );
    $form['annotation'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Annotation'),
      '#maxlength' => 256,
      '#size' => 256,
    );
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Add Annotation',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $id = $form_state->getValue('docid');
    $connection = Database::getConnection();
    $query = $connection->select('solr_annotation', 'sa')
      ->fields('sa', array('id'))
      ->condition('sa.solr_doc_id', $form_state->getValue('docid'));
    $result = $query->execute()->fetchAssoc();
    if (!empty($result)) {
      $entity = SolrAnnotation::load($result['id']);
      $entity->annotation[] = $form_state->getValue('annotation');
    }
    else {
      $entity = SolrAnnotation::create(
        array(
          'solr_doc_id' => $form_state->getValue('docid'),
          'annotation' => $form_state->getValue('annotation')
        )
      );
    }
   $entity->save();
    $server = $form_state->getValue('server');
    $result = \Drupal::service('custom_solr_search.search')->basicSearch("id:".$id, 0, 1, $server);
//    $doc = new Document((array) $result[0]);
//    $doc->setKey('id');
//    $doc->setField('annotation_txt_mv', $form_state->getValue('annotation'), null, 'set');
//    $doc->removeField('score');
//    $doc->removeField('title_fullStr');
//    $doc->removeField('title_full_unstemmed');


//    ep($form_state->getValue('annotation'));exit;

    //Updating solr index data
    $backend_config = \Drupal::config('search_api.server.' . $server)->get('backend_config');
    $client = new Client();
    $client->createEndpoint($backend_config + ['key' => 'core'], TRUE);
    // get an update query instance
    $update = $client->createUpdate();
    // create a new document for the data
    $doc = $update->createDocument();
    $doc->setKey('id');
    $doc->setField('id', $id);
    $doc->setField('annotation', $form_state->getValue('annotation'), null, 'add');
//    $doc->setField('spelling', "ir-10054-122612011-12-27T05:49:31ZHEAT-TRANSFER IN FLOW PAST A CONTINUOUS MOVING PLATE WITH VARIABLE TEMPERATURESOUNDALGEKAR, VMMURTY, TVRSPRINGER VERLAGTrishul Pandey2011-08-30T09:01:03Z2011-12-26T12:58:52Z2011-12-27T05:49:31Z2011-08-30T09:01:03Z2011-12-26T12:58:52Z2011-12-27T05:49:31Z1980ArticleWARME UND STOFFUBERTRAGUNG-THERMO AND FLUID DYNAMICS, 14(2), 91-930042-9929http://dx.doi.org/10.1007/BF01806474http://dspace.library.iitb.ac.in/xmlui/handle/10054/12261http://hdl.handle.net/10054/12261en", null, 'set');

    // add the documents and a commit command to the update query
    $update->addDocuments(array($doc));
    $update->addCommit(TRUE);
    $customizer = $client->getPlugin('customizerequest');
    $customizer->createCustomization('id')
      ->setType('param')
      ->setName('commitWithin')
      ->setValue('10000');
    // this executes the query and returns the result
    $result = $client->update($update);
    drupal_set_message('Annossstation Added !');
    $form_state->setRedirect('custom_solr_search.basic_search_result', array('server' => $form_state->getValue('server'), 'keyword' => $form_state->getValue('keyword')));
  }

}
