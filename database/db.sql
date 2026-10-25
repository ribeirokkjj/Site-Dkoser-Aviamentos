-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26/10/2026 às 00:54
-- Versão do servidor: 10.11.16-MariaDB
-- Versão do PHP: 8.3.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `dkoser_aviamentos`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `descricao` text DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `ordem` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `preco`, `descricao`, `imagem`, `categoria_id`, `ativo`, `ordem`) VALUES
(30, 'Linha 2000 jardas adsadsads', 5.90, 'essa é linha 2000 jardas\r\n\r\npara costura em roupas', 'assets/images/produtos/prod_6ade7d0907055.webp', NULL, 1, 1),
(31, 'Renda Poliamida Branca 50mm (5m)', 14.90, 'Rolo com 5 metros de renda delicada, perfeita para acabamentos e lingerie.', '', NULL, 1, 9),
(32, 'Bastidor de Madeira para Bordado 20cm', 18.50, 'Bastidor em madeira com tarracha de ajuste para manter o tecido esticado.', '', NULL, 1, 10),
(33, 'Dedal de Metal Tradicional', 3.50, 'Dedal ergonómico em liga metálica para proteção dos dedos durante a costura manual.', '', NULL, 1, 11),
(34, 'Fita Métrica Retrátil 150cm', 7.90, 'Fita métrica de bolso com botão de recolhimento automático.', '', NULL, 1, 12),
(35, 'Desmanchador de Costura / Abridor de Casas', 4.20, 'Ferramenta essencial para desfazer pontos e abrir casas de botões com precisão.', '', NULL, 1, 13),
(36, 'Cola para Tecido 75g', 11.00, 'Cola lavável e de secagem rápida, ideal para baínhas temporárias e artesanato.', '', NULL, 1, 14),
(37, 'Kit Elástico Chato Branco 10mm (10m)', 16.00, 'Rolo de elástico flexível de alta resistência para vestuário.', '', NULL, 1, 15),
(38, 'Giz de Alfaiate Colorido (Caixa c/ 4)', 6.50, 'Giz para marcação temporária em tecidos nas cores branco, azul, amarelo e rosa.', '', NULL, 1, 16);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
