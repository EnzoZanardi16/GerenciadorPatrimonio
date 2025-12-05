<?php
session_start();
require "../config.php";

header ("Access-Control-Allow-Origin: *");
header ("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header ("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
    http_response_code(200);
    exit;
}
header("Content-Type: application/json");
$pdo = conn();
$jsonRaw = file_get_contents("php://input");
$data = json_decode($jsonRaw, true)?? [];

// Se não recebeu token pelo JSON, tenta pelo POST comum
$token = $data["token"] ?? ($_POST["token"] ?? ($_GET["token"] ?? null));


// echo $token;
if (!$token) {
    echo json_encode([
        "status" => "error",
        "message" => "Nenhum token recebido."
    ]);
    exit;
}
// 🔥 APAGA O TOKEN NA TABELA CORRETA
$stmt = $pdo->prepare("DELETE FROM token WHERE token = ?");
$stmt->execute([$token]);

// Destrói sessão
session_unset();
session_destroy();

echo json_encode([
    "status" => "success",
    "message" => "Logout realizado com sucesso."
    
]);
header("Location:http://localhost/patrimonio/front-end/view/index.php?alert=logout");
exit;
?>
