<?php
/**
 * @file
 * Contains \Drupal\syndo_ecommerce\Form\WorkForm.
 */
namespace Drupal\syndo_ecommerce\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManager;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\Core\TempStore\TempStoreException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EraseCart extends FormBase {

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
    return 'erase_cart';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {


    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Limpar Carrinho'),
      '#button_type' => 'primary',
    );

    return $form;
  }
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->userPrivateTempstore->delete('cart_items');

  }

}
