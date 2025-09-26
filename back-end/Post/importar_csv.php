<?php
// Inclui os arquivos necessários
require_once "../config.php";

// Conecta ao banco de dados
$pdo = conn();

// Verifica se um arquivo foi enviado via formulário
if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
    $tmp_name = $_FILES['csv_file']['tmp_name'];

    // Abre o arquivo CSV para leitura
    if (($handle = fopen($tmp_name, 'r')) !== FALSE) {
        // Lê e descarta a primeira linha do cabeçalho
        fgetcsv($handle, 1000, ';'); 

        // Prepara a query de inserção para a tabela 'patrimonios'
        $sql_patrimonios = "INSERT INTO patrimonios (num_patrimonio, denominacao, ambientes_id_ambientes, patrimonio_del, status, patrimonio_img, created_at) 
                             VALUES (:num, :denominacao, :id_ambiente, 'ativo', 'localizado', '', NOW())";
        $stmt_patrimonios = $pdo->prepare($sql_patrimonios);
        
        // Prepara a query de busca na tabela 'ambientes'
        $sql_busca_ambiente = "SELECT id_ambientes FROM ambientes WHERE localizacao = :localizacao LIMIT 1";
        $stmt_busca_ambiente = $pdo->prepare($sql_busca_ambiente);

        // Inicia uma transação para garantir que todas as inserções sejam bem-sucedidas
        $pdo->beginTransaction();

        try {
            // Loop para ler cada linha do CSV
            while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                // Mapeamento das colunas do CSV para variáveis
                $num_patrimonio      = ($data[0] ?? null);
                $denominacao         = ($data[1] ?? null);
                $localizacao_ambiente = ($data[2] ?? null);

                // Apenas um exemplo de validação básica para evitar a inserção de linhas vazias
                if (empty($num_patrimonio) || empty($denominacao) || empty($localizacao_ambiente)) {
                    continue; // Pula para a próxima linha se os dados essenciais estiverem faltando
                }

                // 1. Busca o id_ambiente correspondente à localização
                $stmt_busca_ambiente->execute([':localizacao' => $localizacao_ambiente]);
                $id_ambiente = $stmt_busca_ambiente->fetchColumn();

                // 2. Se o id_ambiente for encontrado, insere o patrimônio
                if ($id_ambiente) {
                    $stmt_patrimonios->execute([
                        ':num' => $num_patrimonio,
                        ':denominacao' => $denominacao,
                        ':id_ambiente' => $id_ambiente
                    ]);
                } else {
                    // Opcional: registrar ou pular a linha se a localização não for encontrada
                    // echo "Aviso: Localização '$localizacao_ambiente' não encontrada na tabela de ambientes. Linha ignorada.<br>";
                    continue;
                }
            }

            // Confirma a transação se todas as inserções foram bem-sucedidas
            $pdo->commit();
            fclose($handle);
            // Redireciona para a página home_admin
            header("Location: /../enzo-zanardi/patrimonio/front-end/view/home_admin.html");
            exit();
        } catch (Exception $e) {
            // Reverte a transação em caso de erro
            $pdo->rollBack();
            echo "❌ Erro na importação: " . $e->getMessage();
        }
    } else {
        echo "❌ Erro ao abrir o arquivo.";
    }
} else {
    echo "❌ Nenhum arquivo enviado.";
}