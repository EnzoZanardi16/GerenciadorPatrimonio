<?php
session_start();

$required_levels = ['administrador', 'gestor'];

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/cadastrar_ambientes.css">
    <title>Cadastrar Ambientes</title>
</head>
<body>
     <header>
        <div class="menu">
            <button class="hamburguer">
                <div id="barra1" class="barra"></div>
                <div id="barra2" class="barra"></div>
                <div id="barra3" class="barra"></div>
            </button>
            <nav>
                <ul>
                    <div class="perfil" onclick="Perfil()">
                        <img src="../assets/man.png" alt="foto de perfil">
                        <div class="editar">
                            <p>Administrador</p><i class="bi bi-pencil" style="color: #fff; margin-left: 5px;"></i>
                        </div>
                    </div>
                    <div class="itensmenu">

                        <div class="cardmenu">
                            <i class="bi bi-house"></i>
                            <li><a href="home_admin.php">Home</a></li>
                        </div>
                        <div class="cardmenu">
                            <i class="bi bi-person-add"></i>
                            <li><a href="cadastro.php">Cadastrar Usuário</a></li>
                        </div>
                        <div class="cardmenu">
                            <i class="bi bi-house-add"></i>
                            <li><a href="cadastrar_ambientes.php">Cadastrar Ambientes</a></li>
                        </div>
                        <div class="cardmenu">
                            <i class="bi bi-house-door"></i>
                            <li><a href="ambientes.php">Ambientes</a></li>
                        </div>
                    </div>
                    <div class="sair" onclick="Sair()">
                        <i class="bi bi-box-arrow-right"></i>
                        <p>Sair</p>
                    </div>

                </ul>
            </nav>
        </div>
        <div class="logo">
            <img src="../assets/logo_senai.png" alt="logo-senai">
        </div>
        <div class="notificacao">
            <i class="bi bi-bell"></i>
        </div>
    </header>

    <main>
        <div class="escrita">
            <i class="bi bi-arrow-left" onclick="Voltar()"></i>
            <h1>Cadastrar Ambientes</h1>
        </div>

        <div class="conteiner">
            <form id="formAmbiente" class="register-form">
                <div class="form-group">
                    <label for="nome">Nome da sala:</label>
                    <input type="text" id="nome" name="ambien_nome" class="form-control" placeholder="Ex: Laboratório de Usinagem">
                </div>

                <div class="form-group">
                    <label for="categoria">Categoria:</label>
                    <select name="categoria" id="categoria" class="form-control">
                        <option value="" disabled selected>Selecione uma categoria</option>
                        <option value="eletroeletronica">Eletroeletrônica</option>
                        <option value="oficina">Oficina</option>
                        <option value="quimica">Química</option>
                        <option value="t.i">T.I</option>
                        <option value="panificacao">Panificação</option>
                        <option value="metalmecanica">Metalmecânica</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="localizacao">Código:</label>
                    <input type="text" id="localizacao" name="localizacao" class="form-control" placeholder="Ex: 4911359">
                </div>

                <button type="submit" class="btn">Cadastrar</button>

            </form>
        </div>
    </main>

    <script>
        document.getElementById("formAmbiente").addEventListener("submit", async function (e) {
            e.preventDefault();

            // 1. Pega o token de autenticação
            const token = localStorage.getItem("token");

            if (!token) {
                alert("❌ Usuário não autenticado. Faça login novamente.");
                window.location.href = "login.php"; // Redireciona para login
                return;
            }

            const data = {
                ambiente_nome: document.getElementById("nome").value,
                categoria: document.getElementById("categoria").value,
                localizacao: document.getElementById("localizacao").value,
                ambiente_del: "ativo"
            };

            try {
                const response = await fetch("../../back-end/Post/Post_ambientes.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        // ESSENCIAL: Envia o token para autenticar a permissão
                        "Authorization": `Bearer ${token}`
                    },
                    body: JSON.stringify(data)
                });

                // ⚠️ Se o status for 403, o PHP negou a permissão.
                if (response.status === 403) {
                    const errorResult = await response.json();
                    alert(`❌ Acesso Negado: ${errorResult.message}`);
                    return;
                }

                const result = await response.json();

                if (result.status === "success") {
                    alert("✅ " + result.message);
                    document.getElementById("formAmbiente").reset(); // limpa o formulário
                } else {
                    alert("❌ " + result.message);
                }
            } catch (error) {
                alert("⚠️ Erro na requisição: " + error.message);
            }
        });
        function Voltar() {
            window.location.href = 'home_admin.php'
        }
    </script>
</body>
</html>