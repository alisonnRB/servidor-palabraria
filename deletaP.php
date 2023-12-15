<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: *');
header('Content-Type: application/json');

function resposta($codigo, $ok) {
    http_response_code($codigo);

    $response = [
        'ok' => $ok,
    ];

    echo(json_encode($response));
    die;
}

function apagar($body){
    $destino = './img/'; // Adicione um ponto e vírgula aqui

    try{
        $conexao = new PDO("mysql:host=localhost;dbname=palabraria", "root", "");

        $stmt = $conexao->prepare("SELECT imagens FROM palavras WHERE palavraEN = :antigo");
        $stmt->execute([':antigo' => $body->palavra]);
    
        $imagensAntigasJSON = $stmt->fetchColumn();
        
        $imagensAntigas = json_decode($imagensAntigasJSON);
        
        if ($imagensAntigas !== null) { // Verifique se $imagensAntigas não é null
            foreach ($imagensAntigas as $imagemAntiga) {
                if (file_exists($destino . $imagemAntiga)) {
                    unlink($destino . $imagemAntiga);
                }
            }
        }

        $stmt = $conexao->prepare("DELETE FROM palavras WHERE palavraEN = :palavra");
        $stmt->execute([':palavra' => $body->palavra]);

        resposta(200, true);
    } catch(PDOException $e) {
        resposta(500, false);
    }
}

$body = file_get_contents('php://input');
$body = json_decode($body);

apagar($body);
?>
