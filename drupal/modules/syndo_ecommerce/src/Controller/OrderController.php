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

    $nids = Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'order')
      ->condition('uid', $usuario->id())
      ->execute();
    
    //var_dump($nids);
    /*highlight_string("<?php\n\$data =\n" . var_export($result, true) . ";\n?>");*/
    //die();

    $nodes = node_load_multiple($nids);

    foreach ($nodes as $node) {
      $datapedido = $node->get('field_datapedido')->getValue();
      $idpagamento = $node->get('field_idpagamento')->getValue();
      $idpedido = $node->get('field_idpedido')->getValue();
      $idrastreio = $node->get('field_idrastreio')->getValue();
      
      $listidproduto = $node->get('field_listidproduto')->getValue();

      $meiopagamento = $node->get('field_meiopagamento')->getValue();
      if ($meiopagamento == "boleto") {
        $idstatus = $node->get('field_idstatus')->getValue();
        $situacaopagamento = situacaoBoleto($idstatus);
      } else {
        $situacaopagamento = "Aprovado";
      }
    }
    
    $output = node_view_multiple($nodes);

    //var_dump($output);
    /*highlight_string("<?php\n\$data =\n" . var_export($output, true) . ";\n?>");*/
    //die();



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