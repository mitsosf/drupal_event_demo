<?php

namespace Drupal\mymodule\Controller;

use Drupal\Core\Controller\ControllerBase;

class FirstController extends ControllerBase{
  public function content(){
    return [
      '#type' => 'markup',
      '#markup' => t('This is my custom page'),
    ];
  }
}
