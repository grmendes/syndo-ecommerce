<?php

namespace Drupal\syndo_ecommerce\Form;

use Drupal\user\RegisterForm;
use Drupal\Core\Form\FormStateInterface;

class NewRegisterForm extends RegisterForm {
  protected function actions(array $form, FormStateInterface $form_state) {
    $element = parent::actions($form, $form_state);
    $element['submit']['#value'] = 'Submit';
    return $element;
  }

}