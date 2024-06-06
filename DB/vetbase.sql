-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/06/2024 às 05:27
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
-- Banco de dados: `vetbase`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `acessos_tipos_usuarios`
--

CREATE TABLE `acessos_tipos_usuarios` (
  `CD_ACESSO_TIPO_USUARIO` int(11) NOT NULL,
  `CD_USUARIO` int(11) DEFAULT NULL,
  `CD_TIPOS_ACESSOS_USUARIOS` int(11) DEFAULT NULL,
  `USUARIOS_CD_USUARIO` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `especies`
--

CREATE TABLE `especies` (
  `CD_ESPECIE` int(11) NOT NULL,
  `DESCRICAO` varchar(500) NOT NULL,
  `FL_ATIVO` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `especies`
--

INSERT INTO `especies` (`CD_ESPECIE`, `DESCRICAO`, `FL_ATIVO`) VALUES
(1, 'Pastor-Alemão', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `ficha_lpv`
--

CREATE TABLE `ficha_lpv` (
  `CD_FICHA_LPV` int(11) NOT NULL,
  `DT_FICHA` date DEFAULT NULL,
  `ANIMAL` varchar(45) DEFAULT NULL,
  `NM_VET_REMETENTE` varchar(50) DEFAULT NULL,
  `NR_TEL_VET_REMETENTE` varchar(50) DEFAULT NULL,
  `DS_EMAIL_VET_REMETENTE` varchar(50) DEFAULT NULL,
  `CRMV_VET_REMETENTE` varchar(50) DEFAULT NULL,
  `NM_CIDADE_VET_REMETENTE` varchar(50) DEFAULT NULL,
  `CD_USUARIO_PLANTONISTA` int(11) DEFAULT NULL,
  `NM_PROPRIETARIO` varchar(45) DEFAULT NULL,
  `NR_TELEFONE_PROPRIETARIO` varchar(45) DEFAULT NULL,
  `CIDADE_PROPRIEDADE` varchar(45) DEFAULT NULL,
  `DS_ESPECIE` varchar(45) DEFAULT NULL,
  `DS_RACA` varchar(45) DEFAULT NULL,
  `DS_SEXO` varchar(45) DEFAULT NULL,
  `IDADE` int(11) DEFAULT NULL,
  `TOTAL_ANIMAIS` int(11) DEFAULT NULL,
  `QTD_ANIMAIS_MORTOS` int(11) DEFAULT NULL,
  `QTD_ANIMAIS_DOENTES` int(11) DEFAULT NULL,
  `DS_MATERIAL_RECEBIDO` varchar(50) DEFAULT NULL,
  `DS_DIAGNOSTICO_PRESUNTIVO` varchar(45) DEFAULT NULL,
  `FL_AVALIACAO_TUMORAL_COM_MARGEM` char(1) DEFAULT 'N',
  `DS_NOME_ANIMAL` varchar(45) DEFAULT NULL,
  `DS_EPIDEMIOLOGIA_HISTORIA_CLINICA` varchar(500) DEFAULT NULL,
  `DS_LESOES_MACROSCOPICAS` varchar(300) DEFAULT NULL,
  `DS_LESOES_HISTOLOGICAS` varchar(300) DEFAULT NULL,
  `DS_DIAGNOSTICO` varchar(100) DEFAULT NULL,
  `DS_RELATORIO` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `grupos_usuarios`
--

CREATE TABLE `grupos_usuarios` (
  `CD_GRUPO_USUARIOS` int(11) NOT NULL,
  `NM_GRUPO_USUARIOS` varchar(45) NOT NULL,
  `FL_ACESSAR` int(11) DEFAULT NULL,
  `FL_EDITAR` int(11) DEFAULT NULL,
  `FL_EXCLUIR` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `CD_LOG` int(11) NOT NULL,
  `DT_HORA` datetime NOT NULL,
  `NM_PESSOA` varchar(45) NOT NULL,
  `ACAO` varchar(45) NOT NULL,
  `DESCRICAO` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pessoas`
--

CREATE TABLE `pessoas` (
  `CD_PESSOA` int(11) NOT NULL,
  `NM_PESSOA` varchar(45) NOT NULL,
  `CIDADE` varchar(45) NOT NULL,
  `NR_TELEFONE` varchar(45) NOT NULL,
  `DS_EMAIL` varchar(45) NOT NULL,
  `NR_CRMV` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `racas`
--

CREATE TABLE `racas` (
  `CD_RACA` int(11) NOT NULL,
  `DESCRICAO` varchar(500) NOT NULL,
  `FL_ATIVO` int(1) NOT NULL DEFAULT 1,
  `CD_ESPECIE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipo_animal`
--

CREATE TABLE `tipo_animal` (
  `cd_tipo_animal` int(11) NOT NULL,
  `descricao` varchar(200) NOT NULL,
  `fl_ativo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `tipo_animal`
--

INSERT INTO `tipo_animal` (`cd_tipo_animal`, `descricao`, `fl_ativo`) VALUES
(1, 'Anta', 1),
(2, 'Ariranha', 1),
(3, 'Bicho-preguiça', 1),
(4, 'Bugio', 1),
(5, 'Cachorro', 1),
(6, 'Cavalo', 1),
(7, 'Coati', 1),
(8, 'Cotia', 1),
(9, 'Cutia', 1),
(10, 'Esquilo', 1),
(11, 'Gato', 0),
(12, 'Jaguatirica', 0),
(13, 'Lobo-guará', 0),
(14, 'Morcego', 0),
(15, 'Onça', 0),
(16, 'Paca', 0),
(17, 'Porco-do-mato', 0),
(18, 'Quati', 0),
(19, 'Raposa', 1),
(21, 'Tamanduá', 0),
(30, 'Macaco Branco', 1),
(42, 'Amazonense', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `CD_USUARIO` int(11) NOT NULL,
  `CD_PESSOA` int(11) DEFAULT NULL,
  `USUARIO` varchar(45) DEFAULT NULL,
  `SENHA` varchar(45) DEFAULT NULL,
  `FL_ATIVO` char(1) DEFAULT 'S',
  `CD_GRUPO_USUARIOS` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`CD_USUARIO`, `CD_PESSOA`, `USUARIO`, `SENHA`, `FL_ATIVO`, `CD_GRUPO_USUARIOS`) VALUES
(1, 1, 'carlos', 'carlos', 'S', 2);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `acessos_tipos_usuarios`
--
ALTER TABLE `acessos_tipos_usuarios`
  ADD PRIMARY KEY (`CD_ACESSO_TIPO_USUARIO`);

--
-- Índices de tabela `especies`
--
ALTER TABLE `especies`
  ADD PRIMARY KEY (`CD_ESPECIE`);

--
-- Índices de tabela `ficha_lpv`
--
ALTER TABLE `ficha_lpv`
  ADD PRIMARY KEY (`CD_FICHA_LPV`);

--
-- Índices de tabela `grupos_usuarios`
--
ALTER TABLE `grupos_usuarios`
  ADD PRIMARY KEY (`CD_GRUPO_USUARIOS`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`CD_LOG`);

--
-- Índices de tabela `pessoas`
--
ALTER TABLE `pessoas`
  ADD PRIMARY KEY (`CD_PESSOA`);

--
-- Índices de tabela `racas`
--
ALTER TABLE `racas`
  ADD PRIMARY KEY (`CD_RACA`),
  ADD KEY `FK_ESPECIE_RACAS` (`CD_ESPECIE`);

--
-- Índices de tabela `tipo_animal`
--
ALTER TABLE `tipo_animal`
  ADD PRIMARY KEY (`cd_tipo_animal`),
  ADD UNIQUE KEY `descricao` (`descricao`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`CD_USUARIO`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `acessos_tipos_usuarios`
--
ALTER TABLE `acessos_tipos_usuarios`
  MODIFY `CD_ACESSO_TIPO_USUARIO` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `especies`
--
ALTER TABLE `especies`
  MODIFY `CD_ESPECIE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `ficha_lpv`
--
ALTER TABLE `ficha_lpv`
  MODIFY `CD_FICHA_LPV` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `grupos_usuarios`
--
ALTER TABLE `grupos_usuarios`
  MODIFY `CD_GRUPO_USUARIOS` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `CD_LOG` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pessoas`
--
ALTER TABLE `pessoas`
  MODIFY `CD_PESSOA` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `racas`
--
ALTER TABLE `racas`
  MODIFY `CD_RACA` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipo_animal`
--
ALTER TABLE `tipo_animal`
  MODIFY `cd_tipo_animal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `CD_USUARIO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `racas`
--
ALTER TABLE `racas`
  ADD CONSTRAINT `FK_ESPECIE_RACAS` FOREIGN KEY (`CD_ESPECIE`) REFERENCES `especies` (`CD_ESPECIE`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
