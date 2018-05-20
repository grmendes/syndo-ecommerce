<?php
// Your ID and token
//$blogID = '8070105920543249955';
//$authToken = 'OAuth 2.0 token here';
// The data to send to the API

cadastrarEmpresa('Pe de Meia', '12028976', '1025');

function cadastrarEmpresa($nome_fantasia, $cnpj, $idempresa) {

	$postData = array(
		'nome_fantasia' => $nome_fantasia,
	    'cnpj' => $cnpj,
	    'idempresa' => $idempresa
	);

	// Setup cURL
	$ch = curl_init('http://produtos.vitainformatica.com/api/empresa');

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

	//var_dump(json_decode($response));


	echo $response;
}