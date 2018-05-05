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
    drupal_set_message($this->t('@emp_name ,Your application is being submitted!', array('@emp_name' => $form_state->getValue('zipcode'))));

    /*
     * @TODO: Chamar api com o CEP passado.
    */

    $form_state->set('frete', 10.0);
  }
}