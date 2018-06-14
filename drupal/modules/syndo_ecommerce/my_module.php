<?php

/**
 * @file
 * Contains syndo_ecommerce.module
 */

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\syndo_ecommerce\Api\Client\Domain\Model\Client;


include 'libs/produto.php';
include 'libs/categoria.php';
include 'libs/campo.php';
include 'libs/sac.php';

/**
 * Implements hook_theme().
 */
function syndo_ecommerce_theme($existing, $type, $theme, $path) {
  $variables = array(
    'form__syndo_ecommerce_form' => array(
      'render element' => 'form',
      'template' => 'form--syndo_ecommerce--form',
    ),
  );
  return $variables;
}

function syndo_ecommerce_form_user_form_alter(&$form, FormStateInterface $form_state, $form_id) {
    _syndo_ecommerce_user_form($form, $form_state);

    $form['account']['name']['#access'] = false;
    $form['account']['roles']['#access'] = false;
    $form['account']['field_idcliente']['#access'] = false; 
}

function syndo_ecommerce_form_alter(&$form, FormStateInterface $form_state, $form_id) {
    error_log($form_id);
}

function syndo_ecommerce_user_register_form_validate(&$form, FormStateInterface $form_state) {
    $previousErrors = $form_state->getErrors();
    if (!empty($previousErrors)) {
        return;
    }

    $newUser = new Client(
        '',
        $form_state->getValue('nome'),
        $form_state->getValue('mail'),
        $form_state->getValue('zipcode'),
        $form_state->getValue('phone'),
        '',
        $form_state->getValue('cpf'),
        $form_state->getValue('gender'),
        stripslashes($form_state->getValue('birthdate'))
    );

    try {
        $newUser->save();
    } catch (\GuzzleHttp\Exception\GuzzleException $e) {
        $form_state->setError($form['data']['cpf'], 'An error has occurred while connecting to webservice. Please try again. Details:' . $e->getMessage());
    }

    $form_state->setValue('field_idcliente', [
        'value' => $newUser->id()
    ]);
}

function _syndo_ecommerce_user_form(&$form, FormStateInterface $form_state) {
    module_set_weight('syndo_ecommerce', 99);
    $user = $form_state->getFormObject()->getEntity();

    assert($user instanceof \Drupal\user\Entity\User);
    $userId = $user->get('field_idcliente')->getValue()[0]['value'];

    if (!empty($userId)) {
        $userData = Client::getById($userId);
        var_dump($userData);
    } 

    $form['footer']['#access'] = false;
    $form['timezone']['#access'] = false;
    $form['language']['#acces'] = false;

    $form['data'] = [];

    $form['data']['cpf'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('CPF'),
        '#default_value' => $userData->getCpf(),
    ];

    $form['data']['nome'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('Nome Completo'),
        '#default_value' => $userData->getName(),
    ];

    $form['data']['zipcode'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('CEP'),
        '#default_value' => $userData->getCep(),
    ];

    $form['data']['number'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('Número'),
        '#default_value' => $userData->getNumero(),
    ];    

    $form['data']['complement'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('Complemento'),
        '#default_value' => $userData->getComplemento(),
    ];

    $form['data']['birthdate'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('Data de Nascimento'),
        '#default_value' => $userData->getBirthday(),
    ];

    $form['data']['phone'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('Telefone'),
        '#default_value' => $userData->getPhone1(),
    ];

    $form['data']['gender'] = [
        '#type' => 'select',
        '#required' => TRUE,
        '#title' => t('Gênero'),
        '#options' => [
            'male' => t('Masculino'),
            'female' => t('Feminino'),
        ]
    ];
}


function syndo_ecommerce_entity_type_alter(array &$entity_types) {
    //var_dump(array_keys($form_id));    	
}

function syndo_ecommerce_form_node_product_form_alter(&$form, FormStateInterface $form_state, $form_id) {
    array_unshift($form['actions']['submit']['#submit'], 'syndo_ecommerce_node_product_form_submit');

//    array_unshift($form['#validate'], 'syndo_ecommerce_node_product_form_validate');
//    var_dump($form['actions']['submit']['#submit']);
//    var_dump($form['#validate']);
//    var_dump(array_keys($form));    

    $form['data'] = [

    ];

    $form['data']['codigo'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('Codigo Produto'),
    ];

    $categorias = consultarCategoria();

	$options = array();
	foreach ($categorias as $categoria) {
    	$options[$categoria["idcategoria"]] = $categoria["nome"];
	}


    $form['data']['idcategoria'] = [
        '#type' => 'select',
        '#required' => TRUE,
        '#title' => t('Categoria'),
        '#options' => $options
    ];

    $form['data']['preco'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('Preço'),
    ];

    $form['data']['peso'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('Peso'),
    ];

    $form['data']['dimensao_a'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('Altura'),
    ];

    $form['data']['dimensao_c'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('Comprimento'),
    ];

    $form['data']['dimensao_l'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('Largura'),
    ];

    $campos = consultarCampos();
	foreach ($campos as $campo) {
		$form['data'][str_replace(' ','', $campo["nome"])] = [
        	'#type' => 'textfield',
        	'#required' => FALSE,
        	'#title' => t($campo["nome"]),
    	];
	}



	$form['imagem'] = array(
		'#title' => t('Imagem'),
		'#type' => 'managed_file',
		'#upload_location' => 'public://image_example_images/',
	);    
	//https://stackoverflow.com/questions/21214526/how-to-add-image-field-in-form-drupal-7?utm_medium=organic&utm_source=google_rich_qa&utm_campaign=google_rich_qa

	$form['field_idproduto']['#access'] = FALSE;
}

function syndo_ecommerce_form_node_sac_form_alter(&$form, FormStateInterface $form_state, $form_id) {
    array_unshift($form['actions']['submit']['#submit'], 'syndo_ecommerce_node_sac_form_submit');   

    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $name = $user->get('name')->value;

    $form['data'] = [

    ];


    $form['data']['remetente'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('Remetente'),
    ];    

    $form['data']['replyer'] = [
        '#type' => 'textfield',
        '#required' => FALSE,
        '#title' => t('Respondido por'),
    ];    

    $form['data']['response'] = [
        '#type' => 'textfield',
        '#required' => FALSE,
        '#title' => t('Resposta'),
    ];       

    if ($name != 'syndoecommerce') {
        $form['data']['replyer']['#access'] = FALSE;    
        $form['data']['response']['#access'] = FALSE;   
    }

}

function syndo_ecommerce_form_node_product_edit_form_alter(&$form, FormStateInterface $form_state, $form_id) {
//    array_unshift($form['actions']['submit']['#submit'], 'syndo_ecommerce_node_product_form_submit');

//    array_unshift($form['#validate'], 'syndo_ecommerce_node_product_form_validate');
//    var_dump($form['actions']['submit']['#submit']);
//    var_dump($form['#validate']);
//    var_dump(array_keys($form));    

	$form['field_idproduto']['#disabled'] = TRUE;

//	$json = visualizarDadosProduto($form_state->getValue('field_idproduto'));

    $form['data'] = [

    ];

    $form['data']['codigo'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('Codigo Produto'),
    ];

    $categorias = consultarCategoria();

	$options = array();
	foreach ($categorias as $categoria) {
    	$options[$categoria["idcategoria"]] = $categoria["nome"];
	}


    $form['data']['idcategoria'] = [
        '#type' => 'select',
        '#required' => TRUE,
        '#title' => t('Categoria'),
        '#options' => $options
    ];    

    $form['data']['preco'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('Preço'),
    ];

    $form['data']['peso'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('Peso'),
    ];

    $form['data']['dimensao_a'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('Altura'),
    ];

    $form['data']['dimensao_c'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('Comprimento'),
    ];    

    $form['data']['dimensao_l'] = [
        '#type' => 'textfield',
        '#required' => TRUE,
        '#title' => t('Largura'),
    ];

    $campos = consultarCampos();
	foreach ($campos as $campo) {
		$form['data'][str_replace(' ','', $campo["nome"])] = [
        	'#type' => 'textfield',
        	'#required' => FALSE,
        	'#title' => t($campo["nome"]),
    	];
	}



	$form['imagem'] = array(
		'#title' => t('Imagem'),
		'#type' => 'managed_file',
		'#upload_location' => 'public://image_example_images/',
	);
	//https://stackoverflow.com/questions/21214526/how-to-add-image-field-in-form-drupal-7?utm_medium=organic&utm_source=google_rich_qa&utm_campaign=google_rich_qa
}

function syndo_ecommerce_user_register_form_submit(&$form, FormStateInterface $form_state) {
//    var_dump($form_state->getValues());
//    var_dump($form_state->getValue('preco'));

}


function syndo_ecommerce_node_product_form_submit(&$form, FormStateInterface $form_state) {
//    var_dump($form_state->getValues());
// 	  var_dump($form_state->getValue('dimensao_c'));


	$array_campos = array();

	$campos = consultarCampos();

	foreach ($campos as $campo) {
		array_push($array_campos, array( 
			"idcampo" => $campo["idcampo"],
			"valor" => $form_state->getValue(str_replace(' ','', $campo["nome"])))
		);		
	}


	$idproduto = cadastrarProduto($form_state->getValue('codigo'), $form_state->getValue('title')[0]['value'], $form_state->getValue('idcategoria'), $form_state->getValue('preco'), $form_state->getValue('peso'), $form_state->getValue('dimensao_a'), $form_state->getValue('dimensao_c'), $form_state->getValue('dimensao_l'), 'img.jpg', $array_campos);

	$array = array(
		array("value" => $idproduto),
	);

	$form_state->setValue('field_idproduto', $array);

}

function syndo_ecommerce_node_sac_form_submit(&$form, FormStateInterface $form_state) {

	$user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
	$name = $user->get('name')->value;



	$ticket = cadastrarTicketCliente($form_state->getValue('body')[0]['value'], $form_state->getValue('remetente'), $name);

    if (($form_state->getValue('response') != NULL) || ($form_state->getValue('replyer') != NULL)) {
        adicionarMensagem($form_state->getValue('response'), $form_state->getValue('replyer'), $name, $ticket['systemMessage']);
    }

}