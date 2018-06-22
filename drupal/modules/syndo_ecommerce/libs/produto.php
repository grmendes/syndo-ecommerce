<?php

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;

function cadastrarProduto($codigo, $nome, $idcategoria, $preco, $peso, $dimensao_a, $dimensao_c, $dimensao_l, $imagem_url, $campos, $descricao) {
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
        'campos' => $campos,
        'descricao' => $descricao,
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

    error_log(print_r($jsonRet, true));

    return $jsonRet["id"];
}

function visualizarDadosProduto($codigo) {

    // Get cURL resource
    $curl = curl_init();
    // Set some options - we are passing in a useragent too here
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'http://produtos.vitainformatica.com/api/produto?idempresa=1024&codigo='.$codigo,
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

    return $jsonRet[0];
}

/**
 * @param $codigos
 * @param string $nome
 *
 * @return array|
 *
 * @throws GuzzleException
 */
function listarProdutos($codigos, $nome = '', $categoria = '', $rawData = false) {
    $httpClient = new HttpClient([
        'base_uri' => 'http://produtos.vitainformatica.com/api/',
    ]);

    try {

        $response = $httpClient->request(
            'GET',
            'produto',
            [
                'headers' => [
                    'Authorization' => 'Bearer 90ddc7c5634095af7f087ed450ab4962b9f06dde9064e5ecb96bd0c3493af4bd',
                ],
                'query' => [
                    'idempresa' => 1024,
                    'idcategoria' => $categoria,
                    'codigo' => implode(',', $codigos),
                ],
            ]
        );

        $responseData = json_decode($response->getBody()->getContents(), true);

        foreach ($responseData as $produto) {
            if (empty($nome) || false !== stripos($produto['nome'], $nome)) {
                if (empty($categoria) || $produto['idcategoria'] == $categoria) {
                    if (false == $rawData) {
                        yield getProductFields($produto, array_search($produto['codigo'], $codigos));
                    } else{
                        yield $produto;
                    }
                }
            }
        }

    } catch (GuzzleException $e) {
        throw $e;
    }
}

function getProductFields($produto, $nid) {
    return [
        'codigo' => $produto['codigo'],
        'nome' => empty($nid) ? $produto['nome'] : \Drupal\Core\Link::createFromRoute(
            $produto['nome'],
            'entity.node.canonical',
            [
                'node' => $nid,
            ]
        )->toString(),
        'preco' => 'R$' . number_format($produto['preco'], 2, ',', '.'),
    ];
}
