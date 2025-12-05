-- phpMyAdmin SQL Dump
-- version 5.2.2deb1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 05/12/2025 às 18:15
-- Versão do servidor: 11.8.2-MariaDB-1 from Debian
-- Versão do PHP: 8.3.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `enzo-zanardi`
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
  `categoria` enum('eletroeletronica','oficina','quimica','t.i','panificação','metalmecânica') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Despejando dados para a tabela `ambientes`
--

INSERT INTO `ambientes` (`id_ambientes`, `localizacao`, `ambiente_nome`, `ambiente_del`, `categoria`) VALUES
(9, 11021, 'Sala de T.I', 'ativo', 't.i'),
(10, 12032, 'Sala de Informática', 'ativo', 't.i'),
(11, 12345, 'Sala de T.I 01', 'ativo', 't.i'),
(12, 23456, 'Laboratório de Química', 'ativo', 'quimica'),
(13, 123432423, 'Usina', 'ativo', 'quimica');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ambientes_has_usuarios`
--

CREATE TABLE `ambientes_has_usuarios` (
  `id` int(11) NOT NULL,
  `ambientes_id_ambiente` int(11) NOT NULL,
  `usuarios_id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Despejando dados para a tabela `arquivo_importacao`
--

INSERT INTO `arquivo_importacao` (`id_arquivo`, `data_importacao`, `resultado`, `arquivo`, `arquivo_del`, `usuarios_id_usuario`) VALUES
(2, '2025-09-17 08:06:47', 'sucesso', '/var/www/html/enzo-zanardi/patrimonio_backend/Patrimonio_Endpoints/Post/uploads/68ca96476b3343.28027478.xlsx', 'ativo', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `itens_ambiente`
--

CREATE TABLE `itens_ambiente` (
  `id` int(11) NOT NULL,
  `id_ambiente` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Despejando dados para a tabela `movimentacao_item`
--

INSERT INTO `movimentacao_item` (`id_movimentacao`, `data_hora`, `movimentacao_del`, `patrimonios_num_patrimonio`, `origem`, `destino`, `usuarios_id_usuario`) VALUES
(1, '2025-09-05 13:44:07', 'ativo', 1001, 1, 2, 2),
(2, '2025-12-02 14:57:28', 'ativo', 695, 11, 11, 9),
(3, '2025-12-02 14:57:28', 'ativo', 677, 11, 11, 9),
(4, '2025-12-02 14:57:28', 'ativo', 1005, 11, 11, 9),
(5, '2025-12-02 14:57:28', 'ativo', 1009, 11, 11, 9),
(6, '2025-12-02 14:57:28', 'ativo', 1013, 11, 11, 9),
(7, '2025-12-02 14:57:28', 'ativo', 1017, 11, 11, 9),
(8, '2025-12-02 14:57:28', 'ativo', 1021, 11, 11, 9),
(9, '2025-12-02 14:57:28', 'ativo', 1025, 11, 11, 9),
(10, '2025-12-02 14:57:28', 'ativo', 1029, 11, 11, 9),
(11, '2025-12-02 14:57:28', 'ativo', 1001, 11, 11, 9),
(12, '2025-12-02 15:08:02', 'ativo', 680, 10, 10, 9),
(13, '2025-12-05 13:17:56', 'ativo', 685, 9, 9, 8),
(14, '2025-12-05 13:17:56', 'ativo', 688, 9, 9, 8),
(15, '2025-12-05 13:17:56', 'ativo', 693, 9, 9, 8),
(16, '2025-12-05 13:17:56', 'ativo', 1003, 9, 9, 8),
(17, '2025-12-05 13:17:56', 'ativo', 1007, 9, 9, 8),
(18, '2025-12-05 13:17:56', 'ativo', 1011, 9, 9, 8),
(19, '2025-12-05 13:17:56', 'ativo', 1015, 9, 9, 8),
(20, '2025-12-05 13:17:56', 'ativo', 1019, 9, 9, 8),
(21, '2025-12-05 13:17:56', 'ativo', 1023, 9, 9, 8),
(22, '2025-12-05 13:17:56', 'ativo', 1027, 9, 9, 8),
(23, '2025-12-05 13:17:56', 'ativo', 1001, 11, 11, 8),
(24, '2025-12-05 13:26:54', 'ativo', 702, 10, 10, 8),
(25, '2025-12-05 13:26:54', 'ativo', 690, 10, 10, 8),
(26, '2025-12-05 13:26:54', 'ativo', 1004, 10, 10, 8),
(27, '2025-12-05 13:26:54', 'ativo', 1008, 10, 10, 8),
(28, '2025-12-05 13:26:54', 'ativo', 1012, 10, 10, 8),
(29, '2025-12-05 13:26:54', 'ativo', 1016, 10, 10, 8),
(30, '2025-12-05 13:26:54', 'ativo', 1020, 10, 10, 8),
(31, '2025-12-05 13:26:54', 'ativo', 1024, 10, 10, 8),
(32, '2025-12-05 13:26:54', 'ativo', 1028, 10, 10, 8),
(33, '2025-12-05 13:26:54', 'ativo', 680, 10, 10, 8),
(34, '2025-12-05 13:45:03', 'ativo', 680, 10, 10, 9);

-- --------------------------------------------------------

--
-- Estrutura para tabela `patrimonios`
--

CREATE TABLE `patrimonios` (
  `id_patrimonio` int(11) NOT NULL,
  `patrimonio_del` enum('ativo','inativo') NOT NULL,
  `status` varchar(60) NOT NULL,
  `patrimonio_img` longtext NOT NULL,
  `patrimonio_img2` longtext NOT NULL,
  `denominacao` varchar(100) NOT NULL,
  `ambientes_id_ambientes` int(11) NOT NULL,
  `verificacao_ambiente_id_verificacao` int(11) DEFAULT NULL,
  `num_patrimonio` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Despejando dados para a tabela `patrimonios`
--

INSERT INTO `patrimonios` (`id_patrimonio`, `patrimonio_del`, `status`, `patrimonio_img`, `patrimonio_img2`, `denominacao`, `ambientes_id_ambientes`, `verificacao_ambiente_id_verificacao`, `num_patrimonio`, `created_at`) VALUES
(62, 'ativo', 'item em outro ambiente', 'ti_SaladeTI01_20251022_183643_cfb3e6.png', 'ti_SaladeTI01_20251022_183650_b4922d.png', 'Computador muito incrível', 11, NULL, 695, '2025-10-01 14:36:36'),
(63, 'ativo', 'item em outro ambiente', '', '', 'Computador muito incrível', 11, NULL, 677, '2025-10-01 14:36:36'),
(64, 'ativo', 'pendente', '', '', 'Computador muito incrível', 12, NULL, 701, '2025-10-01 14:36:36'),
(65, 'ativo', 'pendente', '', '', 'Computador muito incrível', 12, NULL, 707, '2025-10-01 14:36:36'),
(66, 'ativo', 'item em outro ambiente', '', '', 'Computador muito incrível', 9, NULL, 685, '2025-10-01 14:36:36'),
(67, 'ativo', 'item em outro ambiente', '', '', 'Computador muito incrível', 9, NULL, 688, '2025-10-01 14:36:36'),
(68, 'ativo', 'item em outro ambiente', '', '', 'Computador muito incrível', 9, NULL, 693, '2025-10-01 14:36:36'),
(69, 'ativo', 'item nao cadastrado', 'ti_SaladeTI_20251205_170316_d3c52a.png', 'ti_SaladeTI_20251205_170317_6d1621.png', 'Computador muito incrível', 10, NULL, 680, '2025-10-01 14:36:36'),
(70, 'ativo', 'item em outro ambiente', '', '', 'Computador muito incrível', 10, NULL, 702, '2025-10-01 14:36:36'),
(71, 'ativo', 'item em outro ambiente', '', '', 'Computador muito incrível', 10, NULL, 690, '2025-10-01 14:36:36'),
(72, 'ativo', 'item nao cadastrado', 'ti_SaladeTI01_20251202_183617_09c5cd.png', 'ti_SaladeTI01_20251202_183618_4ad2e5.png', 'Notebook', 11, NULL, 1001, '2025-10-01 14:36:36'),
(73, 'ativo', 'pendente', '', '', 'Monitor', 12, NULL, 1002, '2025-10-01 14:36:36'),
(74, 'ativo', 'item em outro ambiente', '', '', 'Mouse', 9, NULL, 1003, '2025-10-01 14:36:36'),
(75, 'ativo', 'item em outro ambiente', '', '', 'Teclado', 10, NULL, 1004, '2025-10-01 14:36:36'),
(76, 'ativo', 'item em outro ambiente', '', '', 'Impressora', 11, NULL, 1005, '2025-10-01 14:36:36'),
(77, 'ativo', 'pendente', '', '', 'Projetor', 12, NULL, 1006, '2025-10-01 14:36:36'),
(78, 'ativo', 'item em outro ambiente', '', '', 'Cadeira de Escritório', 9, NULL, 1007, '2025-10-01 14:36:36'),
(79, 'ativo', 'item em outro ambiente', '', '', 'Mesa de Escritório', 10, NULL, 1008, '2025-10-01 14:36:36'),
(80, 'ativo', 'item em outro ambiente', '', '', 'Headset', 11, NULL, 1009, '2025-10-01 14:36:36'),
(81, 'ativo', 'pendente', '', '', 'Webcam', 12, NULL, 1010, '2025-10-01 14:36:36'),
(82, 'ativo', 'item em outro ambiente', '', '', 'HD Externo', 9, NULL, 1011, '2025-10-01 14:36:36'),
(83, 'ativo', 'item em outro ambiente', '', '', 'SSD Externo', 10, NULL, 1012, '2025-10-01 14:36:36'),
(84, 'ativo', 'item em outro ambiente', '', '', 'Smartphone', 11, NULL, 1013, '2025-10-01 14:36:36'),
(85, 'ativo', 'pendente', '', '', 'Tablet', 12, NULL, 1014, '2025-10-01 14:36:36'),
(86, 'ativo', 'item em outro ambiente', '', '', 'Servidor', 9, NULL, 1015, '2025-10-01 14:36:36'),
(87, 'ativo', 'item em outro ambiente', '', '', 'Switch de Rede', 10, NULL, 1016, '2025-10-01 14:36:36'),
(88, 'ativo', 'item em outro ambiente', '', '', 'Roteador Wi-Fi', 11, NULL, 1017, '2025-10-01 14:36:36'),
(89, 'ativo', 'pendente', '', '', 'Nobreak', 12, NULL, 1018, '2025-10-01 14:36:36'),
(90, 'ativo', 'item em outro ambiente', '', '', 'Estabilizador', 9, NULL, 1019, '2025-10-01 14:36:36'),
(91, 'ativo', 'item em outro ambiente', '', '', 'Placa de Vídeo', 10, NULL, 1020, '2025-10-01 14:36:36'),
(92, 'ativo', 'item em outro ambiente', '', '', 'Microfone', 11, NULL, 1021, '2025-10-01 14:36:36'),
(93, 'ativo', 'pendente', '', '', 'Caixa de Som', 12, NULL, 1022, '2025-10-01 14:36:36'),
(94, 'ativo', 'item em outro ambiente', '', '', 'Luminária', 9, NULL, 1023, '2025-10-01 14:36:36'),
(95, 'ativo', 'item em outro ambiente', '', '', 'TV LED', 10, NULL, 1024, '2025-10-01 14:36:36'),
(96, 'ativo', 'item em outro ambiente', '', '', 'Ar Condicionado', 11, NULL, 1025, '2025-10-01 14:36:36'),
(97, 'ativo', 'pendente', '', '', 'Ventilador', 12, NULL, 1026, '2025-10-01 14:36:36'),
(98, 'ativo', 'item em outro ambiente', '', '', 'Notebook Gamer', 9, NULL, 1027, '2025-10-01 14:36:36'),
(99, 'ativo', 'item em outro ambiente', '', '', 'Chromebook', 10, NULL, 1028, '2025-10-01 14:36:36'),
(100, 'ativo', 'item em outro ambiente', '', '', 'Scanner', 11, NULL, 1029, '2025-10-01 14:36:36'),
(101, 'ativo', 'pendente', '', '', 'Console de Videogame', 12, NULL, 1030, '2025-10-01 14:36:36'),
(102, 'ativo', 'cadastrado', '', '', 'Teste de item correto', 10, NULL, 31232, '2025-12-05 17:58:19');

--
-- Acionadores `patrimonios`
--
DELIMITER $$
CREATE TRIGGER `trg_movimentacao_patrimonio` AFTER UPDATE ON `patrimonios` FOR EACH ROW BEGIN
    IF OLD.ambientes_id_ambientes <> NEW.ambientes_id_ambientes
       OR OLD.status <> NEW.status THEN

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
            @current_user_id
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
  `id_token` int(11) NOT NULL,
  `token` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `usuarios_id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Despejando dados para a tabela `token`
--

INSERT INTO `token` (`id_token`, `token`, `created_at`, `usuarios_id_usuario`) VALUES
(280, '197d82c793dfcca30422eaa22ff7f5d808350f43be0b6a0bfb0027546ea0ac8ae6dfe1eedf29aca97263ccdb575d1333252f62b9eb122575dc2eeddc59850e91', '2025-12-05 17:31:47', 15),
(281, '5944427e07008056c213846717f72653712bbb5c35ce345a809ae166d5afd20bdc228aa60d77b072e2e116277f1df4d186fc67355eccb6a86cf53f44573500f0', '2025-12-05 18:14:27', 9);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `usuario_nome` varchar(100) NOT NULL,
  `usuario_nivel` enum('administrador','gestor','colaborador') NOT NULL,
  `usuario_email` varchar(100) NOT NULL,
  `usuario_del` enum('ativo','inativo') NOT NULL,
  `senha` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `usuario_nome`, `usuario_nivel`, `usuario_email`, `usuario_del`, `senha`) VALUES
(8, 'teste', 'colaborador', 'teste@mail', 'ativo', '3c9909afec25354d551dae21590bb26e38d53f2173b8d3dc3eee4c047e7ab1c1eb8b85103e3be7ba613b31bb5c9c36214dc9f14a42fd7a2fdb84856bca5c44c2'),
(9, 'joao', 'gestor', 'joao@gmail', 'ativo', '3c9909afec25354d551dae21590bb26e38d53f2173b8d3dc3eee4c047e7ab1c1eb8b85103e3be7ba613b31bb5c9c36214dc9f14a42fd7a2fdb84856bca5c44c2'),
(15, 'Administrador', 'administrador', 'adm@gmail.com', 'ativo', '3c9909afec25354d551dae21590bb26e38d53f2173b8d3dc3eee4c047e7ab1c1eb8b85103e3be7ba613b31bb5c9c36214dc9f14a42fd7a2fdb84856bca5c44c2');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Despejando dados para a tabela `verificacao_ambiente`
--

INSERT INTO `verificacao_ambiente` (`id_verificacao`, `data_hora`, `verificacao_del`, `usuarios_id_usuario`, `ambientes_id_ambientes`) VALUES
(1, '2025-09-05 14:50:49', 'ativo', 1, 1);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuarios_id_usuario` (`usuarios_id_usuario`),
  ADD KEY `ambientes_id_ambientes` (`ambientes_id_ambiente`);

--
-- Índices de tabela `arquivo_importacao`
--
ALTER TABLE `arquivo_importacao`
  ADD PRIMARY KEY (`id_arquivo`);

--
-- Índices de tabela `itens_ambiente`
--
ALTER TABLE `itens_ambiente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ambiente` (`id_ambiente`);

--
-- Índices de tabela `movimentacao_item`
--
ALTER TABLE `movimentacao_item`
  ADD PRIMARY KEY (`id_movimentacao`);

--
-- Índices de tabela `patrimonios`
--
ALTER TABLE `patrimonios`
  ADD PRIMARY KEY (`id_patrimonio`),
  ADD KEY `ambientes_id_ambientes` (`ambientes_id_ambientes`);

--
-- Índices de tabela `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`id_token`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Índices de tabela `verificacao_ambiente`
--
ALTER TABLE `verificacao_ambiente`
  ADD PRIMARY KEY (`id_verificacao`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `ambientes`
--
ALTER TABLE `ambientes`
  MODIFY `id_ambientes` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `ambientes_has_usuarios`
--
ALTER TABLE `ambientes_has_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `arquivo_importacao`
--
ALTER TABLE `arquivo_importacao`
  MODIFY `id_arquivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `itens_ambiente`
--
ALTER TABLE `itens_ambiente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `movimentacao_item`
--
ALTER TABLE `movimentacao_item`
  MODIFY `id_movimentacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de tabela `patrimonios`
--
ALTER TABLE `patrimonios`
  MODIFY `id_patrimonio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT de tabela `token`
--
ALTER TABLE `token`
  MODIFY `id_token` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=282;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
  ADD CONSTRAINT `ambientes_has_usuarios_ibfk_1` FOREIGN KEY (`usuarios_id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  ADD CONSTRAINT `ambientes_has_usuarios_ibfk_2` FOREIGN KEY (`ambientes_id_ambiente`) REFERENCES `ambientes` (`id_ambientes`);

--
-- Restrições para tabelas `itens_ambiente`
--
ALTER TABLE `itens_ambiente`
  ADD CONSTRAINT `itens_ambiente_ibfk_1` FOREIGN KEY (`id_ambiente`) REFERENCES `ambientes` (`id_ambientes`);

--
-- Restrições para tabelas `patrimonios`
--
ALTER TABLE `patrimonios`
  ADD CONSTRAINT `patrimonios_ibfk_1` FOREIGN KEY (`ambientes_id_ambientes`) REFERENCES `ambientes` (`id_ambientes`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
