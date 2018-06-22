<?php
namespace Drupal\syndo_ecommerce\Controller;

use Drupal;
use Drupal\node\Entity\Node;

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

    $nodes = Node::loadMultiple($nids);

    $out = array();
    foreach ($nodes as $node) {
      $datapedido = $node->get('field_datapedido')->getValue();
      $idstatus = $node->get('field_idstatus')->getValue();
      $idpedido = $node->get('field_idpedido')->getValue();

      $idrastreio = $node->get('field_idrastreio')->getValue();
      $situacaoEntrega = rastreiaPedido($idrastreio);
      
      $listidproduto = $node->get('field_listidproduto')->getValue();
      $produtos = array();
      foreach($listidproduto as $idproduto) {
        array_push($produtos, visualizarDadosProduto($idproduto));
      }

      $situacaopagamento = "Aprovado";

      $meiopagamento = $node->get('field_meiopagamento')->getValue();
      if ($meiopagamento == "boleto") {
        $idpagamento = $node->get('field_idpagamento')->getValue();
        $situacaopagamento = situacaoBoleto($idpagamento);
      }

      $array = array($datapedido, $idstatus, $idpedido, $situacaoEntrega, $situacaopagamento, $produtos);

      array_push($out, $array);
    }

    $output = [
        '#type' => 'table',
        '#hearders' => [
            'Data do Pedido',
            'Status',
            'Identificador',
            'Situação da Entrega',
            'Situação do Pagamento',
            'Produtos',
        ],
        '#rows' => $out,
        '#empty' => 'Nenhum pedido foi feito até o momento!',
    ];

    return $output;
  }

}
