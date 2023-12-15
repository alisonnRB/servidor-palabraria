<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: *');
header('Content-Type: application/json');

function resposta($codigo, $ok, $dados) {
    http_response_code($codigo);

    $response = [
        'ok' => $ok,
        'dados' => $dados,
    ];

    echo(json_encode($response));
    die;
}

function Buscar(){
    try{
        $conexao = new PDO("mysql:host=localhost;dbname=palabraria", "root", "");
        $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conexao->prepare("SELECT id, palavraEN, palavraPT, imagens, descricao FROM palavras WHERE categoria = 1");
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        resposta(200, true, $resultados);
    }catch(PDOException $e){
        resposta(500, false, []);
    }

}

Buscar();
?>