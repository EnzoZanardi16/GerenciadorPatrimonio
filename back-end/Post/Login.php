<?php

ini_set("display_errors", 1);
require_once "../config.php";
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    // 1. Verifica se os campos de email e senha existem
    if (isset($data['email']) && isset($data['senha'])) {
        $email = $data['email'];
        $senha_fornecida = $data['senha'];

        $pdo = conn();

        // 2. Busca o usuário pelo email, incluindo a senha criptografada
        $smt = $pdo->prepare('SELECT id_usuario, usuario_email, senha FROM usuarios WHERE usuario_email = ?');
        $smt->execute([$email]);
        $user = $smt->fetch(PDO::FETCH_ASSOC);
        
        // Criptografa a senha fornecida com SHA-512 para comparação
        $senha_hash_fornecida = hash('sha512', $senha_fornecida);

        // 3. Verifica se o usuário foi encontrado E se a senha criptografada corresponde
        if ($user && $senha_hash_fornecida === $user['senha']) {
            
            // --- LÓGICA DE GERAÇÃO E LIMPEZA DE TOKEN ---
            
            // Gera token único com SHA512
            $random_string = random_bytes(32);
            $token = hash('sha512', $random_string);

            // Exclui todos os tokens expirados para o usuário
            $smt_delete = $pdo->prepare("DELETE FROM token WHERE usuarios_id_usuario = ? AND created_at < NOW() - INTERVAL ? SECOND");
            $smt_delete->execute([$user['id_usuario'], $timeout_duration]);

            // Salva o novo token na tabela
            $smt_insert = $pdo->prepare("INSERT INTO token (usuarios_id_usuario, token) VALUES (?, ?)");
            $smt_insert->execute([$user['id_usuario'], $token]);

            if ($smt_insert->rowCount() > 0) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login bem-sucedido!',
                    'token'   => $token,
                    'user'    => [
                        'id'    => $user['id_usuario'],
                        'email' => $user['usuario_email']
                    ]
                ]);
                exit();
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Erro ao salvar login.'
                ]);
                exit();
            }
        } else {
            // Se o usuário não foi encontrado OU a senha está incorreta
            echo json_encode([
                'status' => 'error',
                'message' => 'Credenciais inválidas.'
            ]);
            exit();
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Campos email e senha ausentes.'
        ]);
        exit();
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Método inválido. Use POST.'
    ]);
    exit();
}