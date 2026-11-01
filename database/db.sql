-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 31/10/2026 às 22:22
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
  `ordem` int(11) DEFAULT 0,
  `estoque` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `preco`, `descricao`, `imagem`, `categoria_id`, `ativo`, `ordem`, `estoque`) VALUES
(1, 'Linha 2000 jardas Ffilotex', 5.90, 'essa é linha 2000 jardas\r\n\r\npara costura em roupas', '', NULL, 1, 1, 0),
(31, 'Renda Poliamida Branca 50mm (5m)', 14.90, 'Rolo com 5 metros de renda delicada, perfeita para acabamentos e lingerie.', '', NULL, 1, 9, 0),
(32, 'Bastidor de Madeira para Bordado 20cm', 18.50, 'Bastidor em madeira com tarracha de ajuste para manter o tecido esticado.', '', NULL, 1, 10, 0),
(33, 'Dedal de Metal Tradicional', 3.50, 'Dedal ergonómico em liga metálica para proteção dos dedos durante a costura manual.', '', NULL, 1, 11, 0),
(34, 'Fita Métrica Retrátil 150cm', 7.90, 'Fita métrica de bolso com botão de recolhimento automático.', '', NULL, 1, 12, 0),
(35, 'Desmanchador de Costura / Abridor de Casas', 4.20, 'Ferramenta essencial para desfazer pontos e abrir casas de botões com precisão.', '', NULL, 1, 13, 0),
(36, 'Cola para Tecido 75g', 11.00, 'Cola lavável e de secagem rápida, ideal para baínhas temporárias e artesanato.', '', NULL, 1, 14, 0),
(37, 'Kit Elástico Chato Branco 10mm (10m)', 16.00, 'Rolo de elástico flexível de alta resistência para vestuário.', '', NULL, 1, 15, 0),
(38, 'Giz de Alfaiate Colorido (Caixa c/ 4)', 6.50, 'Giz para marcação temporária em tecidos nas cores branco, azul, amarelo e rosa.', '', NULL, 1, 16, 0),
(39, 'Cola para Tecido 75g', 11.00, 'Cola lavável e de secagem rápida, ideal para baínhas temporárias e artesanato.', '', NULL, 1, 14, 0),
(40, 'Kit Elástico Chato Branco 10mm (10m)', 16.00, 'Rolo de elástico flexível de alta resistência para vestuário.', '', NULL, 1, 15, 0),
(41, 'Giz de Alfaiate Colorido (Caixa c/ 4)', 6.50, 'Giz para marcação temporária em tecidos nas cores branco, azul, amarelo e rosa.', '', NULL, 1, 16, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produto_variacoes`
--

CREATE TABLE `produto_variacoes` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `nome_cor` varchar(50) NOT NULL,
  `cor_hex` varchar(10) DEFAULT '#cccccc',
  `estoque` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `produto_variacoes`
--

INSERT INTO `produto_variacoes` (`id`, `produto_id`, `nome_cor`, `cor_hex`, `estoque`) VALUES
(204, 1, '9621', '#63B7EF', 0),
(205, 1, '894', '#6D7193', 0),
(206, 1, '8053', '#6CA8E0', 0),
(207, 1, '1252', '#826252', 0),
(208, 1, '1555', '#E3BB62', 0),
(209, 1, '7828', '#A67ACE', 0),
(210, 1, '7018', '#6A20DC', 0),
(211, 1, '7763', '#9B6C38', 0),
(212, 1, '5482', '#88CAC2', 0),
(213, 1, '3853', '#D827CF', 0),
(214, 1, '6955', '#03ED6E', 0),
(215, 1, '8866', '#30B00B', 0),
(216, 1, '6176', '#B80486', 0),
(217, 1, '9347', '#4E11DD', 0),
(218, 1, '3062', '#8E214D', 0),
(219, 1, '6730', '#B9F355', 0),
(220, 1, '8266', '#9F5C03', 0),
(221, 1, '1477', '#2110B1', 0),
(222, 1, '8852', '#E23CEB', 0),
(223, 1, '2398', '#5B66E4', 0),
(224, 1, '2384', '#294C9F', 0),
(225, 1, '5630', '#C0221E', 0),
(226, 1, '9087', '#B3ED1D', 0),
(227, 1, '3254', '#8EA72E', 0),
(228, 1, '4689', '#4E80EE', 0),
(229, 1, '3449', '#D939A3', 0),
(230, 1, '4103', '#308F5A', 0),
(231, 1, '298', '#07CAEA', 0),
(232, 1, '9428', '#16AC7A', 0),
(233, 1, '4576', '#F164A1', 0),
(234, 1, '5578', '#094493', 0),
(235, 1, '5761', '#BACECD', 0),
(236, 1, '990', '#70216E', 0),
(237, 1, '2526', '#3D4AAF', 0),
(238, 1, '2089', '#BD0CE5', 0),
(239, 1, '3416', '#BA6A41', 0),
(240, 1, '7610', '#A2999A', 0),
(241, 1, '4287', '#7BDE68', 0),
(242, 1, '7553', '#98147D', 0),
(243, 1, '912', '#AFE0E0', 0),
(244, 1, '945', '#896811', 0),
(245, 1, '7721', '#1D06D2', 0),
(246, 1, '7506', '#8C49F9', 0),
(247, 1, '1108', '#12883E', 0),
(248, 1, '1510', '#E6AD25', 0),
(249, 1, '2505', '#F0D537', 0),
(250, 1, '4444', '#037AB6', 0),
(251, 1, '3651', '#317662', 0),
(252, 1, '2689', '#5862E5', 0),
(253, 1, '2344', '#136074', 0),
(254, 1, '1820', '#06AA41', 0),
(255, 1, '9551', '#F64138', 0),
(256, 1, '1910', '#14E52E', 0),
(257, 1, '4611', '#CE441E', 0),
(258, 1, '2007', '#931F31', 0),
(259, 1, '4969', '#937854', 0),
(260, 1, '6463', '#C24DAA', 0),
(261, 1, '631', '#7A9CC7', 0),
(262, 1, '4057', '#F07C66', 0),
(263, 1, '6784', '#4D54FD', 0),
(264, 1, '7293', '#F9638C', 0),
(265, 1, '545', '#61A813', 0),
(266, 1, '3770', '#9F8711', 0),
(267, 1, '6246', '#4182BD', 0),
(268, 1, '7605', '#871CE8', 0),
(269, 1, '5045', '#2E5A0A', 0),
(270, 1, '6183', '#F616C4', 0),
(271, 1, '812', '#B51D76', 0),
(272, 1, '142', '#C240FA', 0),
(273, 1, '6513', '#355FD7', 0),
(274, 1, '7283', '#8ABC6D', 0),
(275, 1, '9866', '#91CB46', 0),
(276, 1, '2791', '#F99876', 0),
(277, 1, '6989', '#171B36', 0),
(278, 1, '275', '#FA92E9', 0),
(279, 1, '6209', '#9345BA', 0),
(280, 1, '7963', '#7C45E2', 0),
(281, 1, '1148', '#C633F6', 0),
(282, 1, '1698', '#1FE88B', 0),
(283, 1, '5764', '#C35427', 0),
(284, 1, '9188', '#77D5D6', 0),
(285, 1, '2933', '#1BBF34', 0),
(286, 1, '308', '#578251', 0),
(287, 1, '8768', '#40937E', 0),
(288, 1, '9149', '#2C6247', 0),
(289, 1, '937', '#60F33D', 0),
(290, 1, '2515', '#F6FF9C', 0),
(291, 1, '3113', '#475F51', 0),
(292, 1, '8276', '#D9F61E', 0),
(293, 1, '2906', '#B72680', 0),
(294, 1, '3035', '#0203A4', 0),
(295, 1, '713', '#4F8875', 0),
(296, 1, '2710', '#D17D65', 0),
(297, 1, '5351', '#4BABB3', 0),
(298, 1, '6400', '#A3739A', 0),
(299, 1, '4923', '#92D850', 0),
(300, 1, '2485', '#05C137', 0),
(301, 1, '8746', '#DF5C80', 0),
(302, 1, '3569', '#E4FC6B', 0),
(303, 1, '2153', '#3529D4', 0),
(304, 1, '6594', '#059B59', 0),
(305, 1, '2203', '#CF1FB5', 0),
(306, 1, '1441', '#5A689D', 0),
(307, 1, '107', '#581BC5', 0),
(308, 1, '6079', '#66F74D', 0),
(309, 1, '3889', '#B95806', 0),
(310, 1, '8225', '#EE789E', 0),
(311, 1, '7325', '#A423D7', 0),
(312, 1, '5912', '#13B2F9', 0),
(313, 1, '745', '#25BBB1', 0),
(314, 1, '9460', '#A7231D', 0),
(315, 1, '1442', '#EFC199', 0),
(316, 1, '3754', '#8C6F59', 0),
(317, 1, '8891', '#674592', 0),
(318, 1, '4488', '#3449B6', 0),
(319, 1, '556', '#5DD4A0', 0),
(320, 1, '9858', '#90BFAC', 0),
(321, 1, '2484', '#D473DE', 0),
(322, 1, '1106', '#0C9DBD', 0),
(323, 1, '3304', '#78A957', 0),
(324, 1, '1320', '#9E299D', 0),
(325, 1, '8925', '#8B4DB1', 0),
(326, 1, '396', '#717B11', 0),
(327, 1, '8760', '#0393CC', 0),
(328, 1, '1451', '#EBAF32', 0),
(329, 1, '6667', '#C3C6AB', 0),
(330, 1, '6905', '#233784', 0),
(331, 1, '5129', '#F025D6', 0),
(332, 1, '3356', '#4E0A73', 0),
(333, 1, '8105', '#51382B', 0),
(334, 1, '8631', '#529060', 0),
(335, 1, '6333', '#ED2652', 0),
(336, 1, '4750', '#AB502C', 0),
(337, 1, '3369', '#8E145D', 0),
(338, 1, '5825', '#1CCF10', 0),
(339, 1, '2873', '#D71668', 0),
(340, 1, '2230', '#63F72B', 0),
(341, 1, '6490', '#4DD85A', 0),
(342, 1, '6330', '#A92832', 0),
(343, 1, '8517', '#0ED14C', 0),
(344, 1, '8883', '#EB5082', 0),
(345, 1, '8719', '#E93E5D', 0),
(346, 1, '1829', '#E65D66', 0),
(347, 1, '3772', '#1C0342', 0),
(348, 1, '5182', '#68AA79', 0),
(349, 1, '8006', '#2A3E18', 0),
(350, 1, '3589', '#7FFDE5', 0),
(351, 1, '1953', '#B8217C', 0),
(352, 1, '5314', '#965B1A', 0),
(353, 1, '7386', '#5C8C26', 0),
(354, 1, '274', '#133D22', 0),
(355, 1, '1298', '#47CA91', 0),
(356, 1, '988', '#1F4138', 0),
(357, 1, '8235', '#DF8B39', 0),
(358, 1, '8109', '#A84EAF', 0),
(359, 1, '7302', '#79A44B', 0),
(360, 1, '3087', '#E3D980', 0),
(361, 1, '702', '#746413', 0),
(362, 1, '3091', '#E4F5A7', 0),
(363, 1, '9205', '#9439EB', 0),
(364, 1, '4694', '#B58D81', 0),
(365, 1, '7004', '#7DD3AE', 0),
(366, 1, '3483', '#AD095C', 0),
(367, 1, '5333', '#7634D0', 0),
(368, 1, '9841', '#FC5BAB', 0),
(369, 1, '4227', '#79120A', 0),
(370, 1, '6101', '#6FD837', 0),
(371, 1, '4204', '#5BEC04', 0),
(372, 1, '4688', '#47CA92', 0),
(373, 1, '8870', '#213693', 0),
(374, 1, '2289', '#025CFE', 0),
(375, 1, '1399', '#7E08B9', 0),
(376, 1, '4440', '#87FC22', 0),
(377, 1, '3797', '#FBEDD2', 0),
(378, 1, '2532', '#648DD1', 0),
(379, 1, '9310', '#AE5AD2', 0),
(380, 1, '8089', '#3A7DBB', 0),
(381, 1, '6287', '#EE300D', 0),
(382, 1, '4605', '#DDF5A2', 0),
(383, 1, '6153', '#FA5BA4', 0),
(384, 1, '2267', '#3E9D4D', 0),
(385, 1, '9302', '#D6F381', 0),
(386, 1, '5123', '#3C16F2', 0),
(387, 1, '1102', '#77BFA6', 0),
(388, 1, '3811', '#E98289', 0),
(389, 1, '5355', '#E7BFED', 0),
(390, 1, '1057', '#BBC04E', 0),
(391, 1, '7029', '#91BB55', 0),
(392, 1, '9626', '#C9FDA3', 0),
(393, 1, '8031', '#CE283B', 0),
(394, 1, '198', '#243172', 0),
(395, 1, '7973', '#71A665', 0),
(396, 1, '1062', '#03711D', 0),
(397, 1, '4068', '#8CFD81', 0),
(398, 1, '9749', '#82E025', 0),
(399, 1, '5997', '#114517', 0),
(400, 1, '8189', '#4A22D3', 0),
(401, 1, '7941', '#CE4517', 0),
(402, 1, '3476', '#573B51', 0),
(403, 1, '8939', '#9D6597', 0);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `produto_variacoes`
--
ALTER TABLE `produto_variacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de tabela `produto_variacoes`
--
ALTER TABLE `produto_variacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=404;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `produto_variacoes`
--
ALTER TABLE `produto_variacoes`
  ADD CONSTRAINT `produto_variacoes_ibfk_1` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
