<?php
/**
 * @file
 * Contains \Drupal\syndo_ecommerce\Form\WorkForm.
 */
namespace Drupal\syndo_ecommerce\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CardPaymentForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'card_payment_form';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['clientCardName'] = array(
      '#type' => 'textfield',
      '#title' => t('Nome Impresso no Cartão:'),
      '#required' => TRUE,
    );
    $form['cpf'] = array(
      '#type' => 'textfield',
      '#title' => t('CPF:'),
      '#required' => TRUE,
    );
    $form['cardNumber'] = array(
      '#type' => 'textfield',
      '#title' => t('Número do Cartão:'),
      '#required' => TRUE,
    );
    $form['month'] = array(
      '#type' => 'textfield',
      '#title' => t('Mês:'),
      '#required' => TRUE,
    );
    $form['year'] = array(
      '#type' => 'textfield',
      '#title' => t('Ano:'),
      '#required' => TRUE,
    );
    $form['securityCode'] = array(
      '#type' => 'textfield',
      '#title' => t('Código de Segurança:'),
      '#required' => TRUE,
    );
    $form['data']['instalments'] = [
        '#type' => 'select',
        '#required' => TRUE,
        '#title' => t('Parcelas:'),
        '#options' => [
            '1' => t('1'),
            '2' => t('2'),
            '3' => t('3'),
            '4' => t('4'),
        ],
    ];    
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Finalizar Compra'),
      '#button_type' => 'primary',
    );

    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    /*
     * @TODO: Chamar api para pagamento com cartão
    */

    $form_state->setRebuild();
  }
}
