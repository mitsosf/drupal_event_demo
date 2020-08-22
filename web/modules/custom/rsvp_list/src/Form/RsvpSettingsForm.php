<?php

namespace Drupal\rsvp_list\Form;

use Drupal\Core\Form\ConfigFormBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Form\FormStateInterface;

class RsvpSettingsForm extends ConfigFormBase {

  public function getFormID() {
    return 'rsvp_list_admin_settings';
  }

  protected function getEditableConfigNames() {
    return [
      'rsvp_list.settings'
    ];
  }

  public function buildForm(array $form, FormStateInterface $form_state, Request $request = NULL) {
    $types = node_type_get_names();
    $config = $this->config('rsvp_list.settings');
    $form['rsvp_list_types'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('The content types to enable RSVP collection for'),
      '#default_value' => $config->get('allowed_types'),
      '#options' => $types,
      '#description' => t('On the specified node types, an RSVP option will be available and can be enabled while tht node is being edited.'),
    );
    $form['array_filter'] = array('#type' => 'value', '#value' => TRUE);

    return parent::buildForm($form,$form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $allowed_types = array_filter($form_state->getValue('rsvp_list_types'));
    sort($allowed_types);
    $this->config('rsvp_list.settings')
      ->set('allowed_types', $allowed_types)
      ->save();
    parent::submitForm($form, $form_state);
  }
}


