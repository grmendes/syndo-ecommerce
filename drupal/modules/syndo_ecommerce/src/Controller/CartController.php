<?php

namespace Drupal\syndo_ecommerce\Controller;

use Drupal;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManager;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\Core\TempStore\TempStoreException;
use Symfony\Component\DependencyInjection\ContainerInterface;


class CartController extends ControllerBase
{
  protected $tempStore;

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

    public function view() {
        if ($this->currentUser->isAnonymous() && !isset($_SESSION['session_started'])) {
            $_SESSION['session_started'] = TRUE;
            $this->sessionManager->start();
        }

        $cart_items = array_filter($this->userPrivateTempstore->get('cart_items') ?? []);

        $productsCodes = array_keys($cart_items);

        $productsInfo = listarProdutos($productsCodes);

        $cart_info = [];
        foreach ($productsInfo as $product) {
            $precoUn = floatval(str_replace('R$', '' , $product['preco']));
            $qtd = intval($cart_items[$product['codigo']]);
            $cart_info[] = [
                $product['nome'],
                $qtd,
                'R$' . number_format($precoUn, 2, ',', '.'),
                'R$' . number_format($precoUn * $qtd, 2, ',', '.'),
            ];
        }

        $frete_form = Drupal::formBuilder()->getForm('Drupal\syndo_ecommerce\Form\CartDeliveryForm', $cart_items);

        $checkout_form = Drupal::formBuilder()->getForm('Drupal\syndo_ecommerce\Form\CheckoutForm', $cart_items);

        $element = array(
            '#type' => 'container',
            'lista' => [
                '#type' => 'table',
                '#header' => ['Produto', 'Quantidade', 'Preço/Un.', 'Valor'],
                //'#header' => ['Quantidade'],
                '#rows' => $cart_info,
            ],
            'frete' => $frete_form,
            'checkout' => [
                '#markup' => '<h1>Finalizar Compra</h1>',
                'form' => $checkout_form,
            ],
        );



        return $element;
    }
}
