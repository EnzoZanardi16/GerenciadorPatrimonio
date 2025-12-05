<?php
session_start();
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
// 1. INICIA A SESSÃO: Isso deve ser a primeira coisa a acontecer.


require_once "../config.php";

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['email']) && isset($data['senha'])) {
        $email = trim($data['email']);
        $senha = $data['senha']; // Senha em texto puro, vinda do front

        if (empty($email) || empty($senha)) {
            http_response_code(400); // Bad Request
            echo json_encode(['status' => 'error', 'message' => 'Email e senha são obrigatórios.']);
            exit();
        }

        // 1. Criptografa a senha recebida para comparação no banco (usando SHA-512)
        $senha_criptografada = hash('sha512', $senha);

        $pdo = conn();

        // 2. Consulta: Verifica se existe usuário com ESSE EMAIL E ESSA SENHA
        $smt = $pdo->prepare('SELECT * FROM usuarios WHERE usuario_email = ? AND senha = ? AND usuario_del = "ativo"');
        $smt->execute([$email, $senha_criptografada]);
        $user = $smt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Se chegou aqui, as credenciais estão corretas e o usuário está ativo.

            // Gera token único com SHA512
            $random_string = random_bytes(32);
            $token = hash('sha512', $random_string);

            // LÓGICA DE LIMPEZA: Exclui TODOS os tokens antigos para este usuário.
            $smt_delete = $pdo->prepare("DELETE FROM token WHERE usuarios_id_usuario = ?");
            $smt_delete->execute([$user['id_usuario']]);

            // Salva o novo token na tabela
            $smt = $pdo->prepare("INSERT INTO token (usuarios_id_usuario, token) VALUES (?, ?)");
            $smt->execute([$user['id_usuario'], $token]);

            if ($smt->rowCount() > 0) {

                // === CORREÇÃO: GARANTE QUE O NÍVEL ESTÁ LIMPO E PADRONIZADO ===
                $user_nivel_limpo = strtolower(trim($user['usuario_nivel']));


                // Resposta de sucesso 
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login realizado com sucesso e sessão iniciada.',
                    'token' => $token,
                    'user' => [
                        'id' => $user['id_usuario'],
                        'email' => $user['usuario_email'],
                        'nome' => $user['usuario_nome'],
                        'token' => $token,
                        'nivel' => $user_nivel_limpo // Retorna o valor LIMPO para o front
                    ],
                    'session_data' => [ // Dados de sessão para DEBUG
                        'login_token' => $_SESSION['login_token'],
                        'user_nivel' => $_SESSION['user_nivel'],
                    ]
                ]);
                exit();
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar novo token no banco de dados.']);
                exit();
            }
        } else {
            // Email ou senha estão incorretos, ou o usuário está inativo.
            http_response_code(401); // Unauthorized
            echo json_encode(['status' => 'error', 'message' => 'Credenciais inválidas. Verifique seu e-mail e senha.']);
            exit();
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Campos de email ou senha ausentes.']);
        exit();
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Método inválido. Use POST.']);
    exit();
}