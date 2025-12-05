<?php
// CRUCIAL: Impede que erros e avisos PHP sejam impressos, o que corromperia o JSON.
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL); 
// ----------------------------------------------------

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

// AJUSTE O CAMINHO: Se 'config.php' está na raiz de 'back-end', use abaixo.
// Caso contrário (se estiver em Update/), use require_once '../Update/config.php';
require_once '../config.php'; 

// Verifica se a variável de conexão ($conn, objeto PDO) foi definida
if (!isset($conn) || !($conn instanceof PDO)) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Erro Crítico: A variável \$conn não é um objeto PDO válido. Verifique o caminho do config.php."]);
    exit();
}

// Verifica se o id_patrimonio foi fornecido
if (!isset($_GET['id_patrimonio']) || empty($_GET['id_patrimonio'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "O ID do patrimônio é obrigatório."]);
    exit();
}

$id_patrimonio = $_GET['id_patrimonio'];

try {
    // Tabela corrigida para minúsculas: patrimonio e ambientes
    $sql = "
        SELECT 
            p.id_patrimonio,
            p.denominacao,
            p.patrimonio_img,
            p.patrimonio_img2,
            p.num_patrimonio,
            p.status,
            a.categoria,
            a.ambiente_nome,
            a.localizacao
        FROM 
            patrimonios p  
        INNER JOIN 
            ambientes a ON p.ambientes_id_ambientes = a.id_ambientes
        WHERE 
            p.num_patrimonio = :num_patrimonio AND p.patrimonio_del = 'ativo'
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':num_patrimonio', $id_patrimonio, PDO::PARAM_STR);
    $stmt->execute();
    
    $patrimonio = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($patrimonio) {
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => "Patrimônio {$id_patrimonio} carregado com sucesso.",
            "data" => $patrimonio
        ]);
    } else {
        http_response_code(200); // Mantenho 200, mas com status de erro para o frontend
        echo json_encode([
            "status" => "error",
            "message" => "Patrimônio ativo com ID {$id_patrimonio} não encontrado.",
            "data" => null
        ]);
    }

} catch (PDOException $e) {
    // Captura erros de banco de dados (query, etc.)
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Erro de PDO na execução: " . $e->getMessage()]);
} catch (Exception $e) {
    // Captura erros gerais de código
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Erro fatal de execução: " . $e->getMessage()]);
}

if (isset($stmt)) $stmt = null;