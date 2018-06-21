<?php

namespace Drupal\syndo_ecommerce\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class SearchForm extends FormBase {
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
        $categorias = consultarCategoria();

        $valoresCategorias = array_combine(array_column($categorias, 'idcategoria'), array_column($categorias, 'nome'));

        $form['name'] = array(
            '#type' => 'textfield',
            '#title' => t('Nome:'),
            '#required' => FALSE,
        );
        $form['category'] = array(
            '#type' => 'select',
            '#options' => (['' => '{Selecione}'] + $valoresCategorias),
            '#title' => t('Categoria:'),
            '#required' => FALSE,
        );
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Buscar'),
            '#button_type' => 'primary',
        );

        return $form;
    }
    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $form_state->setRedirect('syndo_ecommerce.list_products', [], [
            'query' => [
                'nome' => $form_state->getValue('name', ''),
                'categoria' => $form_state->getValue('category', ''),
            ],
        ]);
    }
}