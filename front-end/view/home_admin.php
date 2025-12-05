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
    <link rel="stylesheet" href="../css/home_admin.css">
    <title>Home</title>
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
        <div class="mensagem">
            <img src="../assets/man.png" alt="Foto de perfil">
            <p>Bem-vindo, Administrador</p>
        </div>

        <div class="central">
            <div class="conteiner" onclick="CadastrarUsuario()">
                <i class="bi bi-person-circle" style="color: #940808; font-size: 50px;"></i>
                <p>Cadastrar Usuário</p>
            </div>


            <!-- Container vira um label clicável -->
            <label for="csv_file" class="conteiner" style="cursor: pointer;">
                <i class="bi bi-file-earmark-arrow-up" style="color: #940808; font-size: 50px;"></i>
                <p>Carregar Lista</p>
            </label>

            <!-- Input escondido que abre o seletor -->
            <input type="file" id="csv_file" accept=".csv" required style="display: none;">

            <!-- Modal -->
            <div id="modalUpload" class="modal" style="display: none;">
                <div class="modal-content">
                    <h2 style="margin-bottom: 10px;">Arquivo selecionado: <p style="color: #6c757d;" id="fileName"></p>
                    </h2>


                    <form action="/../enzo-zanardi/patrimonio/back-end/Post/importar_csv.php" method="post"
                        enctype="multipart/form-data" id="uploadForm">
                        <input type="file" name="csv_file" id="csv_file_modal" accept=".csv" required
                            style="display: none;">

                        <div class="modal-buttons">
                            <button type="submit" class="btn-importar">Carregar Lista</button>

                            <button type="button" class="btn-cancelar" onclick="fecharModal()">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>



            <div class="conteiner" onclick="CadastrarAmbientes()">
                <i class="bi bi-house-add-fill" style="color: #940808; font-size: 50px;"></i>
                <p>Cadastrar Ambientes</p>
            </div>
            <div class="conteiner" onclick="Ambientes()">
                <i class="bi bi-house-door" style="color: #940808; font-size: 50px;"></i>
                <p>Ambientes</p>
            </div>

        </div>
    </main>


    <footer></footer>
    <script>
        function Perfil() {
            window.location.href = 'perfil_admin.php'
        }
        function Ambientes() {
            window.location.href = 'ambientes.php'
        }
        function CadastrarUsuario() {
            window.location.href = 'cadastro.php'
        }
        // function CadastrarItens(){
        //     window.location.href = 'cadastro.php'
        // }
        function CadastrarUsuario() {
            window.location.href = 'cadastro.php'
        }
        function CadastrarAmbientes() {
            window.location.href = 'cadastrar_ambiente.php'
        }

        const inputFile = document.getElementById("csv_file");
        const modal = document.getElementById("modalUpload");
        const fileName = document.getElementById("fileName");
        const fileInputModal = document.getElementById("csv_file_modal");

        inputFile.addEventListener("change", function () {
            if (this.files.length > 0) {
                // mostra o nome do arquivo
                fileName.textContent = this.files[0].name;

                // copia para o input do form
                fileInputModal.files = this.files;

                // abre modal
                modal.style.display = "flex";
            }
        });

        function fecharModal() {
            modal.style.display = "none";
            inputFile.value = ""; // limpa seleção se cancelar
        }

        // MENU
        const hamburguer = document.querySelector(".hamburguer");
        const nav = document.querySelector("nav");

        hamburguer.addEventListener("click", () => {
            // animação do hambúrguer (vira X)
            hamburguer.classList.toggle("aberto");

            // abre/fecha o menu
            nav.classList.toggle("ativo");
        });

        document.getElementById('csv_file_modal').addEventListener('change', function () {
            // Pega o nome do arquivo selecionado
            const fileName = this.files.length > 0 ? this.files[0].name : '';
            const fileNameElement = document.getElementById('fileName');

            if (fileName) {
                fileNameElement.textContent = `Você selecionou: ${fileName}`;
            } else {
                fileNameElement.textContent = '';
            }
        });

        function fecharModal() {
            const modal = document.getElementById('modalUpload');
            modal.style.display = 'none';
        }

        async function Sair() {
            try {
                // pega o token salvo na sessão/localStorage
                const token = sessionStorage.getItem("token") || localStorage.getItem("token");

                const response = await fetch("../../back-end/Post/logout.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ token: token })
                });

                const result = await response.json();

                if (result.status === "success") {
                    // limpa tokens armazenados
                    sessionStorage.removeItem("token");
                    localStorage.removeItem("token");

                    // alerta de confirmação
                    alert("Você saiu da conta com sucesso!");

                    // redireciona para a tela de login
                    window.location.href = "index.php";
                } else {
                    alert("Erro ao sair: " + result.mensagem);
                }
            } catch (error) {
                alert("Falha na conexão com o servidor de logout.");
                console.error(error);
            }
        }

    </script>
</body>

</html>