<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Htto\Request;

class AuthController extends Controller
{

    public function login(){

        return view('admin.auth.login');
    }

    public function autenticar(Request $request){

        $request->validade([
            'email_usuario'           => 'required|email',
            'senha_usuario'           => 'required',
        ]);

        // Credencias nescessarias para o USER/FUNCIONARIO logar na aplicação:
        $user = [
            'email_usuarion' => $request->email_usuario,
            'senha_usuario'  => $request->senha_usuario,
            'status_usuario' => 'ATIVO'
        ];

        if(Auth::guard('admin')->attempt($user, $request->filled('remember'))){
            $request->session()->regenerate();

            return redirect()-route('admin.dash');
        }

        return back()->withInput()->with('erro', 'E-mail ou senha invalida');

    }

    public function logout(){

    }

}