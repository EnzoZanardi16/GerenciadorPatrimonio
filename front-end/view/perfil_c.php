<?php
// PHP ORIGINAL DE VERIFICAÇÃO DE SESSÃO E PERMISSÃO
// Garante que o usuário esteja logado e tenha o nível 'colaborador' para acessar esta página.
session_start();

$required_levels = ['colaborador']; // Nível de acesso exigido para esta tela

$redirect_page = 'index.php'; // Página de destino se a verificação falhar

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

// Se chegou até aqui, o usuário está logado e tem permissão.
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/perfil_admin.css?V1.0">
    <title>Perfil</title>
    <style>
        .escrita {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .sair {
            width: 100%;
            height: 350px;
            margin-top: 0;
            display: flex;
            justify-content: start;
            align-items: end;
            cursor: pointer;
        }

        .sair i {
            font-size: 30px;
            margin: 0;
        }

        .sair p {
            margin: 8px;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
        }
    </style>
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
                    <div class="perfilm" onclick="Perfil()">
                        <img src="../assets/man.png" alt="foto de perfil">
                        <div class="editar">
                            <p><?php echo htmlspecialchars($_SESSION['user_nivel'] ?? 'Nível'); ?></p>
                            <i class="bi bi-pencil" style="color: #fff; margin-left: 5px;"></i>
                        </div>
                    </div>
                    <div class="itensmenu">

                        <div class="cardmenu">
                            <i class="bi bi-house-door"></i>
                            <li><a href="ambientesC.php">Ambientes</a></li>
                        </div>
                    </div>
                    <div class="sair" onclick="Sair()">
                        <i class="bi bi-box-arrow-right" style="margin-left: 5px;"></i>
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

    <main class="main-colaborador">

        <div class="conteiner-perfil">
            <div class="perfil">
                <img src="../assets/man.png" alt="Foto do usuário">
                <p id="perfil-nome">Carregando...</p>
            </div>
        </div>

        <div class="dados">
            <div class="info">
                <p>Email</p>
                <p id="perfil-email">Carregando...</p>
            </div>
            <div class="info">
                <p>Senha</p>
                <p>**********</p>
            </div>
            <div class="info">
                <p>Cargo</p>
                <p id="perfil-cargo">Carregando...</p>
            </div>
        </div>

        <div class="editar-logout">
            <div class="card" onclick="Sair()">
                <i class="bi bi-box-arrow-in-right" style="color: #940808;"></i>
                <p>Sair</p>
            </div>
            <div class="card" onclick="Editar()">
                <i class="bi bi-person-fill-gear" style="color: #940808;"></i>
                <p>Editar perfil</p>
            </div>
        </div>
    </main>

    <footer></footer>
    <script>
        function Editar() {
            window.location.href = 'editar_colaborador.php';
        }

        // FUNÇÃO SAIR CORRIGIDA
        async function Sair() {
            // 1. Pega o token de autenticação
            const token = localStorage.getItem("token");

            if (!token) {
                // Se não tiver token, faz a limpeza local de qualquer forma e sai
                localStorage.removeItem("user");
                localStorage.removeItem("token");
                alert("Sessão já finalizada no navegador. Redirecionando.");
                window.location.href = 'index.php';
                return;
            }

            try {
                // 2. CORREÇÃO CRÍTICA DO CAMINHO: Chama o endpoint de logout
                // O caminho correto para o seu arquivo 'logout.php' é:
                const response = await fetch("../../back-end/Post/logout.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": `Bearer ${token}` 
                    },
                    body: JSON.stringify({ token: token })
                });
                
                // Se a resposta for 404, o caminho está errado
                if (response.status === 404) {
                    throw new Error("Erro 404: Arquivo de Logout não encontrado. Verifique o caminho '../../back-end/Post/logout.php'.");
                }

                // Tenta ler o JSON (o backend deve retornar um JSON, mesmo que seja só um status)
                const result = await response.json();

                // 3. Destruição no Frontend
                // Se a resposta for OK (200), ou 401 (token inválido/expirado, mas já saiu), ou status success no JSON.
                if (response.ok || response.status === 401 || result.status === "success") {
                    // Limpa o localStorage (onde estava o token e dados do usuário)
                    localStorage.removeItem("token");
                    localStorage.removeItem("user");

                    alert("Você saiu da sua conta. Sessão finalizada no servidor.");
                    window.location.href = 'index.php';

                } else {
                    // Erro do backend
                    alert("❌ Erro ao finalizar sessão no servidor: " + (result.mensagem || 'Erro desconhecido.'));
                    // Mesmo com erro do backend, forçamos a limpeza local para evitar o reuso do token
                    localStorage.removeItem("token");
                    localStorage.removeItem("user");
                    window.location.href = 'index.php';
                }

            } catch (error) {
                console.error("Erro na requisição de logout:", error);
                alert("⚠️ Erro de conexão ao tentar fazer logout: " + error.message + ". Redirecionando.");

                // Em caso de falha de rede/erro, limpamos localmente e redirecionamos
                localStorage.removeItem("token");
                localStorage.removeItem("user");
                window.location.href = 'index.php';
            }
        }

        // MENU
        const hamburguer = document.querySelector(".hamburguer");
        const nav = document.querySelector("nav");

        hamburguer.addEventListener("click", () => {
            hamburguer.classList.toggle("aberto");
            nav.classList.toggle("ativo");
        });

        // DOMContentLoaded MANTIDO para buscar os dados de perfil, já que você usa 'Carregando...'
        document.addEventListener("DOMContentLoaded", async () => {
            const token = localStorage.getItem("token");
            // Nota: O PHP já protegeu a página, este é um segundo nível de verificação e carregamento
            const usuario = JSON.parse(localStorage.getItem("user"));

            if (!token || !usuario || !usuario.email) {
                // Se o token sumiu no LS, mas a sessão PHP ainda está ativa, forçamos o logout local.
                window.location.href = "index.php";
                return;
            }

            try {
                // busca no back-end (Aqui você deve usar o email que está no localStorage)
                const response = await fetch(`../../back-end/Get/Get_usuario.php?email=${usuario.email}`);

                if (!response.ok) {
                    throw new Error("Falha ao carregar dados do usuário.");
                }

                const result = await response.json();

                if (result.status === "success") {
                    const dados = result.data;
                    document.getElementById("perfil-nome").innerText = dados.usuario_nome;
                    document.getElementById("perfil-email").innerText = dados.usuario_email;
                    document.getElementById("perfil-cargo").innerText = dados.usuario_nivel;

                    // Atualiza o nome no menu superior
                    const perfilMenuP = document.querySelector(".perfilm .editar p");
                    if (perfilMenuP) {
                        perfilMenuP.innerText = dados.usuario_nivel;
                    }

                } else {
                    alert("Erro ao carregar dados do perfil: " + result.message);
                    // Força logout se der erro na busca do perfil
                    localStorage.removeItem("token");
                    localStorage.removeItem("user");
                    window.location.href = "index.php";
                }
            } catch (error) {
                console.error("Erro ao carregar perfil:", error);
            }
        });

    </script>
</body>

</html>