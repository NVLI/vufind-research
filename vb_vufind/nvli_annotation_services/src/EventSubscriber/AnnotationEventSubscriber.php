<?php

namespace Drupal\nvli_annotation_services\EventSubscriber;

use Drupal\nvli_annotation_services\AnnotationStoreEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * Class AnnotationEventSubscriber.
 *
 * @package Drupal\nvli_annotation_services
 */
class AnnotationEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[AnnotationStoreEvent::SAVE][] = array('reactOnAnnotationSave', 100);
    return $events;

  }

  /**
   * Subscriber Callback for the event.
   * @param AnnotationStoreEvent $event
   */
  public function reactOnAnnotationSave(AnnotationStoreEvent $event) {
    drupal_set_message("Saved annotation for solr doc:" . $event->getReferenceSolrDocId());
  }
}
