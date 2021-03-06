<?php
/**
 * @file
 * Contains \Drupal\arabdt\Form\ArabdtForm.
 */

namespace Drupal\arabdt\Form;

use Drupal\{Core\Ajax\AjaxResponse,
  Core\Ajax\AppendCommand,
  Core\Form\FormBase,
  Core\Form\FormStateInterface};

class ArabdtForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'Arabdt_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name:'),
      '#required' => TRUE,
    ];

    $form['file'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Choose File'),
      '#upload_location' => 'public://Files/',
      '#description' => $this->t('Please select single file'),
      '#upload_validators' => [
        'file_validate_extensions' => ['jpg jpeg gif png txt doc xls pdf ppt pps zip'],
        'file_validate_size' => [1024 * 1024 * 5],
      ],
      '#required' => TRUE,
    ];

    $form['date'] = [
      '#type' => 'date',
      '#title' => $this->t('Date:'),
      '#required' => TRUE,
    ];

    $form['checkbox'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Check Box:'),
    ];

    $form['gender'] = [
      '#id' => 'gender',
      '#type' => 'select',
      '#title' => $this->t('Gender'),
      '#options' => [
        'male' => $this->t('Male'),
        'Female' => $this->t('Female'),
      ],
      '#ajax' => [
        'callback' => [$this, 'getDataViaAjax'],
        'wrapper' => 'gender',
      ],
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function getDataViaAjax(): AjaxResponse {
    $response = new AjaxResponse();
    foreach (['Option1', 'Option2', 'Option3'] as $item) {
      $option = "<option>{$item}</option>";
      $response->addCommand(new AppendCommand('#gender', $option));
    }
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    if (strlen($form_state->getValue('name')) <= 9) {
      $form_state->setErrorByName('name', $this->t('Name is too short, should be more than 9 characters.'));
    }

    if ($form_state->getValue('date') < date('Y-m-d')) {
      $form_state->setErrorByName('date', $this->t('Date must be greater than Today.'));
    }
    elseif ($form_state->getValue('date') > date('Y-m-d', strtotime(date('Y-m-d') . "+8 days")) && empty($form_state->getValue('checkbox'))) {
      $form_state->setErrorByName('checkbox', $this->t('This check box is required if the date is greater than 8 days.'));
      $form['checkbox']['#required'] = TRUE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    drupal_set_message($this->t('@name ,Your application is being submitted!', ['@name' => $form_state->getValue('name')]));

    foreach ($form_state->getValues() as $key => $value) {
      drupal_set_message($key . ': ' . $value);
    }

  }

}
