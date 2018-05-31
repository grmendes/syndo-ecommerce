<?php

//buscarCPF(89062920071);
//inserirScore(89062920071, 24);
//realizarPagamento(89062920071, 4000, 4000);

function buscarCPF($cpf) {

    // Get cURL resource
    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://glacial-brook-98386.herokuapp.com/score/' . $cpf,
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

function inserirScore($cpf, $score) {
    
    $postData = array(
        'score' => $score,
    );

    // Setup cURL
    $ch = curl_init('https://glacial-brook-98386.herokuapp.com/score/' . $cpf);

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

    return $jsonRet; 
}

function realizarPagamento($cpf, $valor_pago, $valor_total) {
    
    $postData = array(
        'total_paid' => $valor_pago,
        'total_value' => $valor_total,
    );

    // Setup cURL
    $ch = curl_init('https://glacial-brook-98386.herokuapp.com/payment/' . $cpf);

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

    return $jsonRet; 
}



