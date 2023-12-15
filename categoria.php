<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: *');

// TODO função que encerra as operações e envia uma resposta para a API trabalhar
function resposta($codigo, $ok, $categorias) {
    http_response_code($codigo);

    $response = [
        'ok' => $ok,
        'categorias' => $categorias,
    ];

    echo(json_encode($response));
    die;
}

function quaisGeneros(){
    try{
        $conexao = new PDO("mysql:host=localhost;dbname=palabraria", "root", "");

        $stmt = $conexao->prepare("SELECT id, nome FROM categoria");
        $stmt->execute();
        $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $list = array();

        foreach ($stmt as $row) {
            $list[$row['id']] = $row['nome'];
        }

        resposta(200, true, $list);
    }catch(Exception $e){
        resposta(500, false, []);
    }
}

quaisGeneros();


?>