<?php

namespace Drupal\syndo_ecommerce\Controller;

use DateTime;
use Drupal;
use Drupal\node\Entity\Node;

/**
 * Provides route responses for the Example module.
 */
class OrderController
{

    /**
     * Returns a simple page.
     *
     * @return array
     *   A simple renderable array.
     */
    public function history()
    {
        $usuario = Drupal::currentUser();

        $nids = Drupal::entityQuery('node')
            ->condition('status', 1)
            ->condition('type', 'order')
            ->condition('uid', $usuario->id())
            ->execute();

        /**
         * @var Node[] $nodes
         */
        $nodes = Node::loadMultiple($nids);

        $out = array();
        foreach ($nodes as $node) {
            $datapedido = DateTime::createFromFormat('U', $node->get('field_datapedido')->getValue()[0]['value']);
            $idpedido = $node->id();

            $idrastreio = $node->get('field_idrastreio')->getValue()[0]['value'];
            $entrega = rastreiaPedido($idrastreio);
            $situacaoEntrega = array_pop($entrega['historicoRastreio'])['mensagem'];

            $situacaopagamento = "Em processamento";

            $meiopagamento = $node->get('field_meiopagamento')->getValue()[0]['value'];
            if ($meiopagamento == "bankTicket") {
                $idpagamento = $node->get('field_idpagamento')->getValue()[0]['value'];
                $pagamento = situacaoBoleto($idpagamento);
                $situacaopagamento = $pagamento['status'] == 'OK' ? 'Aprovado' : 'Não autorizado';
            }

            $out[] = [
                'Pedido' => '#' . $idpedido,
                'Data do Pedido' => $datapedido->format('d/m/Y H:i:s'),
                'Forma de Pagamento' => $meiopagamento == 'bankTicket' ? 'Boleto' : 'Cartão de Crédito',
                'Situação do Pagamento' => $situacaopagamento,
                'Código de rastreio' => $idrastreio,
                'Situação da Entrega' => $situacaoEntrega,
            ];
        }

        $output = [
            '#type' => 'table',
            '#header' => [
                'Pedido',
                'Data do Pedido',
                'Forma de Pagamento',
                'Situação do Pagamento',
                'Código de rastreio',
                'Situação da Entrega',
            ],
            '#rows' => $out,
            '#empty' => 'Nenhum pedido foi feito até o momento!',
        ];

        return $output;
    }

}
