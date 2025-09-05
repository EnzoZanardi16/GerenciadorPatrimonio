-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 05/09/2025 às 20:16
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `patrimonio`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `ambientes`
--

CREATE TABLE `ambientes` (
  `id_ambientes` int(11) NOT NULL,
  `localizacao` int(11) NOT NULL,
  `ambiente_nome` varchar(100) NOT NULL,
  `ambiente_del` enum('ativo','inativo') NOT NULL,
  `categoria` enum('eletroeletronica','oficina','quimica','t.i','panificacao','metalmacanica') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `ambientes`
--

INSERT INTO `ambientes` (`id_ambientes`, `localizacao`, `ambiente_nome`, `ambiente_del`, `categoria`) VALUES
(1, 12345, 'lab de quimica', 'ativo', 'quimica'),
(2, 23456, 'laboratorio de ti 1', 'ativo', 't.i'),
(3, 11021, 'Sala de Reuniões 01', 'ativo', 'eletroeletronica'),
(4, 23500, 'Laboratório de Química', 'ativo', 'quimica');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ambientes_has_usuarios`
--

CREATE TABLE `ambientes_has_usuarios` (
  `ambientes_id_ambientes` int(11) NOT NULL,
  `usuarios_id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `ambientes_has_usuarios`
--

INSERT INTO `ambientes_has_usuarios` (`ambientes_id_ambientes`, `usuarios_id_usuario`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `arquivo_importacao`
--

CREATE TABLE `arquivo_importacao` (
  `id_arquivo` int(11) NOT NULL,
  `data_importacao` datetime NOT NULL,
  `resultado` enum('sucesso','falha') NOT NULL,
  `arquivo` longtext NOT NULL,
  `arquivo_del` enum('ativo','inativo') NOT NULL,
  `usuarios_id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `arquivo_importacao`
--

INSERT INTO `arquivo_importacao` (`id_arquivo`, `data_importacao`, `resultado`, `arquivo`, `arquivo_del`, `usuarios_id_usuario`) VALUES
(1, '2025-09-05 20:11:42', 'sucesso', ';-;', 'ativo', 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `movimentacao_item`
--

CREATE TABLE `movimentacao_item` (
  `id_movimentacao` int(11) NOT NULL,
  `data_hora` datetime NOT NULL,
  `movimentacao_del` enum('ativo','inativo') NOT NULL,
  `patrimonios_num_patrimonio` int(11) NOT NULL,
  `origem` int(11) NOT NULL,
  `destino` int(11) NOT NULL,
  `usuarios_id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `movimentacao_item`
--

INSERT INTO `movimentacao_item` (`id_movimentacao`, `data_hora`, `movimentacao_del`, `patrimonios_num_patrimonio`, `origem`, `destino`, `usuarios_id_usuario`) VALUES
(1, '2025-09-05 13:44:07', 'ativo', 1001, 1, 2, 2);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `movimento`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `movimento` (
`data_hora` datetime
,`origem` varchar(100)
,`destino` varchar(100)
,`num_patrimonio` int(11)
,`patrimonio_nome` varchar(100)
,`status` enum('pendente','localizado','fora do lugar','faltando')
,`patrimonio_img` longtext
,`denominacao` varchar(100)
,`usuario_nome` varchar(100)
,`usuario_nivel` enum('administrador','gestor','colaborador')
);

-- --------------------------------------------------------

--
-- Estrutura para tabela `patrimonios`
--

CREATE TABLE `patrimonios` (
  `num_patrimonio` int(11) NOT NULL,
  `patrimonio_nome` varchar(100) NOT NULL,
  `patrimonio_del` enum('ativo','inativo') NOT NULL,
  `status` enum('pendente','localizado','fora do lugar','faltando') NOT NULL,
  `patrimonio_img` longtext NOT NULL,
  `denominacao` varchar(100) NOT NULL,
  `ambientes_id_ambientes` int(11) NOT NULL,
  `verificacao_ambiente_id_verificacao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `patrimonios`
--

INSERT INTO `patrimonios` (`num_patrimonio`, `patrimonio_nome`, `patrimonio_del`, `status`, `patrimonio_img`, `denominacao`, `ambientes_id_ambientes`, `verificacao_ambiente_id_verificacao`) VALUES
(1001, 'Computador Dell Optiplex 7090', 'ativo', 'pendente', 'http://meuservidor.com/imgs/computador.jpg', 'Equipamento de TI', 1, 0),
(1002, 'Projetor Epson PowerLite E20', 'ativo', 'pendente', 'http://meuservidor.com/imgs/projetor_epson.jpg', 'Equipamento Audiovisual', 2, 0);

--
-- Acionadores `patrimonios`
--
DELIMITER $$
CREATE TRIGGER `trg_movimentacao_patrimonio` AFTER UPDATE ON `patrimonios` FOR EACH ROW BEGIN
    IF OLD.ambientes_id_ambientes <> NEW.ambientes_id_ambientes OR OLD.status <> NEW.status THEN
        INSERT INTO `movimentacao_item` (
            `data_hora`,
            `patrimonios_num_patrimonio`,
            `origem`,
            `destino`,
            `usuarios_id_usuario`
        ) VALUES (
            NOW(),
            NEW.num_patrimonio,
            OLD.ambientes_id_ambientes,
            NEW.ambientes_id_ambientes,
            @id_usuario_logado -- A trigger vai pegar o valor daqui
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura para tabela `token`
--

CREATE TABLE `token` (
  `idtoken` int(11) NOT NULL,
  `token` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `usuarios_id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `token`
--

INSERT INTO `token` (`idtoken`, `token`, `created_at`, `usuarios_id_usuario`) VALUES
(1, 'bb9bdfcf082602e6bf71e6b58363c0619021270af6aa1ba2e75042a8c4aa700cace613f1287d0740355a87e8c91640636bf6f608fc783d737523d29374fce944', '2025-09-05 18:16:06', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `usuario_nome` varchar(100) NOT NULL,
  `usuario_nivel` enum('administrador','gestor','colaborador') NOT NULL,
  `usuario_email` varchar(100) NOT NULL,
  `usuario_del` enum('ativo','inativo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `usuario_nome`, `usuario_nivel`, `usuario_email`, `usuario_del`) VALUES
(1, 'Carlos', 'administrador', 'carlos@mail', 'ativo'),
(2, 'Ana', 'colaborador', 'ana@mail', 'ativo');

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `verificacao`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `verificacao` (
);

-- --------------------------------------------------------

--
-- Estrutura para tabela `verificacao_ambiente`
--

CREATE TABLE `verificacao_ambiente` (
  `id_verificacao` int(11) NOT NULL,
  `data_hora` datetime NOT NULL,
  `verificacao_del` enum('ativo','inativo') NOT NULL,
  `usuarios_id_usuario` int(11) NOT NULL,
  `ambientes_id_ambientes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `verificacao_ambiente`
--

INSERT INTO `verificacao_ambiente` (`id_verificacao`, `data_hora`, `verificacao_del`, `usuarios_id_usuario`, `ambientes_id_ambientes`) VALUES
(1, '2025-09-05 14:50:49', 'ativo', 1, 1);

-- --------------------------------------------------------

--
-- Estrutura para view `movimento`
--
DROP TABLE IF EXISTS `movimento`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `movimento`  AS SELECT `mi`.`data_hora` AS `data_hora`, `a1`.`ambiente_nome` AS `origem`, `a2`.`ambiente_nome` AS `destino`, `p`.`num_patrimonio` AS `num_patrimonio`, `p`.`patrimonio_nome` AS `patrimonio_nome`, `p`.`status` AS `status`, `p`.`patrimonio_img` AS `patrimonio_img`, `p`.`denominacao` AS `denominacao`, `u`.`usuario_nome` AS `usuario_nome`, `u`.`usuario_nivel` AS `usuario_nivel` FROM ((((`movimentacao_item` `mi` join `ambientes` `a1` on(`mi`.`origem` = `a1`.`id_ambientes`)) join `ambientes` `a2` on(`mi`.`destino` = `a2`.`id_ambientes`)) join `patrimonios` `p` on(`mi`.`patrimonios_num_patrimonio` = `p`.`num_patrimonio`)) join `usuarios` `u` on(`mi`.`usuarios_id_usuario` = `u`.`id_usuario`)) ;

-- --------------------------------------------------------

--
-- Estrutura para view `verificacao`
--
DROP TABLE IF EXISTS `verificacao`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `verificacao`  AS SELECT `verificacao_ambiente`.`data_hora` AS `data_hora`, `ambientes`.`ambiente_nome` AS `ambiente_nome`, `patrimonios`.`num_patrimonio` AS `num_patrimonio`, `patrimonios`.`patrimonio_nome` AS `patrimonio_nome`, `patrimonios`.`status` AS `status`, `patrimonios`.`patrimonio_img` AS `patrimonio_img`, `patrimonios`.`denominacao` AS `denominacao`, `usuarios`.`usuario_nome` AS `usuario_nome`, `usuarios`.`usuario_nivel` AS `usuario_nivel` FROM (((`verificacao_ambiente` join `ambientes` on(`verificacao_ambiente`.`ambientes_id_ambientes` = `ambientes`.`id_ambientes`)) join `usuarios` on(`verificacao_ambiente`.`usuarios_id_usuario` = `usuarios`.`id_usuario`)) join `patrimonios` on(`verificacao_ambiente`.`patrimonios_num_patrimonio` = `patrimonios`.`num_patrimonio`)) ;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `ambientes`
--
ALTER TABLE `ambientes`
  ADD PRIMARY KEY (`id_ambientes`);

--
-- Índices de tabela `ambientes_has_usuarios`
--
ALTER TABLE `ambientes_has_usuarios`
  ADD PRIMARY KEY (`ambientes_id_ambientes`,`usuarios_id_usuario`),
  ADD KEY `fk_ambientes_has_usuarios_usuarios1_idx` (`usuarios_id_usuario`),
  ADD KEY `fk_ambientes_has_usuarios_ambientes1_idx` (`ambientes_id_ambientes`);

--
-- Índices de tabela `arquivo_importacao`
--
ALTER TABLE `arquivo_importacao`
  ADD PRIMARY KEY (`id_arquivo`),
  ADD KEY `fk_arquivo_importacao_usuarios1_idx` (`usuarios_id_usuario`);

--
-- Índices de tabela `movimentacao_item`
--
ALTER TABLE `movimentacao_item`
  ADD PRIMARY KEY (`id_movimentacao`),
  ADD KEY `fk_MovimentacaoItem_Patrimonios1_idx` (`patrimonios_num_patrimonio`),
  ADD KEY `fk_MovimentacaoItem_Ambientes1_idx` (`origem`),
  ADD KEY `fk_MovimentacaoItem_Ambientes2_idx` (`destino`),
  ADD KEY `fk_movimentacao_item_usuarios1_idx` (`usuarios_id_usuario`);

--
-- Índices de tabela `patrimonios`
--
ALTER TABLE `patrimonios`
  ADD PRIMARY KEY (`num_patrimonio`),
  ADD KEY `fk_patrimonios_ambientes1_idx` (`ambientes_id_ambientes`),
  ADD KEY `fk_patrimonios_verificacao_ambiente1_idx` (`verificacao_ambiente_id_verificacao`);

--
-- Índices de tabela `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`idtoken`),
  ADD KEY `fk_token_usuarios1_idx` (`usuarios_id_usuario`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Índices de tabela `verificacao_ambiente`
--
ALTER TABLE `verificacao_ambiente`
  ADD PRIMARY KEY (`id_verificacao`),
  ADD KEY `fk_verificacao_ambiente_usuarios1_idx` (`usuarios_id_usuario`),
  ADD KEY `fk_verificacao_ambiente_ambientes1_idx` (`ambientes_id_ambientes`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `ambientes`
--
ALTER TABLE `ambientes`
  MODIFY `id_ambientes` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `arquivo_importacao`
--
ALTER TABLE `arquivo_importacao`
  MODIFY `id_arquivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `movimentacao_item`
--
ALTER TABLE `movimentacao_item`
  MODIFY `id_movimentacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `patrimonios`
--
ALTER TABLE `patrimonios`
  MODIFY `num_patrimonio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1203;

--
-- AUTO_INCREMENT de tabela `token`
--
ALTER TABLE `token`
  MODIFY `idtoken` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `verificacao_ambiente`
--
ALTER TABLE `verificacao_ambiente`
  MODIFY `id_verificacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `ambientes_has_usuarios`
--
ALTER TABLE `ambientes_has_usuarios`
  ADD CONSTRAINT `fk_ambientes_has_usuarios_ambientes1` FOREIGN KEY (`ambientes_id_ambientes`) REFERENCES `ambientes` (`id_ambientes`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ambientes_has_usuarios_usuarios1` FOREIGN KEY (`usuarios_id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `arquivo_importacao`
--
ALTER TABLE `arquivo_importacao`
  ADD CONSTRAINT `fk_arquivo_importacao_usuarios1` FOREIGN KEY (`usuarios_id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `movimentacao_item`
--
ALTER TABLE `movimentacao_item`
  ADD CONSTRAINT `fk_MovimentacaoItem_Ambientes1` FOREIGN KEY (`origem`) REFERENCES `ambientes` (`id_ambientes`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_MovimentacaoItem_Ambientes2` FOREIGN KEY (`destino`) REFERENCES `ambientes` (`id_ambientes`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_MovimentacaoItem_Patrimonios1` FOREIGN KEY (`patrimonios_num_patrimonio`) REFERENCES `patrimonios` (`num_patrimonio`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_movimentacao_item_usuarios1` FOREIGN KEY (`usuarios_id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `patrimonios`
--
ALTER TABLE `patrimonios`
  ADD CONSTRAINT `fk_patrimonios_ambientes1` FOREIGN KEY (`ambientes_id_ambientes`) REFERENCES `ambientes` (`id_ambientes`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_patrimonios_verificacao_ambiente1` FOREIGN KEY (`verificacao_ambiente_id_verificacao`) REFERENCES `verificacao_ambiente` (`id_verificacao`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `fk_token_usuarios1` FOREIGN KEY (`usuarios_id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Restrições para tabelas `verificacao_ambiente`
--
ALTER TABLE `verificacao_ambiente`
  ADD CONSTRAINT `fk_verificacao_ambiente_ambientes1` FOREIGN KEY (`ambientes_id_ambientes`) REFERENCES `ambientes` (`id_ambientes`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_verificacao_ambiente_usuarios1` FOREIGN KEY (`usuarios_id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
