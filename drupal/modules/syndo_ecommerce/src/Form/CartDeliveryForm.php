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
        '#markup' => "<p>Preço: R\$".$frete['preco']."</p><p>Prazo: ".$frete['prazo']."</p>",
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
      
    $precoPac = 0;
    $prazoPac = 0;
    $precoSedex = 0;
    $prazoSedex = 0;
    foreach ($cart_items as $key => $value) {
      $dadosProduto = visualizarDadosProduto($key);
      $fretes = calcularTodasOpcoesFrete("13083-872", $cep, $dadosProduto["peso"], 'Caixa', $dadosProduto["dimensao_c"], $dadosProduto["dimensao_a"], $dadosProduto["dimensao_l"]);
      foreach ($fretes as $tipoEntrega => $dadosFrete) {
        if ($tipoEntrega == 'pac') {
          $precoPac += intval($dadosFrete["preco"]) * intval($value["qtd"]);
          if($prazoPac < intval($dadosFrete["prazo"])) {
            $prazoPac = intval($dadosFrete["prazo"]);
          }
        }
        if ($tipoEntrega == 'sedex') {
          $precoSedex += intval($dadosFrete["preco"]) * intval($value["qtd"]);
          if($prazoSedex < intval($dadosFrete["prazo"])) {
            $prazoSedex = intval($dadosFrete["prazo"]);
          }
        }
      }
    }
      
    $resultadoFretes = array('pac' => array('preco' => $precoPac, 'prazo' => $prazoPac), 'sedex' => array('preco' => $precoSedex, 'prazo' => $prazoSedex));

    $form_state->set('frete', $resultadoFretes[$tipoEntrega]);
    $form_state->setRebuild();
  }
}
