<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: *');

function resposta($codigo, $ok, $msg) {
    header('Content-Type: application/json');
    http_response_code($codigo);

    $response = [
        'ok' => $ok,
        'msg' => $msg,
    ];

    echo(json_encode($response));
    die;
}

function update() {
    $conexao = new PDO("mysql:host=localhost;dbname=palabraria", "root", "");

    if (!isset($_POST['antigo']) || empty($_POST['antigo'])) {
        resposta(400, false, "É necessário selecionar uma palavra existente.");
    }

    $stm = $conexao->prepare('UPDATE palavras SET palavraEN = :palavraEN, palavraPT = :palavraPT, categoria = :categoria, imagens = :imagens, descricao = :descricao WHERE palavraEN = :antigo');
    $stm->bindParam(':antigo', $_POST['antigo']);

    if (isset($_POST['palavraEN']) && !empty($_POST['palavraEN'])) {
        $stm->bindParam(':palavraEN', $_POST['palavraEN']);
    } else {
        resposta(400, false, "É necessário que exista uma palavra em Espanhol.");
    }

    if (isset($_POST['palavraPT']) && !empty($_POST['palavraPT'])) {
        $stm->bindParam(':palavraPT', $_POST['palavraPT']);
    } else {
        $stm->bindValue(':palavraPT', ''); 
    }
    
    if (isset($_POST['categoria']) && !empty($_POST['categoria'])) {
        $stm->bindParam(':categoria', $_POST['categoria']);
    } else {
        $stm->bindValue(':categoria', ''); 
    }

    if (isset($_POST['descricao']) && !empty($_POST['descricao'])) {
        $stm->bindParam(':descricao', $_POST['descricao']);
    } else {
        $stm->bindValue(':descricao', ''); 
    }

    $destino = './img/';
    $list = [];

    $stmt = $conexao->prepare("SELECT imagens FROM palavras WHERE palavraEN = :antigo");
    $stmt->execute([':antigo' => $_POST['antigo']]);

    $imagensAntigasJSON = $stmt->fetchColumn();
    $imagensAntigas = json_decode($imagensAntigasJSON);

    $cont = 0;
    
    foreach ($imagensAntigas as $imagemAntiga) {
        $cont += 1;
        $index = strval($cont);

        if($_POST['att'.$index] == 'false'){
            if (file_exists($destino . $imagemAntiga)) {
                unlink($destino . $imagemAntiga);
            }
        }else{
            $list[] = $imagemAntiga;
        }
        
    }

    for ($i = 1; $i < 7; $i++) {

        if (!empty($_FILES['image'.$i]['tmp_name'])) {
            $arquivoTemporario = $_FILES['image'.$i]['tmp_name'];
            $extensao = pathinfo($_FILES['image'.$i]['name'], PATHINFO_EXTENSION);
            $nomeUnico = $i . time() . '.' . $extensao;

            if (move_uploaded_file($arquivoTemporario, $destino . $nomeUnico)) {
                $list[] = $nomeUnico;
            } else {
                resposta(500, false, "Falha ao mover o arquivo. Caminho: " . $destino . $nomeUnico);
            }
        }
    }

    $imagensJSON = json_encode($list);
    $stm->bindParam(':imagens', $imagensJSON);

    if ($stm->execute()) {
        resposta(200, true, "Atualização realizada com sucesso.");
    } else {
        resposta(500, false, "Erro ao atualizar no banco de dados.");
    }
}

update();
?>
