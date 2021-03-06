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
            'PAC' => t('PAC'),
            'SEDEX' => t('SEDEX'),
        ],
    ];    
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Calcular'),
      '#button_type' => 'primary',
    );

    if ($form_state->has('frete')) {
      $frete = number_format($form_state->get('frete')['preco'], 2, ',', '.');
      $form['valor_calculado'] = array(
        '#markup' => "<p>Valor: R$ $frete</p>",
      );
      $frete = $form_state->get('frete')['prazo'];
      $form['prazo_estipulado'] = array(
        '#markup' => "<p>Prazo: $frete dias úteis</p>",
      );
    }
    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $cep = $form_state->getValue('zipcode');
    $entrega = $form_state->getValue('entrega');

    $frete = calcularFrete($entrega, 13083872, $cep, 1000, 'Caixa', 10, 10, 10);

    $form_state->set('frete', $frete);
    $form_state->setRebuild();
  }
}