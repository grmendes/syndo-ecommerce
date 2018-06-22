<?php
/**
 * @file
 * Contains \Drupal\syndo_ecommerce\Form\WorkForm.
 */
namespace Drupal\syndo_ecommerce\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CartDeliveryForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cart_deliver_form';
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
      $frete = $form_state->get('frete');
      $form['valor_calculado'] = array(
        '#markup' => "<p>Preço: R\$".substr_replace($frete['preco'], ',', -2, 0)."</p><p>Prazo: ".$frete['prazo']." dias úteis.</p>",
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
    $cart_items = $form_state->getBuildInfo()['args'][0];

    $preco = 0;
    $prazo = 0;
    foreach ($cart_items as $key => $value) {
      $dadosProduto = visualizarDadosProduto($key);
      $frete = calcularFrete($entrega,"13083-872", $cep, $dadosProduto["peso"], 'Caixa', $dadosProduto["dimensao_c"], $dadosProduto["dimensao_a"], $dadosProduto["dimensao_l"]);
      // var_dump($value);die;
      $preco += intval($frete["preco"]) * intval($value);
      if($prazo < intval($frete["prazo"])) {
        $prazo = intval($frete["prazo"]);
      }
    }

    $resultadoFrete = array('preco' => $preco, 'prazo' => $prazo);

    $form_state->set('frete', $resultadoFrete);
    $form_state->setRebuild();
  }
}
