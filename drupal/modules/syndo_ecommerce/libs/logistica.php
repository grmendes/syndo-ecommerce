<?php

//calcularFrete('SEDEX', 13065051, 13348863, 100, 'caixa', 2, 3, 4);
//calcularTodasOpcoesFrete(13065051, 13348863, 100, 'caixa', 2, 3, 4);
//rastreiaPedido('53a40010-75d9-11e8-945c-275b53f7cbce');
//cadastrarEntrega(62345, 'PAC', '13348863', '13348863', 1000, 'Caixa', 10, 10, 10);
//cadastrarEntrega(1, 'PAC', '13348863', '13348863', 1000, 'Caixa', 10, 10, 10);

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

    return $jsonRet;
}

function rastreiaPedido($codigoRastreio) {
    // Get cURL resource
    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://hidden-basin-50728.herokuapp.com/rastrearentrega/' . $codigoRastreio . '?apiKey=b5fa7551-226b-5568-b81d-5bb9603b3dbc' ,
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

//    var_dump($resp);

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
        'apiKey' => 'b5fa7551-226b-5568-b81d-5bb9603b3dbc',
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

//    return $jsonRet['codigoRastreio'];
    return $response;
}


function calcularTodasOpcoesFrete($cepOrigem, $cepDestino, $peso, $tipoPacote, $comprimento, $altura, $largura) {

    // Get cURL resource
    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://hidden-basin-50728.herokuapp.com/calculatodosfretes?cepOrigem=' . $cepOrigem . '&cepDestino=' . $cepDestino . '&peso=' . $peso . '&tipoPacote=' . $tipoPacote . '&comprimento=' . $comprimento . '&altura=' . $altura . '&largura=' . $largura ,
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

    return $jsonRet;
}

function calcularFreteTotal(string $cep, string $entrega, array $cart_items) {
    $preco = 0;
    $prazo = 0;

    $pesoTotal = 0;
    $comprimentoTotal = 0;
    $alturaTotal = 0;
    $larguraTotal = 0;

    foreach ($cart_items as $key => $value) {
        $pesoTotal += $value['peso'] * $value['qtd'];
        $comprimentoTotal += $value['dimensoes']['comprimento'] * $value['qtd'];
        $larguraTotal += $value['dimensoes']['largura'] * $value['qtd'];
        $alturaTotal += $value['dimensoes']['altura'] * $value['qtd'];
    }

    $frete = calcularFrete($entrega,"13083-872", $cep, $pesoTotal, 'Caixa', $comprimentoTotal, $alturaTotal, $larguraTotal);

    $preco = intval($frete["preco"]);
    $prazo = intval($frete["prazo"]);

    return array('preco' => $preco, 'prazo' => $prazo);
}