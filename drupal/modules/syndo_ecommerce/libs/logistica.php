<?php

//calcularFrete('SEDEX', 13065051, 13348863, 100, 'caixa', 2, 3, 4);
//rastreiaPedido();
cadastrarEntrega(1, 'PAC', 13065051, 13348863, 100, 'Caixa', 1, 1, 1); 

function calcularFrete($tipoEntrega, $cepOrigem, $cepDestino, $peso, $tipoPacote, $comprimento, $altura, $largura) {

    // Get cURL resource
    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://hidden-basin-50728.herokuapp.com/calculafrete?tipoEntrega=' . $tipoEntrega . '&cepOrigem=' . $cepOrigem . '&cepDestino=' . $cepDestino . '&peso=' . $peso . '&tipoPacote=' . $tipoPacote . '&comprimento=' . $comprimento . '&altura=' . $altura . '&largura=' . $largura ,
        CURLOPT_USERAGENT => 'Codular Sample cURL Request'
    ));

    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'x-api-key: tmvcglg')
    );

    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    // Close request to clear up some resources
    curl_close($curl);    


    // Send the request

    // Print the date from the response
    $jsonRet = json_decode($resp, true);

//    var_dump($jsonRet['preco']);

    return $jsonRet['preco'];
}

function rastreiaPedido($codigoRastreio) {
    // Get cURL resource
    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://hidden-basin-50728.herokuapp.com/rastrearentrega/cod_rastreio=' . $codigoRastreio . '?apiKey=tmvcglg' ,
        CURLOPT_USERAGENT => 'Codular Sample cURL Request'
    ));

    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'x-api-key: tmvcglg')
    );

    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    // Close request to clear up some resources
    curl_close($curl);    


    // Send the request

    // Print the date from the response
    $jsonRet = json_decode($resp, true);

    var_dump($jsonRet);

    return $jsonRet;
}

function cadastrarEntrega($idProduto, $tipoEntrega, $cepOrigem, $cepDestino, $peso, $tipoPacote, $altura, $largura, $comprimento) {
    
    $postData = array(
        'idProduto' => $idProduto,
        'tipoEntrega' => $tipoEntrega,
        'cepOrigem' => $cepOrigem,
        'cepDestino' => $cepDestino,
        'peso' => $peso,
        'tipoPacote' => $tipoPacote,
        'altura' => $altura,
        'largura' => $largura,
        'comprimento' => $comprimento,
        'apiKey' => '17b8e5f4-5fb6-593c-bf02-cdf57eab0c69',
    );

    // Setup cURL
    $ch = curl_init('https://hidden-basin-50728.herokuapp.com/cadastrarentrega');

    $json = json_encode($postData);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'x-api-key: tmvcglg')
    );

    // Send the request
    $response = curl_exec($ch);

    // Check for errors
    if($response === FALSE){
        die(curl_error($ch));
    }

    // Print the date from the response
    $jsonRet = json_decode($response, true);

    var_dump($response);

    return $jsonRet; 
}
