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
    <title>Lista de Patrimônios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/mostrar_lista.css">
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
                            <li><a href="cadastrar_ambiente.php">Cadastrar Ambientes</a></li>
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
            <h1>Nome da sala...</h1>
        </div>

        <!-- Barra de pesquisa -->
        <!-- <input type="text" id="barra" class="form-control mb-3" placeholder="Pesquisar patrimônio..."> -->

        <!-- Tabela simulando CSV -->
         <div class="tabela">
            <table class="table table-bordered table-striped tabela-customizada">
            <thead>
                <tr>
                    <th>Número Patrimônio</th>
                    <th>Denominação</th>
                </tr>
            </thead>
            <tbody id="lista-patrimonios"></tbody>
        </table>
         </div>

         <div class="buton">
            <button type="submit" onclick="Scanner()"><i class="bi bi-check-square"></i>  Verificar Patrimônios</button>
         </div>
        
    </main>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const idAmbiente = urlParams.get('id');

        if (!idAmbiente) {
            alert("❌ ID do ambiente não encontrado!");
        }

        let patrimoniosCarregados = [];

        // Função para carregar o nome do ambiente usando o endpoint Get_ambientes.php
        async function carregarNomeAmbiente() {
            try {
                const response = await fetch(`../../back-end/Get/Get_ambientes.php`);
                const result = await response.json();

                if(result.status === "success") {
                    const ambiente = result.data.find(a => a.id_ambientes == idAmbiente);
                    if(ambiente) {
                        document.querySelector(".escrita h1").textContent = ambiente.ambiente_nome;
                    } else {
                        document.querySelector(".escrita h1").textContent = "Ambiente não encontrado";
                    }
                } else {
                    document.querySelector(".escrita h1").textContent = "Erro ao carregar ambiente";
                }
            } catch (error) {
                console.error("Erro ao carregar nome do ambiente:", error);
                document.querySelector(".escrita h1").textContent = "Erro ao carregar ambiente";
            }
        }

        // Função para carregar patrimonios filtrados pelo ambiente
        async function carregarPatrimonios() {
            try {
                const response = await fetch(`../../back-end/Get/Get_patrimonio.php?id_ambiente=${idAmbiente}`);
                const result = await response.json();

                if(result.status === 'success') {
                    patrimoniosCarregados = result.data;
                    renderizarPatrimonios(patrimoniosCarregados);
                } else {
                    alert("❌ Erro: " + result.message);
                }
            } catch (error) {
                alert("⚠️ Erro ao carregar patrimonios: " + error.message);
                console.error(error);
            }
        }

        function renderizarPatrimonios(patrimonios) {
            const lista = document.getElementById("lista-patrimonios");


    // Adicione esta linha para depuração
    console.log("Elemento encontrado:", lista); 

    // Se o elemento for null, o erro acontecerá na linha abaixo
    if (!lista) {
        console.error("ERRO: O elemento 'lista-patrimonios' não foi encontrado no DOM!");
        return; // Para a execução para evitar o erro
    }



            lista.innerHTML = "";

            if (patrimonios.length === 0) {
                lista.innerHTML = `<tr><td colspan="3" class="text-center">Nenhum patrimônio encontrado neste ambiente.</td></tr>`;
                return;
            }

            patrimonios.forEach(p => {
                lista.innerHTML += `
                    <tr>
                        <td>${p.num_patrimonio}</td>
                        <td>${p.denominacao}</td>
                        <td>${p.localizacao || ''}</td>
                    </tr>
                `;
            });
        }


        function Voltar() {
            window.location.href = 'ambientes.php';
        }

        document.addEventListener("DOMContentLoaded", () => {
            carregarNomeAmbiente();   // Atualiza h1
            carregarPatrimonios();    // Carrega tabela
        });

        function Scanner(){
            window.location.href = 'scanner.php'
        }

        function Perfil(){
            window.location.href = 'perfil_admin.php'
        }
    </script>
</body>
</html>
