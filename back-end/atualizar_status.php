<?php
// === CONFIGURAÇÕES DE CORS E ERROS ===
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
header('Content-Type: application/json');

require_once 'config.php';

try {
    // ✔️ 1. RECEBER O OBJETO COMPLETO
    // O front-end agora envia {id_usuario: ..., id_ambiente: ..., patrimonios: [...] }
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true); // Usamos 'true' para converter em array associativo

    // 🔍 2. VALIDAR OS NOVOS DADOS
    // Verificamos se o ID do usuário, o ID do ambiente e a lista de patrimônios foram recebidos
    if (empty($data['id_usuario']) || empty($data['id_ambiente']) || !isset($data['patrimonios']) || !is_array($data['patrimonios'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Dados incompletos: É necessário enviar id_usuario, id_ambiente e a lista de patrimônios.']);
        exit;
    }

    // ➡️ 3. EXTRAIR OS DADOS PARA VARIÁVEIS
    $id_usuario = $data['id_usuario'];
    $id_ambiente = $data['id_ambiente'];
    $lista_patrimonios = $data['patrimonios'];

    $pdo = conn();
    $pdo->beginTransaction();
    
    // ❗ 4. COMUNICAR COM O GATILHO (TRIGGER)
    // Antes de fazer o UPDATE, criamos variáveis de sessão no banco de dados.
    // O gatilho (trigger) na tabela 'patrimonios' poderá ler estas variáveis.
    // É como deixar um "post-it" para o gatilho com as informações que ele precisa.
    $pdo->exec("SET @current_user_id = " . $pdo->quote($id_usuario));
    $pdo->exec("SET @current_ambiente_id = " . $pdo->quote($id_ambiente));


    // 5. PREPARAR A QUERY DE UPDATE (esta parte continua igual)
    // O nome correto da tabela é 'patrimonios', com 's' no final.
    $sqlUpdate = "UPDATE patrimonios SET status = :status WHERE num_patrimonio = :num_patrimonio";
    $stmt = $pdo->prepare($sqlUpdate);

    // 6. EXECUTAR A QUERY PARA CADA ITEM DA LISTA
    foreach ($lista_patrimonios as $p) {
        if (isset($p['num_patrimonio']) && isset($p['status'])) {
            $stmt->execute([
                ':status' => $p['status'],
                ':num_patrimonio' => $p['num_patrimonio']
            ]);
        }
    }
    
    // Limpamos as variáveis de sessão do banco após o uso
    $pdo->exec("SET @current_user_id = NULL");
    $pdo->exec("SET @current_ambiente_id = NULL");

    $pdo->commit();

    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Status dos patrimônios atualizados com sucesso.']);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
?>