<?php
/**
 * @file
 * Contains Drupal\custom_solr_annotation\Form\SolrAnnotationForm.
 */

namespace Drupal\custom_solr_annotation\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Language\Language;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the custom_solr_annotation entity edit forms.
 *
 * @ingroup custom_solr_annotation
 */
class SolrAnnotationForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\custom_solr_annotation\Entity\SolrAnnotation */
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;

    $form['langcode'] = array(
      '#title' => $this->t('Language'),
      '#type' => 'language_select',
      '#default_value' => $entity->getUntranslated()->language()->getId(),
      '#languages' => Language::STATE_ALL,
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $form_state->setRedirect('entity.solr_annotation.collection');
    $entity = $this->getEntity();
    $entity->save();
  }
}
