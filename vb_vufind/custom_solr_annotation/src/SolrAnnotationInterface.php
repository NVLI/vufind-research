<?php
/**
* @file
* Contains \Drupal\custom_solr_annotation\SolrAnnotationInterface.
*/

namespace Drupal\custom_solr_annotation;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
* Provides an interface defining a Solr Annotation entity.
* @ingroup custom_solr_annotation
*/
interface SolrAnnotationInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
