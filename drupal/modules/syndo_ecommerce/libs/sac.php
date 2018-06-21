<?php

date_default_timezone_set("America/Sao_Paulo");

//cadastrarTicketCliente('olar', 'eu', 'gnunes');
//visualizarTicketsCliente('syndoecommerce');
//adicionarMensagem('olar22222222', 'eu', 'syndoecommerce', '20180613032805');

function cadastrarTicketCliente($msg, $sender, $cliente) {

    $postData = array(
        'timestamp' => date('Y-m-d') . 'T' . date('H:i'),
        'sender' => $sender,
        'message' => $msg,
    );

    // Setup cURL
    $ch = curl_init('https://centralatendimento-mc857.azurewebsites.net/tickets/c1f33357c6f9b3b559037f838c3501ae4eedb09e/' . $cliente);

    $json = json_encode($postData);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json')
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

function visualizarTicketsCliente($cliente) {

    // Get cURL resource
    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://centralatendimento-mc857.azurewebsites.net/tickets/c1f33357c6f9b3b559037f838c3501ae4eedb09e/' . $cliente,
        CURLOPT_USERAGENT => 'Codular Sample cURL Request'
    ));

    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    // Close request to clear up some resources
    curl_close($curl);


    // Send the request

    // Print the date from the response
    $jsonRet = json_decode($resp, true);

    return $jsonRet;
}


function adicionarMensagem($msg, $sender, $cliente, $ticket) {

    $postData = array(
        'timestamp' => date('Y-m-d') . 'T' . date('H:i'),
        'sender' => $sender,
        'message' => $msg,
    );

    // Setup cURL
    $ch = curl_init('https://centralatendimento-mc857.azurewebsites.net/tickets/c1f33357c6f9b3b559037f838c3501ae4eedb09e/' . $cliente . '/ticket/' . $ticket);

    $json = json_encode($postData);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json')
    );


    // Send the request
    $response = curl_exec($ch);

    // Check for errors
    if($response === FALSE){
        die(curl_error($ch));
    }

    // Print the date from the response
    $jsonRet = json_decode($response, true);

//    var_dump($jsonRet);

    return $jsonRet;

}
