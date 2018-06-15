<?php

namespace Drupal\syndo_ecommerce\Controller;

use Drupal;

class ProductController
{
    /**
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listProducts() {
        Drupal::service('page_cache_kill_switch')->trigger();
        $nome = Drupal::request()->query->get('nome', '');
        $categoria = Drupal::request()->query->get('categoria', '');
        $query = Drupal::database()->query('SELECT n.nid, field_idproduto_value FROM node__field_idproduto nf JOIN node n ON n.vid = nf.revision_id');
        $codigos = $query->fetchAllKeyed();

        $produtos = listarProdutos($codigos, $nome, $categoria);

        $filters = Drupal::formBuilder()->getForm('Drupal\syndo_ecommerce\Form\SearchForm');

        $filters['name']['#default_value'] = $nome;
        $filters['name']['#value'] = $nome;
        $filters['category']['#default_value'] = $categoria;
        $filters['category']['#value'] = $categoria;

        $element = array(
            '#type' => 'container',
            'filtro' => $filters,
            'lista' => [
                '#type' => 'table',
                '#header' => ['Código', 'Nome do Produto', 'Preço'],
                '#rows' => iterator_to_array($produtos),
            ],
        );

        return $element;
    }
}