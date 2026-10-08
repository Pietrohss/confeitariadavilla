# Prompt para o Claude Code — Atualização da API no servidor e testes (Confeitaria DaVilla)

Copie tudo abaixo da linha e cole no Claude Code, com o terminal aberto na raiz do projeto `tipi05-davilla`.

---

## Contexto

Estou no projeto **tipi05-davilla** (Laravel 13, PHP 8.3+, MySQL, Docker com os containers `php`, `nginx` e `mysql`; o site local abre em `http://localhost:8080`). O código Laravel fica em `src/`. O remoto é `origin` → `https://github.com/Pietrohss/confeitariadavilla.git`, branch `main`.

Preciso deixar o repositório pronto para seguir o guia **"Atualização da API no Servidor e Testes — Confeitaria DaVilla"** (turma TIPI05). Nesse guia, o servidor Plesk faz `git pull --ff-only origin main`, instala as dependências pelo PHP Composer do Plesk (opção **Instalar**, respeitando o `composer.lock`), registra a baseline das migrations no phpMyAdmin, roda `migrate --pretend` e depois `migrate --force`, e testa o login com Sanctum no Postman.

Dados do servidor (só para gerar arquivos e documentação, você **não** tem acesso a ele):
- Projeto: `/var/www/vhosts/smpsistema.com.br/xyzcode.tipi5.smpsistema.com.br/tipi05-davilla`
- Aplicação Laravel: `tipi05-davilla/src`
- PHP do servidor: `/opt/plesk/php/8.4/bin/php` (não há `composer` no SSH)
- Site: `https://xyzcode.tipi5.smpsistema.com.br/davilla/`
- API: `https://xyzcode.tipi5.smpsistema.com.br/davilla/api/v1`

Todos os comandos artisan locais devem rodar no Docker, por exemplo:
`docker compose exec --user "$(id -u):$(id -g)" php php artisan ...`

## Regras

- Antes de editar, leia os arquivos envolvidos e siga o estilo atual (respostas `{ success, data, message }`, colunas com sufixo `_cliente`, `_produto` etc.).
- **Nunca** rode `migrate:fresh`, `migrate:reset`, `migrate:rollback`, `db:wipe` nem nada que apague dados. Não altere o `.env`.
- Não mude o comportamento das rotas públicas que já funcionam (`status`, `banners`, `categorias`, `produtos`).
- Não faça `git push` sem me mostrar o resumo e pedir confirmação.
- Use sempre os nomes reais dos arquivos de migration que estão em `src/database/migrations/` (o guia cita `2026_09_24_113919_create_personal_access_tokens_table`, mas no projeto o arquivo é `2026_09_25_135000_create_personal_access_tokens_table.php`).

## O que fazer

### 1. Diagnóstico do Git (guia, seções 04 e 05)
- Rode `git status -sb`, `git log --oneline -5` e `git fetch origin` seguido de `git log --oneline origin/main..HEAD`.
- O que eu já sei: a branch local está **à frente** da `origin/main`, e os arquivos da autenticação **ainda não foram commitados**: `src/app/Models/Cliente.php`, `src/app/Http/Controllers/Api/V1/AuthController.php` e `ClienteController.php` estão como não rastreados, e `src/routes/api.php`, `src/bootstrap/app.php` e `src/resources/views/api/documentacao.blade.php` estão modificados. Sem isso no GitHub, o `git pull` do servidor não traz o login.
- Também aparecem como apagados vários arquivos `node_modules/...` da raiz e arquivos `*:Zone.Identifier` que estavam versionados por engano. Confirme comigo e inclua essa remoção num commit separado de limpeza (e adicione `/node_modules` da raiz ao `.gitignore` se fizer sentido).
- Configure `git config core.fileMode false` localmente, como no guia, para que mudança de permissão não apareça como alteração.

### 2. Corrigir a migration de favoritos antes de ela chegar ao servidor (guia, seções 10 e 11)
Em `src/database/migrations/2026_09_10_120536_create_tbl_favoritos_table.php`, a coluna `criado_em_favorito` está como `$table->integer('criado_em_favorito')->useCurrent();`. No MySQL, `INT DEFAULT CURRENT_TIMESTAMP` gera erro (*Invalid default value*), e o `migrate --force` do servidor falharia. Troque por `$table->timestamp('criado_em_favorito')->useCurrent();`. Confira também se `id_cliente` e `id_produto` têm o mesmo tipo das PKs referenciadas (`integer` em `tbl_clientes` e `tbl_produtos`), para as foreign keys não falharem. Mostre o SQL gerado com `php artisan migrate --pretend` (localmente) para eu conferir.

### 3. Conferir o Sanctum e as dependências (guia, seção 06)
- Confirme que `laravel/sanctum` está no `composer.json` **e** no `composer.lock` (o servidor vai usar **Instalar**, então o lock precisa estar commitado e coerente). Rode `composer validate` no container.
- Confirme que existe `src/vendor/laravel/sanctum` localmente e que `config/sanctum.php` está versionado.

### 4. Validar as migrations localmente (guia, seções 08 a 12)
- Rode `php artisan migrate:status` e me mostre o resultado.
- Se a baseline de `2026_09_08_*` estiver como **Pending** mas as tabelas já existirem no banco local, **não rode** `migrate`. Faça o mesmo que o guia: registre a baseline na tabela `migrations` (pode usar o arquivo da etapa 6) e confira de novo.
- Rode `migrate --pretend`: só podem aparecer `create_tbl_favoritos_table` e `create_personal_access_tokens_table`. Se aparecer qualquer outra tabela, pare e me avise.
- Só então rode `migrate` e confira com `SHOW TABLES LIKE 'tbl_favoritos'`, `SHOW TABLES LIKE 'personal_access_tokens'` e `DESCRIBE` das duas.

### 5. Validar rotas e autenticação localmente (guia, seções 13 a 18)
- `php artisan route:list --path=api/v1` deve mostrar exatamente estas rotas:
  ```
  POST   api/v1/auth/login
  POST   api/v1/auth/logout
  GET    api/v1/banners
  GET    api/v1/categorias
  GET    api/v1/categorias/{id}/produtos
  GET    api/v1/cliente
  PUT    api/v1/cliente
  PATCH  api/v1/cliente
  PUT    api/v1/cliente/senha
  GET    api/v1/produtos
  GET    api/v1/produtos/{slug}
  GET    api/v1/status
  ```
- Se `tbl_clientes` estiver vazia, crie um cliente de teste ATIVO com e-mail `cliente@davilla.com.br` e senha `12345678` gravada como **hash bcrypt** (`$2y$`, 60 caracteres), nunca em texto puro.
- Faça os 4 testes do guia com `curl` em `http://localhost:8080/api/v1` e me mostre status HTTP e corpo de cada um:
  1. `POST /auth/login` com `{"email":"cliente@davilla.com.br","senha":"12345678","device_name":"postman-producao"}` → **200**, `"message": "Login realizado com sucesso."` e `token`.
  2. `GET /cliente` com `Authorization: Bearer <token>` → **200** com os dados do cliente (sem `senha_cliente`).
  3. `POST /auth/logout` com o mesmo token → **200**, `"message": "Logout realizado com sucesso."`, e o token some de `personal_access_tokens`.
  4. `GET /cliente` com o token revogado → **401** em JSON. Teste também **sem** o header `Accept: application/json` e com `curl` sem seguir redirecionamentos: deve continuar 401 JSON, nunca 302 para o login do admin nem página HTML.
- Teste também os casos de erro: senha errada (401), e-mail inexistente (401, mesma mensagem) e cliente com status diferente de ATIVO (403).
- Se possível, transforme esses testes em um Feature Test em `src/tests/Feature/Api/AuthClienteTest.php` usando um banco de teste separado (SQLite em memória ou banco `davilla_test`), **sem** tocar no banco `davilla`. Se as migrations antigas não rodarem em SQLite, me avise em vez de forçar.

### 6. Gerar os arquivos de apoio para o servidor
Crie a pasta `deploy/` na raiz (versionada) com:

1. **`deploy/01-baseline-migrations.sql`** — para rodar no phpMyAdmin do servidor (guia, seção 09). Começa com o `SELECT id, migration, batch FROM migrations ORDER BY id;` e depois tem um `INSERT ... SELECT ... WHERE NOT EXISTS` no padrão do guia, batch `1`, para **cada** migration `2026_09_08_*` que existe em `src/database/migrations/` (as de `create_tbl_*` e as de `add_foreign_keys_to_*`), gerado a partir da lista real de arquivos. **Não** inclua `create_tbl_favoritos_table` nem `create_personal_access_tokens_table` — elas precisam ficar Pending para o `migrate` criar.
2. **`deploy/02-cliente-teste.sql`** — o comando PHP para gerar o hash (`/opt/plesk/php/8.4/bin/php -r "echo password_hash('12345678', PASSWORD_BCRYPT), PHP_EOL;"`), um `INSERT` de cliente de teste com todas as colunas obrigatórias de `tbl_clientes` e o placeholder `COLE_AQUI_O_HASH`, e o `SELECT` de conferência do guia (`LEFT(senha_cliente, 4)`, `LENGTH(senha_cliente)`, `status_cliente`).
3. **`deploy/03-validacao.sql`** — os `SHOW TABLES LIKE` e `DESCRIBE` da seção 12.
4. **`deploy/atualizar-servidor.sh`** — o fluxo da seção 19 em script para rodar no SSH do servidor, com `set -e`, usando `/opt/plesk/php/8.4/bin/php`, parando para confirmação antes do `migrate --force` e avisando para rodar o **PHP Composer → Instalar** no Plesk quando `composer.json`/`composer.lock` mudarem no pull (compare com `git diff --name-only ORIG_HEAD HEAD`). Ao final, `optimize:clear` e `chmod -R 775 storage bootstrap/cache`.
5. **`deploy/DaVilla-API.postman_collection.json`** — coleção Postman v2.1 com variável `base_url` = `https://xyzcode.tipi5.smpsistema.com.br/davilla/api/v1`, header `Accept: application/json` em todas as requisições e as requisições: Status, Banners, Categorias, Produtos, Login (com script de teste que salva o `token` numa variável da coleção), Cliente (Bearer `{{token}}`), Logout e "Cliente após logout" (teste esperando 401). Cada requisição com um teste do status esperado.
6. **`deploy/README.md`** — checklist curto, em português, na ordem do guia (seções 03 a 18), apontando qual arquivo usar em cada etapa e o resultado esperado. Inclua a dica do Postman: se aparecer HTML com status 200, desligar **Settings → Automatically follow redirects**.

### 7. Commits
Separe em commits pequenos, com mensagens em português, por exemplo:
1. `Remove node_modules e Zone.Identifier versionados por engano`
2. `Autenticação do cliente com Sanctum (model, controllers e rotas)`
3. `Corrige tipo de criado_em_favorito na migration de favoritos`
4. `Testes da API de autenticação`
5. `Arquivos de apoio para atualização no servidor (deploy/)`

Depois me mostre `git log --oneline origin/main..HEAD` e `git status`, e **pergunte antes** de rodar `git push origin main`.

## Entrega

No final, me passe um resumo com:
- o que estava faltando ou errado e o que foi corrigido;
- a saída de `migrate:status`, `migrate --pretend` e `route:list --path=api/v1`;
- o resultado (status + corpo) dos 4 testes do guia e dos casos de erro;
- a lista dos arquivos criados em `deploy/`;
- os passos que eu ainda preciso fazer manualmente no servidor (SSH, Plesk Composer, phpMyAdmin e Postman), na ordem.
