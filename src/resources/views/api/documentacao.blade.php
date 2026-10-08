<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>API — Confeitaria DaVilla</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
            margin: 0;
        }

        header {
            background: #691650;
            color: white;
            padding: 30px;
        }

        main {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .endpoint {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
        }

        .method {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            background: #198754;
            color: white;
            font-weight: bold;
        }

        .method.post {
            background: #0d6efd;
        }

        .method.put {
            background: #fd7e14;
        }

        .method.patch {
            background: #6f42c1;
        }

        .acesso {
            display: inline-block;
            margin-left: 8px;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 13px;
            background: #e9ecef;
        }

        .acesso.token {
            background: #691650;
            color: white;
        }

        code {
            background: #eee;
            padding: 4px 8px;
            border-radius: 4px;
        }

        pre {
            background: #1e1e1e;
            color: #eee;
            padding: 16px;
            overflow-x: auto;
            border-radius: 6px;
        }
    </style>
</head>

<body>

<header>
    <h1>API — Confeitaria DaVilla</h1>

    <p>
        Documentação da API utilizada pelo aplicativo.
    </p>
</header>

<main>

    <h2>Informações</h2>

    <p>
        Versão:
        <strong>v1</strong>
    </p>

    <p>
        Base da API:
        <code>{{ url('/api/v1') }}</code>
    </p>


    <h2>Endpoints</h2>


    <div class="endpoint">

        <span class="method">GET</span>

        <code>/api/v1/status</code>

        <p>
            Verifica se a API está funcionando.
        </p>

    </div>


    <div class="endpoint">

        <span class="method">GET</span>

        <code>/api/v1/banners</code>

        <p>
            Retorna os banners ativos.
        </p>

    </div>


    <div class="endpoint">

        <span class="method">GET</span>

        <code>/api/v1/categorias</code>

        <p>
            Retorna as categorias ativas.
        </p>

    </div>


    <div class="endpoint">

        <span class="method">GET</span>

        <code>/api/v1/categorias/{id}/produtos</code>

        <p>
            Retorna os produtos ativos de uma categoria.
        </p>

    </div>


    <div class="endpoint">

        <span class="method">GET</span>

        <code>/api/v1/produtos</code>

        <p>
            Retorna todos os produtos ativos.
        </p>

    </div>


    <div class="endpoint">

        <span class="method">GET</span>

        <code>/api/v1/produtos/{slug}</code>

        <p>
            Retorna os detalhes de um produto.
        </p>

    </div>


    <h2>Exemplo de resposta</h2>

    <pre>
{
    "success": true,
    "data": {
        "aplicacao": "Confeitaria DaVilla",
        "api": "v1",
        "status": "online"
    }
}
    </pre>


    <h2>Autenticação do cliente</h2>

    <p>
        O login devolve um token (Laravel Sanctum). Nas rotas marcadas com
        <span class="acesso token">Token</span> envie o cabeçalho:
    </p>

    <pre>
Authorization: Bearer 1|SEU_TOKEN
Accept: application/json
    </pre>

    <p>
        Sem token (ou com token inválido) a resposta é <strong>401</strong>:
    </p>

    <pre>
{
    "success": false,
    "message": "Não autenticado."
}
    </pre>


    <div class="endpoint">

        <span class="method post">POST</span>

        <code>/api/v1/auth/login</code>

        <span class="acesso">Público</span>

        <p>
            Autentica o cliente por e-mail e senha e gera o token.
            <code>device_name</code> é opcional (padrão <code>app-mobile</code>);
            um novo login no mesmo dispositivo invalida o token anterior.
        </p>

        <p>Corpo:</p>

        <pre>
{
    "email": "cliente@davilla.com.br",
    "senha": "12345678",
    "device_name": "app-mobile"
}
        </pre>

        <p>Resposta (200):</p>

        <pre>
{
    "success": true,
    "message": "Login realizado com sucesso.",
    "data": {
        "token": "1|SEU_TOKEN",
        "cliente": {
            "id_cliente": 1,
            "nome_cliente": "Cliente Teste",
            "email_cliente": "cliente@davilla.com.br",
            "telefone_cliente": "(11)99999-9999"
        }
    }
}
        </pre>

        <p>
            Erros: <strong>401</strong> "E-mail ou senha inválidos." ·
            <strong>403</strong> "Cliente inativo." ·
            <strong>422</strong> campos inválidos.
        </p>

    </div>


    <div class="endpoint">

        <span class="method">GET</span>

        <code>/api/v1/cliente</code>

        <span class="acesso token">Token</span>

        <p>
            Retorna o perfil do cliente autenticado (a senha nunca é enviada).
        </p>

        <p>Resposta (200):</p>

        <pre>
{
    "success": true,
    "data": {
        "id_cliente": 1,
        "nome_cliente": "Cliente Teste",
        "tipo_cliente": "PF",
        "cpf_cnpj_cliente": "000.000.000-00",
        "data_nasc_cliente": "2000-01-01T00:00:00.000000Z",
        "email_cliente": "cliente@davilla.com.br",
        "telefone_cliente": "(11)99999-9999",
        "status_cliente": "ATIVO",
        ...
    }
}
        </pre>

    </div>


    <div class="endpoint">

        <span class="method put">PUT</span>

        <code>/api/v1/cliente</code>

        <span class="acesso token">Token</span>

        <p>
            Atualiza os dados do perfil. Todos os campos abaixo são obrigatórios.
            E-mail e CPF/CNPJ não podem pertencer a outro cliente (422).
            Senha e status não são alterados por esta rota.
        </p>

        <p>Corpo:</p>

        <pre>
{
    "nome_cliente": "Cliente Teste",
    "tipo_cliente": "PF",
    "cpf_cnpj_cliente": "000.000.000-00",
    "data_nasc_cliente": "2000-01-01",
    "email_cliente": "cliente@davilla.com.br",
    "telefone_cliente": "(11)99999-9999"
}
        </pre>

        <p>Resposta (200):</p>

        <pre>
{
    "success": true,
    "message": "Perfil atualizado com sucesso.",
    "data": { "id_cliente": 1, "nome_cliente": "Cliente Teste", ... }
}
        </pre>

    </div>


    <div class="endpoint">

        <span class="method patch">PATCH</span>

        <code>/api/v1/cliente</code>

        <span class="acesso token">Token</span>

        <p>
            Atualização parcial do perfil: envie só os campos que mudaram
            (mesmas regras do PUT).
        </p>

        <p>Corpo:</p>

        <pre>
{
    "telefone_cliente": "(11)98888-7777"
}
        </pre>

        <p>Resposta (200): igual à do PUT.</p>

    </div>


    <div class="endpoint">

        <span class="method put">PUT</span>

        <code>/api/v1/cliente/senha</code>

        <span class="acesso token">Token</span>

        <p>
            Altera a senha. A nova senha precisa ter no mínimo 8 caracteres
            e ser repetida em <code>nova_senha_confirmation</code>.
        </p>

        <p>Corpo:</p>

        <pre>
{
    "senha_atual": "12345678",
    "nova_senha": "novaSenha123",
    "nova_senha_confirmation": "novaSenha123"
}
        </pre>

        <p>Resposta (200):</p>

        <pre>
{
    "success": true,
    "message": "Senha atualizada com sucesso."
}
        </pre>

        <p>
            Erro: <strong>422</strong> "Senha atual incorreta."
        </p>

    </div>


    <div class="endpoint">

        <span class="method post">POST</span>

        <code>/api/v1/auth/logout</code>

        <span class="acesso token">Token</span>

        <p>
            Invalida o token usado na requisição. Depois disso ele passa a receber 401.
        </p>

        <p>Resposta (200):</p>

        <pre>
{
    "success": true,
    "message": "Logout realizado com sucesso."
}
        </pre>

    </div>


    <h2>Próximas etapas</h2>

    <p>
        A API será expandida para endereços, favoritos,
        depoimentos, cupons e pedidos.
    </p>

</main>

</body>
</html>