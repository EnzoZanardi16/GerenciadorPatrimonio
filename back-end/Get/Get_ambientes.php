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
require_once "../config.php"; // Ajuste se o caminho do config.php for diferente
header("Content-Type: application/json");
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

try {
    $pdo = conn();
    $stmt = $pdo->query("SELECT id_ambientes, ambiente_nome, categoria, localizacao FROM ambientes WHERE ambiente_del = 'ativo'");
    $ambientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "data" => $ambientes
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
