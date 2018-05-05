<?php

namespace Drupal\syndo_ecommerce\Plugin\Block;

use \Drupal;	
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormInterface;

/**
 * Provides a 'Hello' Block.
 *
 * @Block(
 *   id = "deliver_block",
 *   admin_label = @Translation("Delivery form block"),
 *   category = @Translation("Syndo Ecommerce"),
 * )
 */
class DeliveryBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = Drupal::formBuilder()->getForm('Drupal\syndo_ecommerce\Form\DeliveryForm');
    return $form;
  }

}