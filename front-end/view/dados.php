<?php
// PHP de segurança/sessão (pode adaptar o check_auth.php aqui)
session_start();

// BUSCA id_patrimonio da URL
$id_patrimonio = $_GET['id_patrimonio'] ?? null;
if (!$id_patrimonio) {
    // Redireciona se não houver ID do patrimônio na URL
    header("Location: ambientesC.php?error=no_patrimonio_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Patrimônio #<?php echo htmlspecialchars($id_patrimonio); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.0/font/bootstrap-icons.min.css">

    <style>
        /* Estilos customizados */
        body {
            background-color: #f8f9fa;
        }

        .patrimonio-card {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .header-bg {
            background-color: #940808;
            /* Sua cor primária */
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 0.375rem;
        }

        .status-badge {
            font-size: 1rem;
            padding: .5em 1em;
            font-weight: bold;
        }

        .img-preview {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .icon-lg {
            font-size: 2rem;
            margin-right: 15px;
            color: #940808;
        }
    </style>
</head>

<body>
    <div class="container mt-5">

        <header class="header-bg text-center rounded">
            <h1 id="item-denominacao" class="display-5">Carregando detalhes do Patrimônio...</h1>
            <p class="lead mb-0">ID: #<?php echo htmlspecialchars($id_patrimonio); ?></p>
        </header>

        <div id="patrimonio-details">
            <div class="text-center p-5">
                <div class="spinner-border text-danger" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Buscando dados no servidor...</p>
            </div>
        </div>

        <footer class="text-center mt-4">
            <button onclick="window.history.back()" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </button>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const idPatrimonio = <?php echo json_encode($id_patrimonio); ?>;

            // 🚨 NOVO: Mapeamento de Ícones por Categoria
            const categoriaIcones = {
                "eletroeletronica": "bi bi-lightning-charge",
                "panificacao": "bi bi-basket",
                "oficina": "bi bi-gear",
                "quimica": "bi bi-flask",
                "metalmecanica": "bi bi-tools",
                "t.i": "bi bi-display", // Adaptei para "t.i" conforme sua tabela (image_6e3977.png)
                "default": "bi bi-box-seam" // Ícone padrão
            };

            async function carregarDadosPatrimonio() {
                const detailsDiv = document.getElementById("patrimonio-details");
                detailsDiv.innerHTML = `<div class="text-center p-5"><div class="spinner-border text-danger" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Buscando dados no servidor...</p></div>`;

                try {
                    const endpoint = `../../back-end/Get/Get_patrimonio_unico.php?id_patrimonio=${idPatrimonio}`;
                    const response = await fetch(endpoint);
                    const responseText = await response.text();

                    if (responseText.trim().charAt(0) !== '{') {
                        throw new Error(`Resposta inválida. Verifique o console do navegador (aba Rede/Network) para o erro do servidor.`);
                    }

                    const result = JSON.parse(responseText);
                    const item = result.data;

                    if (result.status === "success" && item) {
                        document.getElementById('item-denominacao').innerText = item.denominacao;

                        // Renderiza o único card de detalhes
                        detailsDiv.innerHTML = criarCardPatrimonio(item);

                    } else if (result.status === "error") {
                        detailsDiv.innerHTML = `<div class="alert alert-warning text-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i> ${result.message}
                        </div>`;
                    } else {
                        detailsDiv.innerHTML = `<div class="alert alert-danger text-center" role="alert">
                            <i class="bi bi-x-octagon-fill"></i> Erro desconhecido ao processar dados.
                        </div>`;
                    }

                } catch (error) {
                    detailsDiv.innerHTML = `<div class="alert alert-danger text-center" role="alert">
                        <i class="bi bi-bug-fill"></i> <strong>Erro de Rede/JSON:</strong> ${error.message}
                    </div>`;
                    console.error("Erro ao carregar dados:", error);
                }
            }

            function criarCardPatrimonio(item) {
                // Lógica de Status
                const status = item.status || 'desconhecido';
                let statusClass = '';
                if (status === 'ativo' || status === 'disponivel') {
                    statusClass = 'bg-success text-white';
                } else if (status === 'pendente' || status === 'em manutencao') {
                    statusClass = 'bg-warning text-dark';
                } else if (status === 'inativo' || status === 'excluido') {
                    statusClass = 'bg-danger text-white';
                } else {
                    statusClass = 'bg-secondary text-white';
                }

                // Lógica de Categoria e Ícone
                const categoriaKey = item.categoria ? item.categoria.toLowerCase() : 'default';
                const iconeClasse = categoriaIcones[categoriaKey] || categoriaIcones['default'];

                // Lógica das Imagens
                const pastaFotos = '../../back-end/Post/uploads/fotos/';
                const img1 = item.patrimonio_img ? `<img src="${pastaFotos}${item.patrimonio_img}" class="img-fluid img-preview" alt="Imagem 1 de ${item.denominacao}">` : '';
                const img2 = item.patrimonio_img2 ? `<img src="${pastaFotos}${item.patrimonio_img2}" class="img-fluid img-preview" alt="Imagem 2 de ${item.denominacao}">` : '';

                return `
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card patrimonio-card h-100 p-3">
                                <h4 class="card-title text-center text-muted border-bottom pb-2">Imagens</h4>
                                ${img1}
                                ${img2}
                                ${(!img1 && !img2) ? '<p class="text-center text-muted mt-3">Nenhuma imagem disponível.</p>' : ''}
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card patrimonio-card h-100">
                                <div class="card-body">
                                    <h2 class="card-title d-flex align-items-center">
                                        <i class="${iconeClasse} icon-lg"></i>
                                        Detalhes do Item
                                    </h2>
                                    
                                    <p class="card-text mb-2"><strong>Denominação:</strong> ${item.denominacao}</p>
                                    <p class="card-text mb-2"><strong>Nº Patrimônio:</strong> ${item.num_patrimonio}</p>
                                    
                                    <p class="card-text mb-3">
                                        <strong>Status:</strong> 
                                        <span class="badge ${statusClass} status-badge">
                                            ${status.toUpperCase()}
                                        </span>
                                    </p>

                                    <hr>

                                    <h5 class="mt-4 text-muted">Localização e Categoria</h5>
                                    <p class="card-text mb-2"><i class="bi bi-building me-2"></i> <strong>Ambiente:</strong> ${item.ambiente_nome}</p>
                                    <p class="card-text mb-2"><i class="bi bi-geo-alt me-2"></i> <strong>Localização Cód.:</strong> ${item.localizacao}</p>
                                    <p class="card-text mb-2"><i class="bi bi-tag me-2"></i> <strong>Categoria:</strong> ${item.categoria}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            carregarDadosPatrimonio();
        });
    </script>
</body>

</html>