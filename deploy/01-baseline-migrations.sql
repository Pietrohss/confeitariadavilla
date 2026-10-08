-- =====================================================================
-- 01 - Baseline das migrations (guia, seção 09) - rodar no phpMyAdmin
-- =====================================================================
-- As tabelas tbl_* já existem no banco do servidor, mas o Laravel não
-- sabe disso. Estes INSERTs registram as migrations 2026_09_08_* como
-- já executadas (batch 1), para o migrate não tentar recriá-las.
--
-- NÃO registra (precisam ficar Pending para o migrate criar):
--   2026_09_10_120536_create_tbl_favoritos_table
--   2026_09_25_135000_create_personal_access_tokens_table
--
-- Pode rodar mais de uma vez: o WHERE NOT EXISTS evita duplicar.
-- =====================================================================

-- Antes: o que já está registrado
SELECT id, migration, batch FROM migrations ORDER BY id;


-- create_tbl_*
INSERT INTO migrations (migration, batch)
SELECT '2026_09_08_143451_create_tbl_banner_table', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_09_08_143451_create_tbl_banner_table');

INSERT INTO migrations (migration, batch)
SELECT '2026_09_08_143451_create_tbl_categoria_table', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_09_08_143451_create_tbl_categoria_table');

INSERT INTO migrations (migration, batch)
SELECT '2026_09_08_143451_create_tbl_clientes_table', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_09_08_143451_create_tbl_clientes_table');

INSERT INTO migrations (migration, batch)
SELECT '2026_09_08_143451_create_tbl_contato_table', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_09_08_143451_create_tbl_contato_table');

INSERT INTO migrations (migration, batch)
SELECT '2026_09_08_143451_create_tbl_controle_materia_prima_table', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_09_08_143451_create_tbl_controle_materia_prima_table');

INSERT INTO migrations (migration, batch)
SELECT '2026_09_08_143451_create_tbl_fornecedores_table', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_09_08_143451_create_tbl_fornecedores_table');

INSERT INTO migrations (migration, batch)
SELECT '2026_09_08_143451_create_tbl_itens_venda_table', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_09_08_143451_create_tbl_itens_venda_table');

INSERT INTO migrations (migration, batch)
SELECT '2026_09_08_143451_create_tbl_materia_prima_table', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_09_08_143451_create_tbl_materia_prima_table');

INSERT INTO migrations (migration, batch)
SELECT '2026_09_08_143451_create_tbl_produtos_table', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_09_08_143451_create_tbl_produtos_table');

INSERT INTO migrations (migration, batch)
SELECT '2026_09_08_143451_create_tbl_usuarios_table', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_09_08_143451_create_tbl_usuarios_table');

INSERT INTO migrations (migration, batch)
SELECT '2026_09_08_143451_create_tbl_vendas_table', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_09_08_143451_create_tbl_vendas_table');


-- add_foreign_keys_to_*
INSERT INTO migrations (migration, batch)
SELECT '2026_09_08_143452_add_foreign_keys_to_tbl_controle_materia_prima_table', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_09_08_143452_add_foreign_keys_to_tbl_controle_materia_prima_table');

INSERT INTO migrations (migration, batch)
SELECT '2026_09_08_143452_add_foreign_keys_to_tbl_itens_venda_table', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_09_08_143452_add_foreign_keys_to_tbl_itens_venda_table');

INSERT INTO migrations (migration, batch)
SELECT '2026_09_08_143452_add_foreign_keys_to_tbl_materia_prima_table', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_09_08_143452_add_foreign_keys_to_tbl_materia_prima_table');

INSERT INTO migrations (migration, batch)
SELECT '2026_09_08_143452_add_foreign_keys_to_tbl_produtos_table', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_09_08_143452_add_foreign_keys_to_tbl_produtos_table');

INSERT INTO migrations (migration, batch)
SELECT '2026_09_08_143452_add_foreign_keys_to_tbl_vendas_table', 1 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM migrations WHERE migration = '2026_09_08_143452_add_foreign_keys_to_tbl_vendas_table');


-- Depois: devem aparecer as 16 migrations 2026_09_08_* acima
SELECT id, migration, batch FROM migrations ORDER BY id;
