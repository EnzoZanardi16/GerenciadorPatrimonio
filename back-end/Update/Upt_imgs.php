<?php
require_once "../config.php";
header("Content-Type: application/json");

// Define o método permitido. PATCH é ideal para updates parciais.
if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    try {
        $pdo = conn(); // função conn() do config.php

        // Recebe e decodifica o JSON do corpo da requisição
        $data = json_decode(file_get_contents("php://input"), true);

        // EXTRAÇÃO DOS DADOS ESSENCIAIS E IMAGENS
        $id_usuario     = $data["id_usuario"] ?? null; // ID do usuário para log/trigger
        $id             = $data["patrimonio"]["id_patrimonio"] ?? null; // chave primária
        $img            = $data["patrimonio"]["patrimonio_img"] ?? null;   // Imagem 1 (nome do arquivo)
        $img2           = $data["patrimonio"]["patrimonio_img2"] ?? null;  // Imagem 2 (nome do arquivo)

        // Verifica se o ID do patrimônio e do usuário existem.
        // Além disso, verifica se pelo menos UMA das imagens foi enviada.
        if ($id && $id_usuario && ($img || $img2)) { 
            
            // DEFINE A VARIÁVEL DE SESSÃO DO MYSQL
            $pdo->exec("SET @id_usuario_logado = " . (int)$id_usuario);
            
            // Prepara a query de UPDATE
            // Usamos COALESCE para que apenas os campos que vierem no JSON sejam atualizados,
            // e os não enviados mantenham o valor original (NULL nesse contexto, mas
            // o COALESCE garante que o valor da coluna seja mantido se a variável for NULL).
            $stmt = $pdo->prepare("
                UPDATE patrimonios SET 
                    patrimonio_img = COALESCE(:img, patrimonio_img),
                    patrimonio_img2 = COALESCE(:img2, patrimonio_img2) 
                WHERE id_patrimonio = :id
            ");

            // Faz o BIND dos parâmetros para segurança
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':img', $img);
            $stmt->bindParam(':img2', $img2);
            
            // Executa o statement
            $stmt->execute();

            // Verifica se alguma linha foi afetada para dar um feedback mais preciso
            if ($stmt->rowCount() > 0) {
                 // Resposta de sucesso
                echo json_encode([
                    'status'  => 'success',
                    'message' => 'Imagens do Patrimônio atualizadas com sucesso!',
                    'data'    => [
                        'id_patrimonios'  => $id,
                        'patrimonio_img'  => $img,
                        'patrimonio_img2' => $img2
                    ]
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Patrimônio não encontrado ou nenhuma alteração nas imagens foi realizada.'
                ]);
            }
           
        } else {
            // Se faltarem IDs obrigatórios ou nenhuma imagem for enviada
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'ID do patrimônio, ID do usuário, e ao menos uma das imagens (patrimonio_img ou patrimonio_img2) são obrigatórios para atualizar.'
            ]);
        }
    } catch (Exception $e) {
        // Trata erros de conexão ou execução
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro interno do servidor: ' . $e->getMessage()
        ]);
    }
} else {
    // Se o método HTTP não for PATCH
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Método inválido. Use PATCH.'
    ]);
}
?>