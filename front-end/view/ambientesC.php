<?php
session_start();

$required_levels = ['administrador', 'gestor', 'colaborador'];

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
    <link rel="stylesheet" href="../css/ambientes.css">
    <title>Ambientes</title>
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
        }

        .sair i {
            font-size: 30px;
            margin: 5px;
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
                    <div class="perfil" onclick="Perfil()">
                        <img src="../assets/man.png" alt="foto de perfil">
                        <div class="editar">
                            <p>Administrador</p><i class="bi bi-pencil" style="color: #fff; margin-left: 5px;"></i>
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

    <main>
        <div class="escrita">
            <!-- <i class="bi bi-arrow-left" onclick="Voltar()"></i> -->
            <h1>Ambientes</h1>
        </div>


        <form class="d-flex" role="search">
            <input class="form-control me-2" type="search" placeholder="Pesquisar Ambiente..." aria-label="Search"
                id="barra" />
            <button class="btn btn-outline" type="submit" style="background-color: #940808; color:#fff;"><i
                    class="bi bi-search"></i></button>
        </form>

        <div class="ambientes-card" id="lista-ambientes">
        </div>

    </main>

    <script>
        // Definição da função Sair() fora do DOMContentLoaded para que possa ser chamada pelo onclick=""
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
                // 2. Chama o endpoint de logout no backend
                // CORREÇÃO CRÍTICA DO CAMINHO: Usando "../../back-end/Post/logout.php"
                const response = await fetch("../../back-end/Post/logout.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": `Bearer ${token}`
                    },
                    body: JSON.stringify({ token: token })
                });

                // Tenta ler o JSON
                const result = await response.json();

                // 3. Destruição no Frontend
                if (response.ok || response.status === 401 || result.status === "success") {
                    // Limpa o localStorage (onde estava o token e dados do usuário)
                    localStorage.removeItem("token");
                    localStorage.removeItem("user");

                    alert("Você saiu da sua conta. Sessão finalizada no servidor.");
                    window.location.href = 'index.php';

                } else {
                    // Erro do backend
                    alert("❌ Erro ao finalizar sessão no servidor: " + (result.mensagem || 'Erro desconhecido.'));
                    // Mesmo com erro, forçamos a limpeza local para evitar o reuso do token
                    localStorage.removeItem("token");
                    localStorage.removeItem("user");
                    window.location.href = 'index.php';
                }

            } catch (error) {
                console.error("Erro na requisição de logout:", error);
                alert("⚠️ Erro de conexão ao tentar fazer logout. Redirecionando.");

                // Em caso de falha de rede/erro, limpamos localmente e redirecionamos
                localStorage.removeItem("token");
                localStorage.removeItem("user");
                window.location.href = 'index.php';
            }
        }

        function Perfil() {
            window.location.href = "perfil_c.php"
        }

        // Usando DOMContentLoaded para carregar e iniciar toda a lógica da página
        document.addEventListener("DOMContentLoaded", () => {
            // Evita reload do form
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', e => e.preventDefault());
            }

            function Voltar() {
                window.location.href = 'home_admin.php'; // Ou onde for a home deste nível de usuário
            }

            const hamburguer = document.querySelector(".hamburguer");
            const nav = document.querySelector("nav");

            hamburguer.addEventListener("click", () => {
                hamburguer.classList.toggle("aberto");
                nav.classList.toggle("ativo");
            });

            const categoriaIcones = {
                "eletroeletronica": "bi bi-lightning-charge",
                "panificacao": "bi bi-basket",
                "oficina": "bi bi-gear",
                "quimica": "bi bi-flask",
                "metalmecanica": "bi bi-tools",
                "ti": "bi bi-display"
            };

            let ambientesCarregados = [];

            async function carregarAmbientes() {
                try {
                    // Verificação de segurança: se o token sumiu, o PHP deve ter barrado, mas verificamos o LS
                    if (!localStorage.getItem("token")) {
                        window.location.href = 'index.php';
                        return;
                    }

                    const response = await fetch("../../back-end/Get/Get_ambientes.php");
                    const result = await response.json();
                    console.log("Result:", result);

                    const lista = document.getElementById("lista-ambientes");
                    if (!lista) return;

                    lista.innerHTML = "";

                    if (result.status === "success") {
                        ambientesCarregados = result.data;
                        renderizarAmbientes(result.data);
                    } else {
                        alert("❌ Erro ao carregar: " + result.message);
                    }
                } catch (error) {
                    alert("⚠️ Erro de rede ao carregar ambientes: " + error.message);
                }
            }

            function renderizarAmbientes(ambientes) {
                const lista = document.getElementById("lista-ambientes");
                if (!lista) return;

                lista.innerHTML = "";
                ambientes.forEach(amb => {
                    const icone = categoriaIcones[amb.categoria.toLowerCase()] || "bi bi-display";

                    lista.innerHTML += `
                <div class="conteiners" onclick="Scanear()">
                    <i class="${icone}"></i>
                    <div class="texto">
                        <p>${amb.ambiente_nome}</p>
                        <p>${amb.localizacao}</p>
                    </div>
                </div>
            `;
                });
            }

            // Pesquisa em tempo real (única ocorrência)
            const barra = document.getElementById("barra");
            if (barra) {
                barra.addEventListener("input", function () {
                    const termo = this.value.toLowerCase();
                    const filtrados = ambientesCarregados.filter(amb =>
                        amb.ambiente_nome.toLowerCase().includes(termo) ||
                        (String(amb.localizacao || "")).toLowerCase().includes(termo)
                    );
                    renderizarAmbientes(filtrados);
                });
            }

            // Chama a função principal de carregamento ao iniciar
            carregarAmbientes();
        });
    </script>
</body>

</html>