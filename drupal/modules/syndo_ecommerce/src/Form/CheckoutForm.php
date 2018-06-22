<?php

namespace Drupal\syndo_ecommerce\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManager;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\Core\TempStore\TempStoreException;
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
        );
        $form['billing'] = array(
            '#type' => 'container',
            'title' => [
                '#markup' => '<h2>Dados de Pagamento</h2>'
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
                'expiry' => [
                    '#type' => 'textfield',
                    '#title' => 'Validade',
                ],
                'ccv' => [
                    '#type' => 'textfield',
                    '#title' => 'CCV',
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
        \Drupal::messenger()->addMessage('Compra efetuada com sucesso!');
    }
}