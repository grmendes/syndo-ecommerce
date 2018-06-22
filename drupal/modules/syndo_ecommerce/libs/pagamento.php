<?php

//pagamentoBoleto('Gustavo', 81932308091, 'Rua Teste', 13065054, 1000);
//situacaoBoleto('5b117b4e36a6550014df4299');
//registrarCartao(8723456312345768, true);
//pagamentoCartao('Gustavo', 81932308091, 8723456312345768, 07, 2027, 772, 1000, 10);

function pagamentoBoleto($name, $cpf, $address, $cep, $value) {
    
    $postData = array(
        'clientName' => $name,
        'cpf' => $cpf,
        'address' => $address,
        'cep' => $cep,
        'value' => $value,
    );

    // Setup cURL
    $ch = curl_init('https://payment-server-mc851.herokuapp.com/payments/bankTicket');

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

    error_log($jsonRet);

    return $jsonRet; 
}


function situacaoBoleto($code) {

    // Get cURL resource
    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://payment-server-mc851.herokuapp.com/payments/bankTicket/' . $code . '/status',
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

    // var_dump($jsonRet);

    return $jsonRet;
}

function registrarCartao($card_number, $credit) {
    
    $postData = array(
        'number' => $card_number,
        'hasCredit' => $credit,
    );

    // Setup cURL
    $ch = curl_init('https://payment-server-mc851.herokuapp.com/creditCard');

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

    // var_dump($jsonRet);

    return $jsonRet; 
}





function pagamentoCartao($name, $cpf, $card_number, $month, $year, $security_code, $value, $instalments) {
    
    $postData = array(
        'clientCardName' => $name,
        'cpf' => $cpf,
        'cardNumber' => $card_number,
        'month' => $month,
        'year' => $year,
        'securityCode' => $security_code,
        'value' => $value,
        'instalments' => $instalments,
    );

    // Setup cURL
    $ch = curl_init('https://payment-server-mc851.herokuapp.com/payments/creditCard');

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

    // var_dump($jsonRet);

    return $jsonRet; 
}


