<?php
namespace Drupal\syndo_ecommerce\Controller;

use Drupal;

/**
 * Provides route responses for the Example module.
 */
class OrderController {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function history() {
    Drupal::service('page_cache_kill_switch')->trigger();
    $usuario = Drupal::currentUser();

    $result = Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'order')
      ->condition('uid', $usuario->id())
      ->execute();
    
    var_dump($result);
    die();

    $nids = array_keys($result['node']);

    $nodes = node_load_multiple($nids);
    $output = node_view_multiple($nodes);



//    $query = Drupal::database()->query('SELECT n.nid, field_idproduto_value FROM node__field_idproduto nf JOIN node n ON n.vid = nf.revision_id');
//    $codigos = $query->fetchAllKeyed();
//
//    $produtos = listarProdutos($codigos, $nome, $categoria);
//
//    $filters = Drupal::formBuilder()->getForm('Drupal\syndo_ecommerce\Form\SearchForm');
//
//    $filters['name']['#default_value'] = $nome;
//    $filters['name']['#value'] = $nome;
//    $filters['category']['#default_value'] = $categoria;
//    $filters['category']['#value'] = $categoria;
//
//    $element = array(
//        '#type' => 'container',
//        'filtro' => $filters,
//        'lista' => [
//            '#type' => 'table',
//            '#header' => ['Código', 'Nome do Produto', 'Preço'],
//            '#rows' => iterator_to_array($produtos),
//        ],
//    );

    return $output;
  }

}
