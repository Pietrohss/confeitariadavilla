#!/usr/bin/env bash
# =====================================================================
# Atualização da API no servidor (guia, seção 19)
# Rodar no SSH do servidor:  bash deploy/atualizar-servidor.sh
# =====================================================================
set -e

PROJETO=/var/www/vhosts/smpsistema.com.br/xyzcode.tipi5.smpsistema.com.br/tipi05-davilla
PHP=/opt/plesk/php/8.4/bin/php

cd "$PROJETO"

echo "== 1. Git: estado atual"
git config core.fileMode false
git status -sb
git log --oneline -3

echo
echo "== 2. Git: pull"
ANTES=$(git rev-parse HEAD)
git pull --ff-only origin main
git log --oneline -3

# Mesmo resultado de git diff --name-only ORIG_HEAD HEAD, mas sem pegar
# um ORIG_HEAD antigo quando o pull não traz nada
ALTERADOS=$(git diff --name-only "$ANTES" HEAD)

if echo "$ALTERADOS" | grep -qE '^src/composer\.(json|lock)$'; then
    echo
    echo "!! composer.json/composer.lock mudaram neste pull."
    echo "!! No Plesk: Sites e Domínios > PHP Composer > Instalar"
    echo "!! (diretório src/, respeita o composer.lock)."
    read -r -p "Depois de rodar o Instalar no Plesk, aperte ENTER para continuar... "
fi

cd src

echo
echo "== 3. Dependências"
$PHP artisan --version
if [ ! -d vendor/laravel/sanctum ]; then
    echo "!! vendor/laravel/sanctum não existe. Rode o PHP Composer > Instalar no Plesk e execute o script de novo."
    exit 1
fi

echo
echo "== 4. Migrations"
STATUS=$($PHP artisan migrate:status)
echo "$STATUS"

if echo "$STATUS" | grep '2026_09_08_' | grep -q 'Pending'; then
    echo
    echo "!! Há migrations 2026_09_08_* como Pending: as tabelas seriam recriadas."
    echo "!! Rode deploy/01-baseline-migrations.sql no phpMyAdmin e execute o script de novo."
    exit 1
fi

echo
echo "Só podem aparecer no pretend:"
echo "  2026_09_10_120536_create_tbl_favoritos_table"
echo "  2026_09_25_135000_create_personal_access_tokens_table"
echo
$PHP artisan migrate --pretend

echo
read -r -p "O SQL acima está correto (só tbl_favoritos e personal_access_tokens)? Digite SIM para rodar migrate --force: " RESPOSTA
if [ "$RESPOSTA" != "SIM" ]; then
    echo "Cancelado. Nada foi migrado."
    exit 1
fi

$PHP artisan migrate --force
$PHP artisan migrate:status

echo
echo "== 5. Rotas da API"
$PHP artisan route:list --path=api/v1

echo
echo "== 6. Cache e permissões"
$PHP artisan optimize:clear
chmod -R 775 storage bootstrap/cache

echo
echo "Pronto. Próximos passos:"
echo "  - phpMyAdmin: deploy/03-validacao.sql (e deploy/02-cliente-teste.sql se ainda não houver cliente de teste)"
echo "  - Postman: importar deploy/DaVilla-API.postman_collection.json e rodar a coleção"
