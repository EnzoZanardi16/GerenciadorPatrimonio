<?php
// === CONFIGURAÇÕES DE CORS E ERROS ===
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
ini_set('display_errors', 1);
error_reporting(E_ALL);
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}
header('Content-Type: application/json');

require_once 'config.php';

// --- CONFIGURAÇÃO INICIAL ---
$pastaFotos = './fotos/';

// === VERIFICAÇÃO DOS DADOS POST OBRIGATÓRIOS ===
if (!isset($_FILES['imagem'], $_POST['num_patrimonio'], $_POST['slot'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Dados incompletos. É necessário enviar imagem, num_patrimonio e slot.']);
    exit;
}

// --- EXTRAÇÃO E SANITIZAÇÃO DOS DADOS ---
$num_patrimonio = preg_replace('/[^0-9]/', '', $_POST['num_patrimonio']);
$slot           = preg_replace('/[^1-2]/', '', $_POST['slot']);
$arquivo        = $_FILES['imagem'];
$categoria      = isset($_POST['categoria']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['categoria']) : 'geral';
$nomeAmbiente   = isset($_POST['nomeAmbiente']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['nomeAmbiente']) : 'ambiente';

if (empty($num_patrimonio) || empty($slot)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Número do patrimônio ou slot da imagem inválido.']);
    exit;
}

// === TRATAMENTO E VALIDAÇÃO DA IMAGEM ===
if ($arquivo['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Erro no upload do arquivo. Código: ' . $arquivo['error']]);
    exit;
}
$ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Formato de arquivo não permitido.']);
    exit;
}

// --- NOME E CAMINHO DO ARQUIVO NOVO ---
$datetime = date('Ymd_His');
$hash = substr(md5(uniqid(rand(), true)), 0, 6);
$nomeArquivo = "{$categoria}_{$nomeAmbiente}_{$datetime}_{$hash}.{$ext}";
$caminhoArquivoNovo = $pastaFotos . $nomeArquivo;
// A variável $urlImagemNova não é mais necessária para o banco, usamos direto $nomeArquivo

try {
    $pdo = conn();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $campoAlvo = ($slot == '1') ? 'patrimonio_img' : 'patrimonio_img2';

    // 1. BUSCA O CAMINHO DA IMAGEM ANTIGA
    $sqlBuscaAntiga = "SELECT $campoAlvo FROM patrimonios WHERE num_patrimonio = :num";
    $stmtBusca = $pdo->prepare($sqlBuscaAntiga);
    $stmtBusca->bindParam(':num', $num_patrimonio);
    $stmtBusca->execute();
    $resultado = $stmtBusca->fetch(PDO::FETCH_ASSOC);

    if (!$resultado) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Patrimônio não encontrado.']);
        exit;
    }
    
    // Guarda o nome do arquivo antigo (sem o prefixo 'fotos/')
    // Assumimos que o banco já pode ter o prefixo, então removemos para segurança
    $nomeArquivoAntigo = str_replace('fotos/', '', $resultado[$campoAlvo]);

    // 2. SALVA A NOVA IMAGEM NO SERVIDOR
    if (move_uploaded_file($arquivo['tmp_name'], $caminhoArquivoNovo)) {

        // 3. ATUALIZA O BANCO DE DADOS COM O NOME DO NOVO ARQUIVO (SEM PREFIXO)
        $sqlUpdate = "UPDATE patrimonios SET $campoAlvo = :nomeArquivoNovo WHERE num_patrimonio = :num";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        
        // ▼▼▼ MUDANÇA PRINCIPAL AQUI ▼▼▼
        // Usamos a variável $nomeArquivo, que não tem o prefixo "fotos/"
        $stmtUpdate->bindParam(':nomeArquivoNovo', $nomeArquivo);
        $stmtUpdate->bindParam(':num', $num_patrimonio);
        $stmtUpdate->execute();

        // 4. APAGA O ARQUIVO ANTIGO DO SERVIDOR
        // Construímos o caminho completo para o arquivo antigo para poder deletá-lo
        if ($nomeArquivoAntigo && file_exists($pastaFotos . $nomeArquivoAntigo)) {
            unlink($pastaFotos . $nomeArquivoAntigo);
        }

        http_response_code(200);
        echo json_encode([
            'status'  => 'success',
            'message' => "Imagem do slot $slot atualizada com sucesso!",
            'arquivo' => $nomeArquivo,
            'campo'   => $campoAlvo
        ]);

    } else {
        throw new Exception('Erro ao salvar o novo arquivo de imagem no servidor.');
    }

} catch (PDOException $e) {
    if (file_exists($caminhoArquivoNovo)) {
        unlink($caminhoArquivoNovo);
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erro no servidor: ' . $e->getMessage()]);
}
?>