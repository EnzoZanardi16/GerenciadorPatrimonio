<?php
require_once "../config.php";
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = conn(); // função conn() vinda do config.php

        // Recebe JSON ou formulário
        $data = json_decode(file_get_contents("php://input"), true);

        $nome   = $data["ambiente_nome"]   ?? null;
        $status = $data["ambiente_del"]    ?? "ativo"; // padrão ativo

        if ($nome) {
            $stmt = $pdo->prepare("
                INSERT INTO ambientes (ambiente_nome, ambiente_del) 
                VALUES (:nome, :status)
            ");

            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':status', $status);

            $stmt->execute();

            echo json_encode([
                'status' => 'success',
                'message' => 'Usuário criado com sucesso!',
                'data' => [
                    'ambiente_nome'  => $nome,
                    'ambiente_del'   => $status
                ]
            ]);
            exit();
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Campos obrigatórios faltando (ambiente_nome).'
            ]);
            exit();
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao inserir ambiente: ' . $e->getMessage()
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
