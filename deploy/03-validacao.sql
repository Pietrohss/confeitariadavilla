-- =====================================================================
-- 03 - Validação depois do migrate --force (guia, seção 12)
-- =====================================================================

-- As duas tabelas novas devem existir
SHOW TABLES LIKE 'tbl_favoritos';
SHOW TABLES LIKE 'personal_access_tokens';

-- tbl_favoritos: criado_em_favorito deve ser timestamp, default CURRENT_TIMESTAMP
DESCRIBE tbl_favoritos;

-- personal_access_tokens: id, tokenable_type, tokenable_id, name, token, ...
DESCRIBE personal_access_tokens;

-- As duas migrations novas devem aparecer com o batch mais alto
SELECT id, migration, batch FROM migrations ORDER BY id;
