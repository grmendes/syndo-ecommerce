<?php
// Your ID and token
//$blogID = '8070105920543249955';
//$authToken = 'OAuth 2.0 token here';
// The data to send to the API

//consultarCategoria();


function consultarCategoria() {

    // Get cURL resource
    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'http://produtos.vitainformatica.com/api/categoria',
        CURLOPT_USERAGENT => 'Codular Sample cURL Request'
    ));

    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer 90ddc7c5634095af7f087ed450ab4962b9f06dde9064e5ecb96bd0c3493af4bd')
    );


    // Send the request & save response to $resp
    $resp = curl_exec($curl);
    // Close request to clear up some resources
    curl_close($curl);    

    // Send the request

    // Print the date from the response
    $jsonRet = json_decode($resp, true);

//    echo count($jsonRet);

    return $jsonRet;

//    var_dump($jsonRet);


//    echo $resp;

}