<?php
// === CONFIGURAÇÕES DE CORS ===
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
header('Content-Type: application/json');

// Inclui a função de conexão PDO (conn())
require_once 'config.php'; 

// ▼▼▼ ADICIONE ESTAS DUAS LINHAS ▼▼▼
$pdo = conn(); // Executa a função do config.php para criar a conexão
$pastaFotos = './fotos/'; // Define o caminho para a pasta de fotos


// O MÉTODO HTTP CORRETO PARA RECEBER DADOS DE FORMULÁRIO/UPLOAD É POST
// === VERIFICAÇÃO DOS DADOS POST OBRIGATÓRIOS ===
if (!isset($_FILES['imagem'], $_POST['categoria'], $_POST['nomeAmbiente'], $_POST['num_patrimonio'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Dados incompletos. Verifique se a imagem (via $_FILES) e os campos POST foram enviados.']);
    exit;
}

// Extração e sanitização dos dados
$categoria        = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['categoria']);
$nomeAmbiente     = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['nomeAmbiente']);
$num_patrimonio   = preg_replace('/[^0-9]/', '', $_POST['num_patrimonio']);
$arquivo          = $_FILES['imagem'];

if (empty($num_patrimonio)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Número do patrimônio (num_patrimonio) inválido ou ausente.']);
    exit;
}

// === TRATAMENTO E VALIDAÇÃO DA IMAGEM ===
if ($arquivo['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Erro no upload do arquivo. Código: ' . $arquivo['error']]);
    exit;
}

$ext = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
if (!in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp'])) {
     http_response_code(400);
     echo json_encode(['status' => 'error', 'message' => 'Formato de arquivo não permitido.']);
     exit;
}

// === NOME DO ARQUIVO ===
$datetime = date('Ymd_His');
$hash = substr(md5(uniqid(rand(), true)), 0, 6);
$nomeArquivo = "{$categoria}_{$nomeAmbiente}_{$datetime}_{$hash}.{$ext}";
$caminhoArquivo = $pastaFotos . $nomeArquivo;

if (move_uploaded_file($arquivo['tmp_name'], $caminhoArquivo)) {

    $urlImagem = "fotos/" . $nomeArquivo;


    // === AGORA BUSCA PELO num_patrimonio ===
    $sqlVerifica = "SELECT patrimonio_img, patrimonio_img2 FROM patrimonios WHERE num_patrimonio = :num";
    $stmtVerifica = $pdo->prepare($sqlVerifica);
    $stmtVerifica->bindParam(':num', $num_patrimonio);
    $stmtVerifica->execute();
    $row = $stmtVerifica->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        if (file_exists($caminhoArquivo)) {
            unlink($caminhoArquivo);
        }
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Patrimônio não encontrado pelo número informado.']);
        exit;
    }

    // Define qual campo atualizar
    if (empty($row['patrimonio_img'])) {
        $campo = 'patrimonio_img';
    } elseif (empty($row['patrimonio_img2'])) {
        $campo = 'patrimonio_img2';
    } else {
        $campo = 'patrimonio_img2';
    }

    // === UPDATE USANDO num_patrimonio ===
    $sqlUpdate = "UPDATE patrimonios SET $campo = :urlImagem WHERE num_patrimonio = :num";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->bindParam(':urlImagem', $urlImagem);
    $stmtUpdate->bindParam(':num', $num_patrimonio);

    if ($stmtUpdate->execute()) {
        http_response_code(200);
        echo json_encode([
            'status'    => 'success',
            'arquivo'   => $nomeArquivo,
            'campo'     => $campo,
            'url_salva' => $urlImagem,
            'message'   => 'Imagem salva e vinculada ao número do patrimônio com sucesso!'
        ]);
    } else {
        if (file_exists($caminhoArquivo)) {
            unlink($caminhoArquivo);
        }
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar o banco de dados.']);
    }
}
?>