<?php

namespace Drupal\syndo_ecommerce\Controller;

use Drupal;
use Drupal\Core\Controller\ControllerBase;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;


class CartController extends ControllerBase
{
  protected $tempStore;

    // Pass the dependency to the object constructor
    public function __construct(PrivateTempStoreFactory $temp_store_factory) {
      // For "mymodule_name," any unique namespace will do
      $this->tempStore = $temp_store_factory->get('syndo-ecommerce');

      if($this->tempStore->get('cart_items') == NULL) {
        $this->tempStore->set('cart_items', ['idproduto1' =>['nome' => 'Meia extra confortável', 'quantidade' => 15, 'preco' => 'R$123,45'], 'idproduto2' =>['nome' => 'Meia meh dinamica', 'quantidade' => 2, 'preco' => 'R$10,99'] ]);
      }
    }

    // Uses Symfony's ContainerInterface to declare dependency to be passed to constructor
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('user.private_tempstore')
        );
    }

    public function view() {
        $cart_items = $this->tempStore->get('cart_items');

        $frete_form = Drupal::formBuilder()->getForm('Drupal\syndo_ecommerce\Form\CartDeliveryForm');


        $element = array(
            '#type' => 'container',
            'lista' => [
                '#type' => 'table',
                '#header' => ['Produto', 'Quantidade', 'Preço'],
                '#rows' => $cart_items
            ],
            'frete' => $frete_form,
        );



        return $element;
    }
}
