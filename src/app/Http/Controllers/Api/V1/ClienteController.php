<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }


    public function update(Request $request): JsonResponse
    {
        $cliente = $request->user();

        // PUT exige todos os campos; PATCH aceita atualização parcial
        $regra = $request->isMethod('patch') ? 'sometimes' : 'required';

        $dados = $request->validate([
            'nome_cliente' => [$regra, 'string', 'max:50'],
            'tipo_cliente' => [$regra, 'string', 'max:2'],
            'cpf_cnpj_cliente' => [
                $regra, 'string', 'max:18',
                Rule::unique('tbl_clientes', 'cpf_cnpj_cliente')->ignore($cliente->id_cliente, 'id_cliente'),
            ],
            'data_nasc_cliente' => [$regra, 'date'],
            'email_cliente' => [
                $regra, 'email', 'max:80',
                Rule::unique('tbl_clientes', 'email_cliente')->ignore($cliente->id_cliente, 'id_cliente'),
            ],
            'telefone_cliente' => [$regra, 'string', 'max:14'],
        ]);

        // Só os campos validados acima: senha, status e id nunca passam por aqui
        $cliente->update($dados);

        return response()->json([
            'success' => true,
            'message' => 'Perfil atualizado com sucesso.',
            'data' => $cliente->fresh()
        ]);
    }


    public function updateSenha(Request $request): JsonResponse
    {
        $cliente = $request->user();

        $dados = $request->validate([
            'senha_atual' => 'required|string',
            'nova_senha' => 'required|string|min:8|confirmed',
        ]);

        if (! Hash::check($dados['senha_atual'], $cliente->senha_cliente)) {
            return response()->json([
                'success' => false,
                'message' => 'Senha atual incorreta.'
            ], 422);
        }

        $cliente->senha_cliente = Hash::make($dados['nova_senha']);
        $cliente->save();

        return response()->json([
            'success' => true,
            'message' => 'Senha atualizada com sucesso.'
        ]);
    }
}
