<?php
require_once "../config.php";
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = conn();

        // Recebe JSON enviado no body
        $data = json_decode(file_get_contents("php://input"), true);

        $usuario_id    = $data["usuarios_id_usuario"] ?? null;
        $ambiente_id   = $data["ambientes_id_ambientes"] ?? null;
        $patrimonio_id = $data["patrimonios_num_patrimonio"] ?? null;

        // Validação
        if (!$usuario_id || !$ambiente_id || !$patrimonio_id) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Campos obrigatórios faltando (usuario, ambiente, patrimonio).'
            ]);
            exit();
        }

        // Insert (verificacao_del sempre 'ativo')
        $stmt = $pdo->prepare("
            INSERT INTO verificacao_ambiente (
                data_hora, verificacao_del, usuarios_id_usuario, ambientes_id_ambientes, patrimonios_num_patrimonio
            ) VALUES (NOW(), 'ativo', ?, ?, ?)
        ");

        $stmt->execute([
            $usuario_id,
            $ambiente_id,
            $patrimonio_id
        ]);

        echo json_encode([
            'status' => 'success',
            'message' => 'Registro inserido com sucesso!',
            'id_inserido' => $pdo->lastInsertId()
        ]);
        exit();
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao inserir: ' . $e->getMessage()
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
