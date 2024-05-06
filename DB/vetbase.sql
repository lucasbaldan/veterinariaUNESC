-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 07/05/2024 às 01:12
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.0.28

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
-- Estrutura para tabela `ficha_lpv`
--

CREATE TABLE `ficha_lpv` (
  `CD_FICHA_LPV` int(11) NOT NULL,
  `DT_FICHA` date DEFAULT NULL,
  `ANIMAL` varchar(45) DEFAULT NULL,
  `CD_USUARIO_VETERINARIO` int(11) DEFAULT NULL,
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
  `DS_DIAGNOSTICO_PRESUNTIVO` varchar(45) DEFAULT NULL,
  `DS_NOME_ANIMAL` varchar(45) DEFAULT NULL,
  `DS_EPIDEMIOLOGIA_HISTORIA_CLINICA` varchar(500) DEFAULT NULL,
  `DS_LESOES_MACROSCOPICAS` varchar(300) DEFAULT NULL,
  `DS_DIAGNOSTICO` varchar(100) DEFAULT NULL,
  `DS_RELATORIO` varchar(500) DEFAULT NULL
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
-- Estrutura para tabela `tipos_acessos_usuarios`
--

CREATE TABLE `tipos_acessos_usuarios` (
  `CD_TIPO_USUARIO` int(11) NOT NULL,
  `NM_TIPO_USUARIO` varchar(45) NOT NULL,
  `FL_ACESSAR` int(11) DEFAULT NULL,
  `FL_EDITAR` int(11) DEFAULT NULL,
  `FL_EXCLUIR` int(11) DEFAULT NULL,
  `ACESSOS_TIPOS_USUARIOS_CD_ACESSO_TIPO_USUARIO` int(11) NOT NULL,
  `ACESSOS_TIPOS_USUARIOS_USUARIOS_CD_USUARIO` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `PESSOAS_CD_PESSOA` int(11) NOT NULL,
  `CD_TIPO_USUARIO` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `acessos_tipos_usuarios`
--
ALTER TABLE `acessos_tipos_usuarios`
  ADD PRIMARY KEY (`CD_ACESSO_TIPO_USUARIO`);

--
-- Índices de tabela `ficha_lpv`
--
ALTER TABLE `ficha_lpv`
  ADD PRIMARY KEY (`CD_FICHA_LPV`);

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
-- Índices de tabela `tipos_acessos_usuarios`
--
ALTER TABLE `tipos_acessos_usuarios`
  ADD PRIMARY KEY (`CD_TIPO_USUARIO`);

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
-- AUTO_INCREMENT de tabela `ficha_lpv`
--
ALTER TABLE `ficha_lpv`
  MODIFY `CD_FICHA_LPV` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT de tabela `tipos_acessos_usuarios`
--
ALTER TABLE `tipos_acessos_usuarios`
  MODIFY `CD_TIPO_USUARIO` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `CD_USUARIO` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
