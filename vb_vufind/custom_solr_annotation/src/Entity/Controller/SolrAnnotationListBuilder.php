<?php

/**
 * @file
 * Contains \Drupal\custom_solr_annotation\Entity\Controller\SolrAnnotationListBuilder.
 */

namespace Drupal\custom_solr_annotation\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;

/**
 * Provides a list controller for custom_solr_annotation entity.
 *
 * @ingroup custom_solr_annotation
 */
class SolrAnnotationListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   *
   * We override ::render() so that we can add our own content above the table.
   * parent::render() is where EntityListBuilder creates the table using our
   * buildHeader() and buildRow() implementations.
   */
  public function render() {
    $build['description'] = array(
      '#markup' => $this->t('Solr Annotation Entity Example implements a solr annotation model. These annotation are fieldable entities. You can manage the fields on the <a href="@adminlink">Solr Annotation admin page</a>.', array(
        '@adminlink' => \Drupal::urlGenerator()->generateFromRoute('custom_solr_annotation.solr_annotation_settings'),
      )),
    );
    $build['table'] = parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * Building the header and content lines for the contact list.
   *
   * Calling the parent::buildHeader() adds a column for the possible actions
   * and inserts the 'edit' and 'delete' links as defined for the entity type.
   */
  public function buildHeader() {
    $header['id'] = $this->t('Solr Annotation Id');
    $header['annotation'] = $this->t('Annotation');
    $header['solr_doc_id'] = $this->t('Solr index doc ID');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\custom_solr_annotation\Entity\SolrAnnotation */
    $annotation = '';
    foreach ($entity->toArray()['annotation'] as $val){
      $annotation[] = $val['value'];
    }
    $row['id'] = $entity->id();
    $row['annotation'] = implode(', ', $annotation);
    $row['solr_doc_id'] = $entity->solr_doc_id->value;
    return $row + parent::buildRow($entity);
  }

}
