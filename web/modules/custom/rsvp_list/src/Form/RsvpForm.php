<?php

namespace Drupal\rsvp_list\Form;


use Drupal;
use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class RsvpForm extends FormBase
{

  public function getFormId()
  {
    return 'rsvp_list_email_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $node = \Drupal::routeMatch()->getParameter('node');
    $nid = $node->nid->value;
    $form['email'] = [
      '#title' => t('Email address'),
      '#type' => 'textfield',
      '#size' => 25,
      '#description' => t('Updates will be sent to provided email address'),
      '#require' => true,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Submit RSVP'),
    ];
    $form['nid'] = [
      '#type' => 'hidden',
      '#value' => $nid,
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    parent::validateForm($form, $form_state);

    $email = $form_state->getValue('email');
    if (!Drupal::service('email.validator')->isValid($email)) {
      $form_state->setErrorByName('email', t('Invalid email address'));
      return;
    }

    $this->validateEmailNotInEvent($form_state, $email);
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $user = Drupal::currentUser();

    try {
      Drupal::database()->insert('rsvp_list')->fields([
        'mail' => $form_state->getValue('email'),
        'nid' => $form_state->getValue('nid'),
        'uid' => $user->id(),
        'created' => time()
      ])->execute();
    } catch (\Exception $e) {
      //Log exception
    }

    Drupal::messenger()->addMessage(t('Successful submission'), 'status');
  }

  private function validateEmailNotInEvent(FormStateInterface &$form_state, string $email): void
  {
    $node = Drupal::routeMatch()->getParameter('node');

    $result = false;
    try{
      $query = Database::getConnection()->select('rsvp_list_', 'r');
      $query->fields('r', ['nid']);
      $query->condition('nid', $node->id);
      $query->condition('mail', $email);
      $result = $query->execute();
    } catch (\Exception $e){
      //Log exception
    }

    if ($result && !empty($result->fetchCol())){
      $form_state->setErrorByName('email', t('%email is already subscribed to this event'));
      return;
    }
  }
}

;
