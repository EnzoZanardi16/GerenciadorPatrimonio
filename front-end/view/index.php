<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css?V1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <title>Login</title>
</head>

<body>
    <header></header>

    <main>
        <div class="logosenai">
            <img src="../assets/logo_senai.png" alt="logosenai" p>
            <h1>Formando quem forma a indústria.</h1>
        </div>



        <div class="formulariologin">

            <form id="formLogin">
                <div class="input-container">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" id="email" placeholder="Email">
                </div>

                <div class="input-container">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="senha" id="senha" placeholder="Senha">
                </div>

                <div class="esqueci-senha">
                    <a href="">Esqueci minha senha</a>
                </div>

                <div class="button">
                    <button type="submit">ENTRAR</button>
                </div>
            </form>
        </div>
    </main>



    <script>
        document.getElementById("formLogin").addEventListener("submit", async function (e) {
            e.preventDefault();

            const email = document.getElementById("email").value;
            const senha = document.getElementById("senha").value;

            try {
                // OBSERVAÇÃO: Seu script HTML está chamando LoginSSO.php. 
                // Certifique-se de que este script no back-end está tratando a senha corretamente.
                const response = await fetch("../../back-end/Post/LoginSSO.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ email, senha })
                });

                const result = await response.json();
                console.log(result); // <--- O nível está aqui! Exemplo: result.user.nivel

                if (result.status === "success") {
                    // Mostra alerta de login realizado
                    alert("✅ Login realizado com sucesso!");

                    // Salva token e usuário
                    localStorage.setItem("token", result.token);
                    localStorage.setItem("user", JSON.stringify(result.user));

                    // 🌟 CORREÇÃO AQUI: Garante que o nível é comparado em minúsculas e trata o 'administrador'
                    const nivel = result.user.nivel ? result.user.nivel.toLowerCase() : '';

                    // Redirecionamento conforme nível
                    if (nivel === "colaborador") {
                        window.location.href = "ambientesC.php";
                    } else if (nivel === "gestor" || nivel === "administrador") {
                        // Gestor e Administrador vão para a mesma página administrativa
                        window.location.href = "home_admin.php";
                    } else {
                        // Se o nível vier nulo, vazio ou diferente dos esperados.
                        alert(`❌ Tipo de usuário desconhecido: ${nivel}`);
                    }

                } else {
                    alert("❌ " + result.message);
                }
            } catch (error) {
                alert("⚠️ Erro na requisição: " + error.message);
            }
        });

    </script>

</body>

</html>