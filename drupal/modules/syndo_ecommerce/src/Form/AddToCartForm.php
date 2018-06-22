<?php

namespace Drupal\syndo_ecommerce\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManager;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\Core\TempStore\TempStoreException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AddToCartForm extends FormBase {
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
        $form['quantity'] = array(
            '#type' => 'select',
            '#title' => t('Quantidade:'),
            '#required' => true,
            '#options' => range(1, 5),
        );

        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Adicionar ao carrinho'),
            '#button_type' => 'primary',
        );

        return $form;
    }
    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        if ($this->currentUser->isAnonymous() && !isset($_SESSION['session_started'])) {
            $_SESSION['session_started'] = TRUE;
            $this->sessionManager->start();
        }

        $cart = $this->userPrivateTempstore->get('cart_items') ?? [];
        // var_dump($cart);
        $productId = $form_state->getBuildInfo()['args'][0];
        $qtyInCart = $cart[$productId] ?? 0;

        $cart[$productId] = $qtyInCart + (int) $form_state->getValue('quantity', 0) + 1;
        // var_dump($cart);
        try {
            $this->userPrivateTempstore->set('cart_items', $cart);
        } catch (TempStoreException $e) {
            $form_state->setError($form['quantity'], 'An error has occurred, please try again.');
        }

        $qty = array_sum($cart);
        \Drupal::messenger()->addMessage("Produto adicionado ao carrinho com sucesso! Seu carrinho contém $qty " . $this->formatPlural($qty, 'item', 'items'));
    }
}
