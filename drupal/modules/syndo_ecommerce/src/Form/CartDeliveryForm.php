<?php
/**
 * @file
 * Contains \Drupal\syndo_ecommerce\Form\WorkForm.
 */

namespace Drupal\syndo_ecommerce\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CartDeliveryForm extends FormBase
{
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'cart_deliver_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['zipcode'] = array(
            '#type' => 'textfield',
            '#title' => t('CEP'),
            '#required' => TRUE,
            '#size' => 10,
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
                '#type' => 'table',
                '#rows' => [
                    ['Valor', 'R$' . number_format($frete['preco'], 2, ',', '.')],
                    ['Prazo', $frete['prazo'] . ' dias úteis.'],
                ],
                '#weight' => 500,
            );
        }
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $cep = $form_state->getValue('zipcode');
        $entrega = $form_state->getValue('entrega');
        $cart_items = $form_state->getBuildInfo()['args'][0];

        $resultadoFrete = calcularFreteTotal($cep, $entrega, $cart_items);

        $form_state->set('frete', $resultadoFrete);
        $form_state->setRebuild();
    }
}
