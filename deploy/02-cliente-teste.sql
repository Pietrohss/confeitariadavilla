-- =====================================================================
-- 02 - Cliente de teste para o login da API - rodar no phpMyAdmin
-- =====================================================================
-- 1) No SSH do servidor, gere o hash bcrypt da senha 12345678:
--
--    /opt/plesk/php/8.4/bin/php -r "echo password_hash('12345678', PASSWORD_BCRYPT), PHP_EOL;"
--
--    A saída começa com $2y$ e tem 60 caracteres.
--
-- 2) Troque COLE_AQUI_O_HASH abaixo pela saída do comando e rode o INSERT.
--    Nunca grave a senha em texto puro.
--
-- Se o e-mail ou o CPF já existirem, o INSERT falha (são UNIQUE):
-- nesse caso confira o cliente existente com o SELECT do final.
-- =====================================================================

INSERT INTO tbl_clientes (
    nome_cliente,
    tipo_cliente,
    cpf_cnpj_cliente,
    data_nasc_cliente,
    endereco_cliente,
    numero_cliente,
    complemento_cliente,
    bairro_cliente,
    cidade_cliente,
    uf_cliente,
    cep_cliente,
    email_cliente,
    senha_cliente,
    telefone_cliente,
    foto_cliente,
    status_cliente
) VALUES (
    'Cliente Teste',
    'PF',
    '111.111.111-11',
    '2000-01-01',
    'Rua Teste',
    '100',
    NULL,
    'Centro',
    'Sao Paulo',
    'SP',
    '01000-000',
    'cliente@davilla.com.br',
    'COLE_AQUI_O_HASH',
    '(11)99999-9999',
    '',
    'ATIVO'
);


-- Conferência: esperado $2y$ / 60 / ATIVO
SELECT
    id_cliente,
    email_cliente,
    LEFT(senha_cliente, 4) AS inicio_hash,
    LENGTH(senha_cliente) AS tamanho_hash,
    status_cliente
FROM tbl_clientes
WHERE email_cliente = 'cliente@davilla.com.br';
