<?php

namespace Drupal\syndo_ecommerce\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManager;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\Core\TempStore\TempStoreException;
use Drupal\Core\TypedData\Plugin\DataType;
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
            'tipo_entrega' => [
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

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $cart = $this->userPrivateTempstore->get('cart_items') ?? [];
        $valorTotal = $form_state->getBuildInfo()['args'][1];
        $mode = $form_state->getValue('mode');

        switch ($mode) {
            case 'creditCard':
                $this->processCreditCardPurchase($form_state, $cart, $valorTotal);
                break;
            case 'bankTicket':
                $this->processBankTicketPurchase($form_state, $cart, $valorTotal);
                break;
        }

        $this->registraEntrega($form_state, $cart);

        $this->userPrivateTempstore->delete('cart_items');

        \Drupal::messenger()->addMessage('Compra efetuada com sucesso!');



    }

    protected function processCreditCardPurchase(FormStateInterface $form_state, array $cart_items) {
        $total = 0;
        foreach($cart_items as $item) {
            $total += $item[3];
        }

        $total += $frete;

        registrarCartao($form_state->getValue('number'), true);

        pagamentoCartao($form_state->getValue('cardholder'), $form_state->getValue('cpf'),
            $form_state->getValue('number'), $form_state->getValue('month'), $form_state->getValue('year'),
            $form_state->getValue('ccv'), $total, $form_state->getValue('instalments'));
    }

    protected function processBankTicketPurchase(FormStateInterface $form_state, array $cart_items) {


        $total = 0;
        foreach($cart_items as $item) {
            $total += $item[3];
        }
        highlight_string("<?php\n\$data =\n" . var_export($cart_items, true) . ";\n?>");

        $response = pagamentoBoleto(
            $form_state->getValue('fullName'),
            $form_state->getValue('cpf'),
            $form_state->getValue('billing_address'),
            $form_state->getValue('bankTicket')->getValue('billing_zipcode'),
            $total
        );

        var_dump($response); die();

        $idRastreio = registraEntrega($form_state, $cart_items);

        criaOrder($response[''], 'bankTicket', $idRastreio, $cart_items);
    }

    private function criaOrder($idPagamento, $meioPagamento, $idRastreio, $cart_items) {
        foreach ($cart_items as $key => $value) {

        }

        $node = Node::create([
            'type' => 'article',
            'title' => 'Order',
            'field_datapedido' => Timestamp::getDateTime(),
            'field_idpagamento' => $idPagamento,
            'field_idrastreio' => $idRastreio,
            'field_idstatus' => '',
            'field_listidproduto' => '',
            'field_meiopagamento' => $meioPagamento
        ]);
        $node->save();
    }

    protected function registraEntrega($form_state, $cart)
    {
    }
}