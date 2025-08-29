<?php
require_once "../config.php";
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['email'])) {
        $email = trim($data['email']);

        $pdo = conn();

        // Verifica se existe usuário com esse email
        $smt = $pdo->prepare('SELECT * FROM usuarios WHERE usuario_email = ?');
        $smt->execute([$email]);
        $user = $smt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Gera token único
            $token = bin2hex(random_bytes(32));

            // Salva token na tabela login
            $smt = $pdo->prepare("INSERT INTO token (id_usuario, token) VALUES (?, ?)");
            $smt->execute([$user['id'], $token]);

            if ($smt->rowCount() > 0) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login realizado via SSO',
                    'token'   => $token,
                    'user'    => [
                        'id'    => $user['id'],
                        'email' => $user['email']
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
            echo json_encode([
                'status' => 'error',
                'message' => 'Email não encontrado.'
            ]);
            exit();
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Campo email ausente.'
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
