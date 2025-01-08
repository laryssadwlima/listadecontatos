-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 04/11/2024 às 10:27
-- Versão do servidor: 8.3.0
-- Versão do PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `comunicacao`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(50) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `admins`
--

INSERT INTO `admins` (`id`, `usuario`, `senha`, `nome`, `created_at`) VALUES
(1, 'DHO', '123', 'Departamento RH', '2024-11-02 00:17:25'),
(2, 'adminTI', '123', 'Administrador TI', '2024-11-02 00:17:25');

-- --------------------------------------------------------

--
-- Estrutura para tabela `listas_distribuicao`
--

DROP TABLE IF EXISTS `listas_distribuicao`;
CREATE TABLE IF NOT EXISTS `listas_distribuicao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `descricao` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `listas_distribuicao`
--

INSERT INTO `listas_distribuicao` (`id`, `nome`, `email`, `descricao`, `created_at`) VALUES
(1, 'teste 1', 'tetse@fibrafort.com.br', NULL, '2024-11-02 00:28:35');

-- --------------------------------------------------------

--
-- Estrutura para tabela `membros_lista`
--

DROP TABLE IF EXISTS `membros_lista`;
CREATE TABLE IF NOT EXISTS `membros_lista` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lista_id` int DEFAULT NULL,
  `usuario_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `lista_id` (`lista_id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `membros_lista`
--

INSERT INTO `membros_lista` (`id`, `lista_id`, `usuario_id`, `created_at`) VALUES
(1, 1, 1, '2024-11-02 00:28:35'),
(2, 1, 2, '2024-11-02 00:28:35'),
(3, 1, 5, '2024-11-02 00:28:35');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `setor` varchar(50) NOT NULL,
  `planta` enum('P1','P2') NOT NULL,
  `email` varchar(100) NOT NULL,
  `ramal` varchar(10) DEFAULT NULL,
  `telefone_comercial` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `setor`, `planta`, `email`, `ramal`, `telefone_comercial`) VALUES
(1, 'João Silva', 'TI', 'P1', 'joao.silva@empresa.com', '1234', '98765-4321'),
(2, 'Maria Oliveira', 'RH', 'P2', 'maria.oliveira@empresa.com', '5678', '98765-4322'),
(3, 'Carlos Pereira', 'Financeiro', 'P1', 'carlos.pereira@empresa.com', '9101', '98765-4323'),
(5, 'Paulo Souza', 'TI', 'P1', 'paulo.souza@empresa.com', '3141', '98765-4325');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
