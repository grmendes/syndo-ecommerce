<?php
// Your ID and token
//$blogID = '8070105920543249955';
//$authToken = 'OAuth 2.0 token here';
// The data to send to the API

cadastrarUsuario('guilherme', 'guilherme@gmail.com', '123');

function cadastrarUsuario($nome, $email, $senha) {

//	$nome = 'guilherme';
//	$email = 'guilherme@gmail.com';
//	$senha = '123';


	$postData = array(
	    'nome' => $nome,
	    'email' => $email,
	    'senha' => $senha
	);

	// Setup cURL
	$ch = curl_init('http://produtos.vitainformatica.com/api/usuario_empresa/login');

	$json = json_encode($postData);

	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    'Content-Type: application/json',
		'Authorization: Bearer 90ddc7c5634095af7f087ed450ab4962b9f06dde9064e5ecb96bd0c3493af4bd')
	);

	// Send the request
	$response = curl_exec($ch);

	// Check for errors
	if($response === FALSE){
	    die(curl_error($ch));
	}

	// Print the date from the response
	$jsonRet = json_decode($response);

	//echo $jsonRet;
}