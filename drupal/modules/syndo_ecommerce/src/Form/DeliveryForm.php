<?php
/**
 * @file
 * Contains \Drupal\syndo_ecommerce\Form\WorkForm.
 */
namespace Drupal\syndo_ecommerce\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class DeliveryForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'deliver_form';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['zipcode'] = array(
      '#type' => 'textfield',
      '#title' => t('CEP:'),
      '#required' => TRUE,
    );    
    $form['data']['entrega'] = [
        '#type' => 'select',
        '#required' => TRUE,
        '#title' => t('Tipo da Entrega'),
        '#options' => [
            'pac' => t('PAC'),
            'sedex' => t('Sedex'),
        ],
    ];    
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Calcular'),
      '#button_type' => 'primary',
    );

    if ($form_state->has('frete')) {
      $frete = $form_state->get('frete');
      $form['valor_calculado'] = array(
        '#markup' => "<p>Valor: $frete</p>",
      );
    }
    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $cep = $form_state->getValue('zipcode');
    /*
     * @TODO: Chamar api com o CEP passado.
    */
    $frete = 10.0;

    $form_state->set('frete', $frete);
    $form_state->setRebuild();
  }
}