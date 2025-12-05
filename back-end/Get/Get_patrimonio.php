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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id_ambiente'] ?? null;

    if (!$id) {
        echo json_encode([
            'status' => 'error',
            'message' => 'ID do ambiente não fornecido'
        ]);
        exit();
    }

    try {
        $pdo = conn();

        $stmt = $pdo->prepare("SELECT * FROM patrimonios  WHERE ambientes_id_ambientes = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'data' => $result
        ]);
        exit();
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erro ao acessar a view: ' . $e->getMessage()
        ]);
        exit();
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Método inválido. Use GET.'
    ]);
    exit();
}
