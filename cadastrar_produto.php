<?php
// Your ID and token
//$blogID = '8070105920543249955';
//$authToken = 'OAuth 2.0 token here';
// The data to send to the API
$postData = array(
	'codigo' => 'COD01',
    'nome' => 'Meia de Lã Elza Frozen',
    'idcategoria' => 1,
    'preco' => 10.9,
    'peso' => 1.2,
    'dimensao_a' => 1,
    'dimensao_c' => 3,
    'dimensao_l' => 2,
    'idempresa' => 1,
    'imagem_url' => 'img.jpg',
    'campos' => ['idcampo' => 1, 'valor' => 30]
);

// Setup cURL
$ch = curl_init('http://produtos.vitainformatica.com/api/produto?idempresa=1&idusuario_empresa=1');

$json = json_encode($postData);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($json))
);

// Send the request
$response = curl_exec($ch);

// Check for errors
if($response === FALSE){
    die(curl_error($ch));
}

// Print the date from the response
$jsonRet = json_decode($response);

var_dump(json_decode($response));


//echo $jsonRet;