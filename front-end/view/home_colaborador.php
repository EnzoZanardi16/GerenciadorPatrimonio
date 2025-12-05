<?php
session_start();

$required_levels = ['colaborador'];

$redirect_page = 'index.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['login_token'])) {
    $_SESSION['auth_error'] = "Você precisa estar logado para acessar esta página.";
    header("Location: " . $redirect_page);
    exit();
}

$user_nivel_sessao = isset($_SESSION['user_nivel']) ? strtolower(trim($_SESSION['user_nivel'])) : null;

if (empty($user_nivel_sessao) || !in_array($user_nivel_sessao, $required_levels)) {
    $_SESSION['auth_error'] = "Acesso Negado. Seu nível de usuário ('" . strtoupper($user_nivel_sessao) . "') não tem permissão para esta área.";

    header("Location: " . $redirect_page);
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <title>Home</title>
</head>
<body>
    <header>
        <div class="header-colaborador">
        <div class="menu">
            <button class="hamburguer">
                <div id="barra1" class="barra"></div>
                <div id="barra2" class="barra"></div>
                <div id="barra3" class="barra"></div>
            </button>
        </div>
        <div class="logo">
            <img src="../assets/logo_senai.png" alt="logo-senai">
        </div>
        <div class="notificacao">
            <i class="bi bi-bell"></i>
        </div>
    </div>
    </header>


    <main>
        <div class="mensagem">
            <img src="../assets/man.png" alt="Foto de perfil">
            <p id="bemVindo">Bem-vindo, </p>
        </div>

        <div class="central">
        <div class="conteiner" onclick="Ambientes()">
            <img src="../assets/iconcasa.png" alt="">
            <h1>Ambientes</h1>
        </div>
        </div>
    </main>


    <footer></footer>
    <script>
        function Ambientes(){
            window.location.href = 'ambientes.php'
        }

        // Atualiza a mensagem com o nome do usuário
        const nomeUsuario = localStorage.getItem("usuario_nome") || "Colaborador";
        document.getElementById("bemVindo").textContent = `Bem-vindo, ${nomeUsuario}`;

        // Função para redirecionar
        function Ambientes() {
            window.location.href = 'ambientes.php';
        }
    </script>
</body>
</html>