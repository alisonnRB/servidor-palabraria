<?php
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: *');


function resposta($codigo, $ok, $dados) {
    header('Content-Type: application/json');
    http_response_code($codigo);

    $response = [
        'ok' => $ok,
        'dados' => $dados,
    ];

    echo(json_encode($response));
    die;
}

function Buscar($body){
    try{
        
        $conexao = new PDO("mysql:host=localhost;dbname=palabraria", "root", "");
        $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $letra = $body->digito . '%';


        $stmt = $conexao->prepare("SELECT id, palavraEN, palavraPT, categoria, imagens, descricao FROM palavras WHERE palavraEN LIKE ? LIMIT 6");
        $stmt->execute([$letra]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $list = array();

        foreach ($resultados as $row) {
            $list[$row['id']] = [
                'palavraEN' => $row['palavraEN'],
                'palavraPT' => $row['palavraPT'],
                'categoria' => $row['categoria'],
                'imagens' => $row['imagens'],
                'descricao' => $row['descricao']
            ];
        }

        resposta(200, true, $list);
    }catch(PDOException $e){
        resposta(500, false, []);
    }

}

$body = file_get_contents('php://input');


$body = json_decode($body);


Buscar($body);
?>