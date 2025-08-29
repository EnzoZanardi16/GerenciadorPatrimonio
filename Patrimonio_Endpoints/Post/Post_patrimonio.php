<?php
require_once "../config.php";
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = conn(); // função conn() vinda do config.php

        // Recebe JSON ou formulário
        $data = json_decode(file_get_contents("php://input"), true);

        $num_patrimonio = $data["num_patrimonio"] ?? null;
        $nome           = $data["patrimonio_nome"] ?? null;
        $atividade      = $data["patrimonio_del"] ?? "ativo"; 
        $status = "pendente"; // sempre inicia pendente
        $img            = $data["patrimonio_img"] ?? null; 
        $denominacao    = $data["denominacao"] ?? null; 
        $origem         = $data["ambientes_id_ambientes"] ?? null; 

        // Validação mínima
        if ($num_patrimonio && $nome) {
            $stmt = $pdo->prepare("
                INSERT INTO patrimonios 
                    (num_patrimonio, patrimonio_nome, patrimonio_del, status, patrimonio_img, denominacao, ambientes_id_ambientes) 
                VALUES 
                    (:num_patrimonio, :nome, :atividade, :status, :img, :denominacao, :origem)
            ");

            $stmt->bindParam(':num_patrimonio', $num_patrimonio);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':atividade', $atividade);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':img', $img);
            $stmt->bindParam(':denominacao', $denominacao);
            $stmt->bindParam(':origem', $origem);

            $stmt->execute();

            http_response_code(201);
            echo json_encode([
                'status'  => 'success',
                'message' => 'Patrimônio cadastrado com sucesso!',
                'data'    => [
                    'num_patrimonio' => $num_patrimonio,
                    'patrimonio_nome' => $nome,
                    'status' => $status
                ]
            ]);
            exit();
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Campos obrigatórios faltando (num_patrimonio, patrimonio_nome).'
            ]);
            exit();
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao inserir patrimônio: ' . $e->getMessage()
        ]);
        exit();
    }
} else {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Método inválido. Use POST.'
    ]);
    exit();
}
