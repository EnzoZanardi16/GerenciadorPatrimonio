<?php
require_once "../config.php";
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    try {
        $pdo = conn(); // função conn() do config.php

        // Recebe JSON
        $data = json_decode(file_get_contents("php://input"), true);

        // EXTRAI O ID DO USUÁRIO DO JSON
        $id_usuario     = $data["id_usuario"] ?? null;

        // EXTRAÇÃO DOS DADOS
        $id             = $data["patrimonio"]["id_patrimonio"] ?? null; // CHAVE PRIMÁRIA PARA O WHERE
        $num            = $data["patrimonio"]["num_patrimonio"] ?? null; // Coluna a ser potencialmente atualizada
        $nome           = $data["patrimonio"]["patrimonio_nome"] ?? null;
        $atividade      = $data["patrimonio"]["patrimonio_del"] ?? null;
        $status         = $data["patrimonio"]["status"] ?? null;
        $img            = $data["patrimonio"]["patrimonio_img"] ?? null;
        $denominacao    = $data["patrimonio"]["denominacao"] ?? null;
        $origem         = $data["patrimonio"]["ambientes_id_ambientes"] ?? null;

        // Verifica se o ID do patrimônio e do usuário existem
        if ($id && $id_usuario) { 
            // DEFINE A VARIÁVEL DE SESSÃO DO MYSQL
            $pdo->exec("SET @id_usuario_logado = " . (int)$id_usuario);
            
            $stmt = $pdo->prepare("
                UPDATE patrimonios SET 
                    num_patrimonio = COALESCE(:num, num_patrimonio),             -- NOVO: Inclui a atualização do num_patrimonio
                    patrimonio_nome = COALESCE(:nome, patrimonio_nome),
                    patrimonio_del = COALESCE(:atividade, patrimonio_del),
                    status = COALESCE(:status, status),
                    patrimonio_img = COALESCE(:img, patrimonio_img),
                    denominacao = COALESCE(:denominacao, denominacao),
                    ambientes_id_ambientes = COALESCE(:origem, ambientes_id_ambientes)
                WHERE id_patrimonio = :id                                       -- ALTERADO: Usando id_patrimonio para localizar
            ");

            // Faz o BIND dos parâmetros
            $stmt->bindParam(':id', $id); // ID usado no WHERE
            $stmt->bindParam(':num', $num); // NOVO: num_patrimonio
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':atividade', $atividade);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':img', $img);
            $stmt->bindParam(':denominacao', $denominacao);
            $stmt->bindParam(':origem', $origem);
            
            $stmt->execute();

            // Resposta de sucesso (incluindo o novo num_patrimonio para feedback)
            echo json_encode([
                'status'  => 'success',
                'message' => 'Patrimônio atualizado com sucesso!',
                'data'    => [
                    'id_patrimonio'   => $id,
                    'num_patrimonio'  => $num, 
                    'patrimonio_nome' => $nome,
                    'status'          => $status
                ]
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'ID do patrimônio (id_patrimonio) e ID do usuário são obrigatórios para atualizar.'
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao atualizar patrimônio: ' . $e->getMessage()
        ]);
    }
} else {
    // Notei que a sua mensagem de erro sugere 'PUT', mas o método usado é 'PATCH'.
    // Mantenho PATCH, que é o correto para updates parciais.
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Método inválido. Use PATCH.'
    ]);
}
?>