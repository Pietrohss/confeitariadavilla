<?php

namespace Tests\Feature\Api;

use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

// Roda no SQLite em memória (phpunit.xml): não toca no banco davilla
class AuthClienteTest extends TestCase
{
    use RefreshDatabase;

    private function criarCliente(string $status = 'ATIVO', string $email = 'cliente@davilla.com.br'): Cliente
    {
        return Cliente::create([
            'nome_cliente' => 'Cliente Teste',
            'tipo_cliente' => 'PF',
            'cpf_cnpj_cliente' => '111.111.111-11',
            'data_nasc_cliente' => '2000-01-01',
            'endereco_cliente' => 'Rua Teste',
            'numero_cliente' => '100',
            'bairro_cliente' => 'Centro',
            'cidade_cliente' => 'Sao Paulo',
            'uf_cliente' => 'SP',
            'cep_cliente' => '01000-000',
            'email_cliente' => $email,
            'senha_cliente' => Hash::make('12345678'),
            'telefone_cliente' => '(11)99999-9999',
            'foto_cliente' => '',
            'status_cliente' => $status,
        ]);
    }

    private function login(string $email = 'cliente@davilla.com.br', string $senha = '12345678')
    {
        return $this->postJson('/api/v1/auth/login', [
            'email' => $email,
            'senha' => $senha,
            'device_name' => 'postman-producao',
        ]);
    }


    public function test_fluxo_login_cliente_logout(): void
    {
        $this->criarCliente();

        // 1. Login
        $resposta = $this->login();
        $resposta->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Login realizado com sucesso.')
            ->assertJsonStructure(['data' => ['token', 'cliente' => ['id_cliente', 'email_cliente']]]);

        $token = $resposta->json('data.token');
        $this->assertSame(1, DB::table('personal_access_tokens')->count());

        // 2. Dados do cliente, sem a senha
        $this->withToken($token)->getJson('/api/v1/cliente')
            ->assertOk()
            ->assertJsonPath('data.email_cliente', 'cliente@davilla.com.br')
            ->assertJsonMissingPath('data.senha_cliente');

        // 3. Logout apaga o token
        $this->withToken($token)->postJson('/api/v1/auth/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Logout realizado com sucesso.');

        $this->assertSame(0, DB::table('personal_access_tokens')->count());

        // O guard guarda o usuário entre requisições do mesmo teste
        $this->app['auth']->forgetGuards();

        // 4. Token revogado
        $this->withToken($token)->getJson('/api/v1/cliente')
            ->assertUnauthorized()
            ->assertJsonPath('success', false);
    }


    public function test_sem_token_e_sem_accept_retorna_401_json_e_nao_redireciona(): void
    {
        $this->get('/api/v1/cliente')
            ->assertUnauthorized()
            ->assertHeader('Content-Type', 'application/json')
            ->assertJsonPath('message', 'Não autenticado.');

        $this->withToken('1|token-invalido')->get('/api/v1/cliente')
            ->assertUnauthorized()
            ->assertHeader('Content-Type', 'application/json');
    }


    public function test_senha_errada_e_email_inexistente_tem_a_mesma_mensagem(): void
    {
        $this->criarCliente();

        $this->login(senha: 'senhaerrada')
            ->assertUnauthorized()
            ->assertJsonPath('message', 'E-mail ou senha inválidos.');

        $this->login(email: 'naoexiste@davilla.com.br')
            ->assertUnauthorized()
            ->assertJsonPath('message', 'E-mail ou senha inválidos.');

        $this->assertSame(0, DB::table('personal_access_tokens')->count());
    }


    public function test_cliente_inativo_retorna_403(): void
    {
        $this->criarCliente('INATIVO');

        $this->login()
            ->assertForbidden()
            ->assertJsonPath('message', 'Cliente inativo.');

        $this->assertSame(0, DB::table('personal_access_tokens')->count());
    }


    public function test_login_sem_campos_retorna_422_json(): void
    {
        $this->post('/api/v1/auth/login')
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'senha']);
    }
}
