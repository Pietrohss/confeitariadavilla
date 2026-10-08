# Atualização da API no servidor — checklist

Arquivos de apoio para o guia **"Atualização da API no Servidor e Testes — Confeitaria DaVilla"** (TIPI05).

- Projeto: `/var/www/vhosts/smpsistema.com.br/xyzcode.tipi5.smpsistema.com.br/tipi05-davilla`
- Laravel: `tipi05-davilla/src`
- PHP: `/opt/plesk/php/8.4/bin/php` (não há `composer` no SSH: usar o PHP Composer do Plesk)
- API: `https://xyzcode.tipi5.smpsistema.com.br/davilla/api/v1`

> O script `atualizar-servidor.sh` faz as etapas de SSH (04 a 08 e 10 a 13) em sequência. As etapas de phpMyAdmin, Plesk e Postman são manuais.

| # | Etapa | Onde | Arquivo / comando | Resultado esperado |
|---|---|---|---|---|
| 03 | Acessar o servidor | SSH | `cd .../tipi05-davilla` | Prompt na pasta do projeto |
| 04 | Diagnóstico do Git | SSH | `git config core.fileMode false` e `git status -sb` | `## main...origin/main`, sem alterações locais |
| 05 | Atualizar o código | SSH | `git pull --ff-only origin main` | Fast-forward, sem conflito |
| 06 | Dependências (Sanctum) | Plesk | **PHP Composer → Instalar** (pasta `src`) | `src/vendor/laravel/sanctum` existe |
| 07 | Conferir o Laravel | SSH | `/opt/plesk/php/8.4/bin/php artisan --version` | Laravel 13.x |
| 08 | Status das migrations | SSH | `php artisan migrate:status` | Só favoritos e personal_access_tokens Pending |
| 09 | Baseline das migrations | phpMyAdmin | `01-baseline-migrations.sql` | As 16 migrations `2026_09_08_*` com batch 1 |
| 10 | Conferir de novo | SSH | `php artisan migrate:status` | Todas `2026_09_08_*` como Ran |
| 11 | Simular | SSH | `php artisan migrate --pretend` | Só `create table tbl_favoritos` e `create table personal_access_tokens` |
| 12 | Migrar e validar | SSH + phpMyAdmin | `php artisan migrate --force` e `03-validacao.sql` | As duas tabelas existem; `criado_em_favorito` é `timestamp` |
| 13 | Rotas | SSH | `php artisan route:list --path=api/v1` | 12 rotas, incluindo `auth/login`, `auth/logout` e `cliente` |
| 14 | Cliente de teste | SSH + phpMyAdmin | `02-cliente-teste.sql` (gerar o hash no SSH) | `$2y$`, 60 caracteres, `ATIVO` |
| 15 | Login | Postman | `DaVilla-API.postman_collection.json` → **Login** | 200, `Login realizado com sucesso.` e `token` |
| 16 | Dados do cliente | Postman | **Cliente** (Bearer `{{token}}`) | 200, sem `senha_cliente` |
| 17 | Logout | Postman | **Logout** | 200, `Logout realizado com sucesso.` |
| 18 | Token revogado | Postman | **Cliente após logout** | 401 em JSON |

No final do script: `optimize:clear` e `chmod -R 775 storage bootstrap/cache`.

## Dicas

- **Postman mostrando HTML com status 200**: desligue **Settings → Automatically follow redirects**. A API responde 401 em JSON; o HTML é o Postman seguindo um redirecionamento.
- Todas as requisições da coleção já mandam `Accept: application/json`. A variável `base_url` fica nas variáveis da coleção.
- **Nunca** rode `migrate:fresh`, `migrate:reset`, `migrate:rollback` ou `db:wipe` no servidor: apagam os dados.
- Se o `migrate --pretend` mostrar qualquer tabela além de `tbl_favoritos` e `personal_access_tokens`, pare e confira a baseline (etapa 09).
