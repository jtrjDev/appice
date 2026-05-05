-- Importação de produtos do sistema antigo para a tabela nova `produtos`
-- Origem: tabela antiga `produto`
-- Mapeamento principal: valor -> preco
-- ATENÇÃO 1: antes de rodar, confirme se existem categorias com estes IDs:
-- 46, 49, 50, 55, 56, 57
-- SELECT id, nome FROM categorias WHERE id IN (46, 49, 50, 55, 56, 57);
--
-- ATENÇÃO 2: a tabela nova tem UNIQUE em `codigo`.
-- Estes códigos estavam repetidos no banco antigo e foram importados como NULL para evitar erro:
-- 1 (2x), 2 (2x), 3 (2x), 4 (2x), 6 (2x), 13 (2x), 16 (2x), 17 (2x), 42 (2x)
--
-- Se você quiser manter esses códigos, ajuste manualmente para códigos únicos antes de rodar.

START TRANSACTION;

INSERT INTO `produtos` (
  `id`, `nome`, `slug`, `codigo`, `categoria_id`, `preco`, `preco_promocional`, `descricao`, `imagem`, `icone`, `estoque`, `estoque_minimo`, `ativo`, `destaque`, `created_at`, `updated_at`, `deleted_at`, `tipo_venda`, `permite_meio`, `preco_meio`, `tamanhos`, `adicionais`, `ncm`, `cfop`, `cest`, `origem`, `aliq_icms`, `aliq_ipi`, `aliq_pis`, `aliq_cofins`, `cst_icms`, `cst_pis`, `cst_cofins`, `unidade_medida`
) VALUES
(62, 'Picolé de leite', 'picole-de-leite', NULL, 49, 2.50, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-10-21 09:53:54', '2025-09-26 19:39:33', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, '5101', NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '49', '49', 'UN'),
(63, 'Picolé de fruta promocional', 'picole-de-fruta-promocional', NULL, 49, 1.65, NULL, NULL, 'picole-de-fruta-promocional.jpg', NULL, 0, 0, 1, 0, '2024-10-21 09:54:13', '2025-09-27 19:05:27', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, '5101', NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '49', '49', 'UN'),
(64, 'Picolé premium', 'picole-premium', NULL, 49, 8.00, NULL, NULL, 'picole-premium.jpg', NULL, 0, 0, 1, 0, '2024-10-21 09:54:34', '2025-09-26 19:36:02', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, '5101', NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '49', '49', 'UN'),
(67, 'Self service', 'self-service', NULL, 46, 59.99, NULL, NULL, 'self-service.jpg', NULL, 0, 0, 1, 0, '2024-11-06 16:16:29', '2025-09-26 18:52:35', NULL, 'peso', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '41', '49', '49', 'KG'),
(68, 'picole leite Promocional', 'picole-leite-promocional', NULL, 49, 1.80, NULL, NULL, 'picole-leite-promocional.jpg', NULL, 0, 0, 1, 0, '2024-11-12 14:07:07', '2025-09-27 19:04:47', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, '5101', NULL, '0', 0.00, 0.00, 0.00, 0.00, '20', '05', '04', 'UN'),
(69, 'Pote 1 Litro', 'pote-1-litro', NULL, 49, 18.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-12 14:11:25', '2025-09-26 19:34:22', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '40', '05', '04', 'UN'),
(70, 'SORVETE POTE PROMOCIONAL', 'sorvete-pote-promocional', '60', 49, 16.67, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-14 09:29:47', '2025-09-26 18:54:00', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, '5105', NULL, '0', 0.00, 0.00, 0.00, 0.00, '40', '49', '49', 'UN'),
(71, 'IceTortas individuais', 'icetortas-individuais', '10', 50, 12.00, NULL, NULL, 'icetortas-individuais.jpg', NULL, 0, 0, 1, 0, '2024-11-19 10:49:14', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(72, 'IceTortas médias', 'icetortas-medias', '19', 50, 60.00, NULL, NULL, 'icetortas-medias.jpg', NULL, 0, 0, 1, 0, '2024-11-19 10:53:19', '2025-10-05 11:45:14', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(73, 'IceTortas médias sabores especiais', 'icetortas-medias-sabores-especiais', '20', 50, 65.00, NULL, NULL, 'icetortas-medias-sabores-especiais.jpg', NULL, 0, 0, 1, 0, '2024-11-19 10:54:13', '2025-09-28 15:15:40', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(74, 'IceTortas grandes', 'icetortas-grandes', NULL, 50, 120.00, NULL, NULL, 'icetortas-grandes.jpg', NULL, 0, 0, 1, 0, '2024-11-19 10:55:36', '2025-10-05 11:45:48', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(75, 'IceTortas grandes sabores especiais', 'icetortas-grandes-sabores-especiais', '14', 50, 140.00, NULL, NULL, 'icetortas-grandes-sabores-especiais.jpg', NULL, 0, 0, 1, 0, '2024-11-19 11:13:36', '2025-10-05 11:46:12', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(77, 'Pote 1 Litro Promocional', 'pote-1-litro-promocional', NULL, 49, 16.66, NULL, NULL, 'pote-1-litro-promocional.jpg', NULL, 0, 0, 1, 0, '2024-11-19 11:19:37', '2025-09-27 15:55:37', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(78, 'balas', 'balas', NULL, 55, 0.20, NULL, NULL, 'balas.jpg', NULL, 0, 0, 1, 0, '2024-11-19 19:26:42', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(79, 'Fini', 'fini', NULL, 55, 3.00, NULL, NULL, 'fini.jpg', NULL, 0, 0, 1, 0, '2024-11-19 19:30:04', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(80, 'Saquinho Fini', 'saquinho-fini', NULL, 55, 10.00, NULL, NULL, 'saquinho-fini.jpg', NULL, 0, 0, 1, 0, '2024-11-19 19:31:04', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(81, 'Paçoca', 'pacoca', NULL, 55, 2.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 19:36:04', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(82, 'agua', 'agua', '5', 46, 3.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 19:36:28', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(83, 'Picolé fruta', 'picole-fruta', NULL, 49, 2.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 19:37:59', '2025-09-26 19:07:11', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(84, 'Venda avulsa', 'venda-avulsa', '999', 46, 1.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 20:01:13', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(85, 'Cone', 'cone', NULL, 49, 9.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 20:04:02', '2025-09-15 18:11:51', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(86, 'Cobertura', 'cobertura', '25', 56, 14.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 20:04:54', '2025-09-26 18:51:26', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(87, 'Cascao', 'cascao', NULL, 49, 10.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 20:05:19', '2025-04-21 13:42:57', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(88, 'Cestinha', 'cestinha', NULL, 49, 5.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 20:06:05', '2025-04-21 13:42:35', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(89, 'Look', 'look', '28', 49, 4.50, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 20:06:23', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(90, 'Leite Condensado', 'leite-condensado', '29', 56, 10.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 20:07:10', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(91, 'Complemento para sorvete', 'complemento-para-sorvete', '37', 49, 6.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 20:07:46', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(92, 'Pote Açai', 'pote-acai', '012', 46, 35.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 20:35:43', '2025-04-21 21:38:46', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(93, 'Pote Açaí 2L', 'pote-acai-2l', '010', 46, 50.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 20:36:25', '2025-09-15 19:52:08', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(94, 'Cocal 2LT', 'cocal-2lt', '73', 46, 14.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 20:38:34', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(95, 'Bag personalizadas', 'bag-personalizadas', '100', 57, 39.90, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 21:05:33', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(96, 'Copo personalizado', 'copo-personalizado', '101', 57, 10.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 21:05:54', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(99, 'refri lata', 'refri-lata', '52', 46, 6.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 21:09:53', '2025-09-26 18:50:12', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(102, 'Fanta 2LT', 'fanta-2lt', '55', 46, 13.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 21:11:12', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(103, 'Cerveja Sol Long Neck', 'cerveja-sol-long-neck', '56', 46, 10.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 21:12:35', '2025-09-26 18:49:09', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(104, 'Cerveja Heineken Long Neck', 'cerveja-heineken-long-neck', '57', 46, 10.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 21:13:11', '2025-09-15 18:10:29', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(105, 'Cerveja Heineken Lata', 'cerveja-heineken-lata', '58', 46, 8.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 21:13:59', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(106, 'Cerveja Amstel lager Lata 350 ml', 'cerveja-amstel-lager-lata-350-ml', '59', 46, 7.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 21:14:37', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(107, 'Coca cola 200 ml', 'coca-cola-200-ml', '81', 46, 3.50, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 21:15:49', '2025-09-15 18:09:09', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(108, 'Coca Cola zero 200 ml', 'coca-cola-zero-200-ml', '82', 46, 3.50, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 21:16:12', '2025-09-15 18:09:47', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(109, 'Fanta 200 ml', 'fanta-200-ml', '83', 46, 3.50, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 21:16:37', '2025-09-15 18:09:35', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(110, 'Mini oreo', 'mini-oreo', '84', 55, 4.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 21:17:59', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(111, 'Bombom Sonho de valsa', 'bombom-sonho-de-valsa', '85', 55, 2.50, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 21:18:24', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(112, 'bombom ouro branco', 'bombom-ouro-branco', '86', 55, 2.50, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 21:19:06', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(113, 'Pote de Açai 1 LT', 'pote-de-acai-1-lt', '18', 49, 30.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 21:19:47', '2025-09-26 19:04:38', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(114, 'Pirulito', 'pirulito', '89', 55, 2.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 21:20:10', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(115, 'Barra chocolate', 'barra-chocolate', '90', 55, 10.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-19 21:20:47', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(116, 'pote de 2 litros', 'pote-de-2-litros', '7', 49, 31.90, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-20 13:41:50', '2025-09-26 19:07:58', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, '5105', NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(117, 'caixa de 5 litros tradicional', 'caixa-de-5-litros-tradicional', NULL, 49, 75.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-11-28 16:51:12', '2025-09-26 19:03:37', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'CX'),
(118, 'bubbaloo', 'bubbaloo', NULL, 55, 0.50, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2024-12-01 16:10:24', '2025-09-15 18:06:13', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(119, 'skimo', 'skimo', '11', 49, 6.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2025-04-21 13:54:33', '2025-09-26 19:03:09', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(120, 'torta individual', 'torta-individual', '9', 50, 12.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2025-04-22 20:48:20', '2025-09-26 19:01:10', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(121, 'furioso', 'furioso', '200', 46, 5.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2025-08-25 14:34:19', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(123, 'PUSH', 'push', '203', 55, 0.30, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2025-09-09 20:21:18', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(124, 'agua sab.', 'agua-sab', '12', 46, 5.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2025-09-15 15:33:55', '2025-09-26 19:02:17', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(126, 'picole caseiro', 'picole-caseiro', '8', 49, 20.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2025-09-15 16:19:06', '2025-09-26 19:01:48', NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN'),
(127, 'caixa de 5 litros sabores trufados', 'caixa-de-5-litros-sabores-trufados', NULL, 49, 85.00, NULL, NULL, NULL, NULL, 0, 0, 1, 0, '2025-12-13 22:46:26', NULL, NULL, 'unidade', 0, NULL, NULL, NULL, NULL, NULL, NULL, '0', 0.00, 0.00, 0.00, 0.00, '102', '07', '07', 'UN')
ON DUPLICATE KEY UPDATE
  `nome` = VALUES(`nome`),
  `categoria_id` = VALUES(`categoria_id`),
  `preco` = VALUES(`preco`),
  `descricao` = VALUES(`descricao`),
  `imagem` = VALUES(`imagem`),
  `ativo` = VALUES(`ativo`),
  `destaque` = VALUES(`destaque`),
  `updated_at` = VALUES(`updated_at`),
  `tipo_venda` = VALUES(`tipo_venda`),
  `ncm` = VALUES(`ncm`),
  `cfop` = VALUES(`cfop`),
  `origem` = VALUES(`origem`),
  `aliq_icms` = VALUES(`aliq_icms`),
  `aliq_ipi` = VALUES(`aliq_ipi`),
  `aliq_pis` = VALUES(`aliq_pis`),
  `aliq_cofins` = VALUES(`aliq_cofins`),
  `cst_icms` = VALUES(`cst_icms`),
  `cst_pis` = VALUES(`cst_pis`),
  `cst_cofins` = VALUES(`cst_cofins`),
  `unidade_medida` = VALUES(`unidade_medida`);

COMMIT;

-- Depois confira:
-- SELECT id, nome, codigo, categoria_id, preco, tipo_venda, unidade_medida, cfop, cst_icms, cst_pis, cst_cofins
-- FROM produtos
-- WHERE id BETWEEN 62 AND 127
-- ORDER BY id;
