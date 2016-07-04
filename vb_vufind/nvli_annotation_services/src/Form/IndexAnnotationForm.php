<?php

namespace Drupal\nvli_annotation_services\Form;

use Drupal\Core\Entity\EntityManager;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\ViewsPluginManager;

/**
 * Class IndexAnnotationForm.
 *
 * @package Drupal\nvli_annotation_services\Form
 */
class IndexAnnotationForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'index_annotation_form';
  }
  /**
   * The wizard plugin manager.
   *
   * @var \Drupal\views\Plugin\ViewsPluginManager
   */
  protected $wizardManager;


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    //$adi = $this->wizardManager;//->getDefinitions();
    //ep($adi);exit;
    $xx = \Drupal::entityManager()->getAllBundleInfo();
    ep($xx);exit;
    $form['select_type'] = array(
      '#type' => 'select',
      '#title' => $this->t('Select Type'),
      '#description' => $this->t('select content type you wish to index'),
      '#options' => array('aditya' => $this->t('aditya'), 'manoj' => $this->t('manoj'), 'anurag' => $this->t('anurag'), 'rakash' => $this->t('rakash')),
      '#size' => 5,
    );
    $form['index'] = array(
      '#type' => 'submit',
      '#value' => t('Index'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
