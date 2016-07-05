<?php

namespace Drupal\nvli_annotation_services\Form;


use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\wizard\WizardPluginBase;
use Drupal\views\Plugin\views\wizard\WizardException;
use Drupal\views\Plugin\ViewsPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

  public function __construct(ViewsPluginManager $wizard_manager) {
    $this->wizardManager = $wizard_manager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.views.wizard')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $wizard_plugins = $this->wizardManager->getDefinitions();
    $options = array();
    foreach ($wizard_plugins as $key => $wizard) {
      $key = preg_replace('/^standard:/', '', $key);
      $options[$key] = $wizard['title'];
    }
    $form['select_type'] = array(
      '#type' => 'select',
      '#title' => $this->t('Select Type'),
      '#description' => $this->t('select content type you wish to index'),
      '#options' => $options,
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
    $entities = \Drupal::entityTypeManager()
      ->getStorage($form_state->getValue('select_type'))
      ->loadMultiple();

//    $batch = array(
//      'title' => t('Exporting'),
//      'operations' => array(
//        array('my_function_1', array($account->id(), 'story')),
//        array('my_function_2', array()),
//      ),
//      'finished' => 'my_finished_callback',
//      'file' => 'path_to_file_containing_myfunctions',
//    );
//    batch_set($batch);
    $batch = array(
      'title' => t('Adding annotation to solr doc'),
      'operations' => array(
        array(
          '\Drupal\nvli_annotation_services\UpdateAnnotation::updateAnnotation',
          array($form_state->getValue('select_type'), $entities)
        ),
      ),
      'finished' => '\Drupal\nvli_annotation_services\UpdateAnnotation::addAnnotationFinishedCallback',
//      'file' => drupal_get_path('module', 'nvli_annotation_services') . 'crc/Form/IndexAnnotationForm.php',
    );

    batch_set($batch);
    //return batch_process();


//    drupal_set_message('Annotation added');
  }

//  public function add_annotation_to_solr($type, $options = array(), &$context) {
//    // Do heavy coding here...
//    $message = 'Adding annotation...';
//dpm('aditya');
//    foreach ($options as $entity) {
//      $server = 'solr';//isset($entity->get('server')->value)?$entity->get('server')->value: 'solr';
//      $id = $entity->get('solr_doc_id')->value;
//      $annotation = array();
//      $fields = array();
//      foreach ($entity->toArray()['annotation'] as $val) {
//        $annotation[] = $val['value'];
//      }
//      $fields['annotation'] = $annotation;
//
//      $results = \Drupal::service('nvli_annotation_services.add_annotation')
//        ->addAnnotation($server, $id, $fields);
//
//    }
//    $context['message'] = $message;
//  }

//  function add_annotation_to_solr_finished_callback($success, $results, $operations) {
//    // The 'success' parameter means no fatal PHP errors were detected. All
//    // other error management should be handled using 'results'.
//    if ($success) {
//      $message = \Drupal::translation()->formatPlural(
//        count($results),
//        'One post processed.', '@count posts processed.'
//      );
//    }
//    else {
//      $message = t('Finished with an error.');
//    }
//    drupal_set_message($message);
//    //$_SESSION['disc_migrate_batch_results'] = $results;
//  }

}
