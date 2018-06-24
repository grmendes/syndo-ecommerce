<?php

namespace Drupal\syndo_ecommerce\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManager;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\Core\TempStore\TempStoreException;
use Drupal\Core\TypedData\Plugin\DataType;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CheckoutForm extends FormBase {
    /**
     * @var PrivateTempStoreFactory
     */
    protected $userPrivateTempstore;

    /**
     * @var SessionManager
     */
    protected $sessionManager;

    /**
     * @var AccountInterface
     */
    protected $currentUser;

    /**
     * @param PrivateTempStoreFactory $temp_store_factory
     */
    public function __construct(
        PrivateTempStoreFactory $user_private_tempstore,
        SessionManager $session_manager,
        AccountInterface $current_user
    ) {
        $this->sessionManager = $session_manager;
        $this->currentUser = $current_user;
        $this->userPrivateTempstore = $user_private_tempstore->get('syndo_ecommerce');
    }

    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('user.private_tempstore'),
            $container->get('session_manager'),
            $container->get('current_user')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'search_form';
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['delivery'] = array(
            '#type' => 'container',
            'title' => [
                '#markup' => '<h2>Dados de Entrega</h2>'
            ],
            'state' => [
                '#type' => 'textfield',
                '#title' => t('Estado:'),
                '#required' => true,
            ],
            'city' => [
                '#type' => 'textfield',
                '#title' => t('Cidade:'),
                '#required' => true,
            ],
            'address' => [
                '#type' => 'textfield',
                '#title' => t('Rua:'),
                '#required' => true,
            ],
            'addressNumber' => [
                '#type' => 'textfield',
                '#title' => t('Número:'),
                '#required' => true,
            ],
            'zipcode' => [
                '#type' => 'textfield',
                '#title' => t('CEP:'),
                '#required' => true,
            ],
            'entrega' => [
                '#type' => 'select',
                '#required' => TRUE,
                '#title' => t('Tipo da Entrega'),
                '#options' => [
                    'PAC' => t('PAC'),
                    'SEDEX' => t('SEDEX'),
                ],
            ],
        );
        $form['billing'] = array(
            '#type' => 'container',
            'title' => [
                '#markup' => '<h2>Dados de Pagamento</h2>'
            ],
            'cpf' => [
                '#type' => 'textfield',
                '#title' => 'CPF',
                '#required' => true,
            ],                

            'mode' => [
                '#title' => 'Modo de Pagamento',
                '#type' => 'radios',
                '#options' => ['creditCard' => 'Cartão de crédito', 'bankTicket' => 'Boleto'],
                '#required' => true,
            ],
            'creditCard' => [
                '#type' => 'container',
                '#states' => array(
                    'visible' => array(
                        ':radio[name="mode"]' => array('value' => 'creditCard'),
                    ),
                ),
                'cardholder' => [
                    '#type' => 'textfield',
                    '#title' => 'Nome do titular do Cartão',
                ],
                'number' => [
                    '#type' => 'textfield',
                    '#title' => 'Número do Cartão',
                ],
                'year' => [
                    '#type' => 'textfield',
                    '#title' => 'Ano Validade',
                ],
                'month' => [
                    '#type' => 'textfield',
                    '#title' => 'Mes Validade',
                ],
                'ccv' => [
                    '#type' => 'textfield',
                    '#title' => 'CCV',
                ],
                'instalments' => [
                    '#type' => 'select',
                    '#title' => t('Parcelas'),
                    '#options' => array_combine(range(1, 4), range(1, 4)),
                ],
            ],
            'bankTicket' => [
                '#type' => 'container',
                '#states' => array(
                    'visible' => array(
                        ':radio[name="mode"]' => array('value' => 'bankTicket'),
                    ),
                ),
                'fullName' => [
                    '#type' => 'textfield',
                    '#title' => 'Nome do Completo',
                ],
                'billing_address' => [
                    '#type' => 'textfield',
                    '#title' => 'Endereço de Cobrança',
                ],
                'billing_zipcode' => [
                    '#type' => 'textfield',
                    '#title' => 'CEP',
                ],
            ],
        );

        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Finalizar compra'),
            '#button_type' => 'primary',
        );

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $cart = $form_state->getBuildInfo()['args'][0];

        $cep = $form_state->getValue('zipcode');
        $entrega = $form_state->getValue('entrega');
        $resultadoFrete = calcularFreteTotal($cep, $entrega, $cart);

        if (empty($resultadoFrete)) {
            $form_state->setError($form, 'Não entregamos no CEP indicado ou ele é inválido');
        }


        $cart = $form_state->getBuildInfo()['args'][0];
        $valorTotal = $form_state->getBuildInfo()['args'][1];

        $valorTotal += $resultadoFrete['preco'];

        $mode = $form_state->getValue('mode');

        $idPedido = null;
        switch ($mode) {
            case 'creditCard':
                $idPedido = $this->processCreditCardPurchase($form_state, $cart, $valorTotal);
                break;
            case 'bankTicket':
                $idPedido = $this->processBankTicketPurchase($form_state, $cart, $valorTotal);
                break;
        }

        if (empty($idPedido)) {
            $form_state->setError($form, 'Um erro ocorreu ao processar sua compra. Por favor tente novamente.');
        }

        $form_state->set('idPedido', $idPedido);
    }


    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $this->userPrivateTempstore->delete('cart_items');

        $idPedido = $form_state->get('idPedido');

        \Drupal::messenger()->addMessage('Compra efetuada com sucesso! Identificador do pedido: ' . $idPedido);
    }

    protected function processCreditCardPurchase(FormStateInterface $form_state, array $cart_items, $valorTotal) {

        registrarCartao($form_state->getValue('number'), true);

        $response = pagamentoCartao(
            $form_state->getValue('cardholder'), 
            $form_state->getValue('cpf'),
            $form_state->getValue('number'), 
            $form_state->getValue('month'), 
            $form_state->getValue('year'),
            $form_state->getValue('ccv'), 
            $valorTotal, 
            $form_state->getValue('instalments')
        );

        $idRastreio = $this->registraEntrega($form_state, $cart_items);

        return $this->criaOrder($response['opHash'], 'creditCard', $idRastreio, $cart_items);
    }

    protected function processBankTicketPurchase(FormStateInterface $form_state, array $cart_items, $valorTotal) {

        $idPagamento = pagamentoBoleto(
            $form_state->getValue('fullName'),
            $form_state->getValue('cpf'),
            $form_state->getValue('billing_address'),
            $form_state->getValue('billing_zipcode'),
            $valorTotal
        );

        $idRastreio = $this->registraEntrega($form_state, $cart_items);

        return $this->criaOrder($idPagamento, 'bankTicket', $idRastreio, $cart_items);
    }

    private function criaOrder($idPagamento, $meioPagamento, $idRastreio, $cart_items) {
        $listPedidos = array();
        foreach ($cart_items as $key => $value) {
            array_push($listPedidos, $value['id']);
        }

        $created_date = time();

        $node = Node::create([
            'type' => 'order',
            'langcode' => 'en',
            'created' => [$created_date],
            'changed' => [$created_date],
            'title' => 'Order',
            'field_datapedido' => [$created_date],
            'field_idpagamento' => [$idPagamento],
            'field_idrastreio' => [$idRastreio],
            'field_idstatus' => [''],
            'field_listidproduto' => $listPedidos,
            'field_meiopagamento' => [$meioPagamento]
        ]);

        $node->save();

        return $node->id();
    }

    protected function registraEntrega($form_state, $cart_items) {

        return cadastrarEntrega(
            $cart_items[0]['id'],
            $form_state->getValue('entrega'),
            '13083872',
            $form_state->getValue('zipcode'),
            (int) floatval($cart_items[0]['peso']) * 1000,
            'Caixa',
            $cart_items[0]['dimensoes']['altura'],
            $cart_items[0]['dimensoes']['largura'],
            $cart_items[0]['dimensoes']['comprimento']
        );

    }
}
