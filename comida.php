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

    echo(json_encode($response, JSON_UNESCAPED_UNICODE));
    die;
}

function Buscar(){
    try{
        $conexao = new PDO("mysql:host=localhost;dbname=palabraria", "root", "");
        $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conexao->prepare("SELECT id, palavraEN, palavraPT, imagens, descricao FROM palavras WHERE categoria = 1");
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Corrigir encoding para cada string no array
        foreach ($resultados as &$item) {
            $item['palavraEN'] = utf8_encode($item['palavraEN']);
            $item['palavraPT'] = utf8_encode($item['palavraPT']);
            $item['descricao'] = utf8_encode($item['descricao']);

            // Se as imagens são strings JSON, você também pode decodificá-las e depois codificá-las novamente
            $item['imagens'] = json_encode(json_decode($item['imagens']), JSON_UNESCAPED_UNICODE);
        }

        resposta(200, true, $resultados);
    } catch(PDOException $e){
        resposta(500, false, []);
    }
}

Buscar();
?>
