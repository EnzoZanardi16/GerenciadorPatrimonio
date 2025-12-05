<?php
// Permite o acesso de qualquer origem (*). Para produção, troque "*" pelo domínio do seu front-end.
header("Access-Control-Allow-Origin: *");
// Define os métodos HTTP permitidos.
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
// Define os cabeçalhos que o navegador pode enviar.
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// O navegador envia uma requisição "preflight" (OPTIONS) para verificar a permissão.
// Esta parte do código responde a essa verificação com sucesso.
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
require_once "../config.php";
header("Content-Type: application/json");

try {
    $pdo = conn();

    // Pega o id do ambiente da URL
    $id = $_GET['id'] ?? null;
    if (!$id || !is_numeric($id)) {
        throw new Exception("ID do ambiente inválido");
    }

    // Consulta itens relacionados a esse ambiente
    $stmt = $pdo->prepare("SELECT * FROM itens_ambiente WHERE id_ambiente = ?");
    $stmt->execute([$id]);
    $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'data' => $itens
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
