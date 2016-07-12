<?php


namespace Drupal\annotation_store\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;

/**
 * Provides a list controller for annotation_store entity.
 *
 * @ingroup annotation_store
 */
class AnnotationStoreListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   *
   * We override ::render() so that we can add our own content above the table.
   * parent::render() is where EntityListBuilder creates the table using our
   * buildHeader() and buildRow() implementations.
   */
  public function render() {
    $build['description'] = array(
      '#markup' => $this->t('Annotation store Entity Example implements a Annotation store model. These annotation are fieldable entities. You can manage the fields on the <a href="@adminlink">Annotation store admin page</a>.', array(
        '@adminlink' => \Drupal::urlGenerator()->generateFromRoute('annotation_store.annotation_store_entity_settings'),
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
    $header['id'] = $this->t('Resource Id');
    $header['annotation'] = $this->t('Title');
    $header['solr_doc_id'] = $this->t('Solr index doc ID');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\annotation_store\Entity\AnnotationStore */

    $row['id'] = $entity->id();
    $row['title'] = $entity->title->value;
    $row['solr_doc_id'] = $entity->solr_doc_id->value;
    return $row + parent::buildRow($entity);
  }

}
