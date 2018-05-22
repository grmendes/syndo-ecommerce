<?php

//cadastrarProduto('CODVAZ', 'Meia de Lã Elza Frozen', 300, 10.9, 1.2, '', '', '', 'img.jpg', ['idcampo' => 100, 'valor' => 30]);


function cadastrarProduto($codigo, $nome, $idcategoria, $preco, $peso, $dimensao_a, $dimensao_c, $dimensao_l, $imagem_url, $campos) {

    $idempresa = 1024;

    $postData = array(
        'codigo' => $codigo,
        'nome' => $nome,
        'idcategoria' => $idcategoria,
        'preco' => $preco,
        'peso' => $peso,
        'dimensao_a' => $dimensao_a,
        'dimensao_c' => $dimensao_c,
        'dimensao_l' => $dimensao_l,
        'idempresa' => $idempresa,
        'imagem_url' => $imagem_url,
        'campos' => $campos
    );

    // Setup cURL
    $ch = curl_init('http://produtos.vitainformatica.com/api/produto?idempresa=1024');

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
    $jsonRet = json_decode($response, true);

//    echo $response;

    return $jsonRet["id"]; 

}