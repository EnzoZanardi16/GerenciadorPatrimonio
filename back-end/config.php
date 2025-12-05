<?php
// back-end/config.php (ou back-end/Update/config.php)
// GARANTA QUE NÃO HÁ ESPAÇOS OU LINHAS ANTES DESTA TAG

function conn() {
    $host = 'localhost';
    $dbname = 'enzo-zanardi';
    $user = 'enzo-zanardi';
    $pass = 'enzo-zanardi'; // Verifique se esta senha está correta!
    $charset = 'utf8mb4';
    $porta = '8024';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset;port=$porta";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        // Se a conexão falhar, retorna JSON de erro e encerra
        http_response_code(500);
        die(json_encode([
            'status' => 'error',
            'message' => 'Erro fatal na conexão com o banco de dados (PDO): ' . $e->getMessage()
        ]));
    }
}

$conn = conn();
