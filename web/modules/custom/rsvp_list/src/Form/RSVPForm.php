<?php

namespace Drupal\rsvp_list\Form;


use Drupal;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class RSVPForm extends FormBase
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
    }

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
}

;
