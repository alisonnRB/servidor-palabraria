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

function inserir() {
    $conexao = new PDO("mysql:host=localhost;dbname=palabraria", "root", "");
    $stm = $conexao->prepare('INSERT INTO palavras(palavraEN, palavraPT, categoria, imagens, descricao) VALUES (:palavraEN, :palavraPT, :categoria, :imagens, :descricao)');
    
    if (isset($_POST['palavraEN']) && !empty($_POST['palavraEN'])) {
        $stm->bindParam(':palavraEN', $_POST['palavraEN']);
    } else {
        resposta(500, false, "É necessário que exista uma palavra em Espanhol.");
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
    


    if (!empty($_FILES['image'.'1']['tmp_name'])) {
        $destino = './img/';
        $list = array();
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
    } else {
        $stm->bindValue(':imagens', '');
    }

    if ($stm->execute()) {
        resposta(200, true, "Inserção realizada com sucesso.");
    } else {
        resposta(500, false, "Erro ao inserir no banco de dados.");
    }
}

inserir();
?>