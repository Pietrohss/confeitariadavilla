<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Produto;

use Illuminate\Http\Request;

class HomeController extends Controller
{

    // Metodo HOME - Carregar a index
    public function home(){

        //Buscar CATEGORIA para montar a lista de filtro
        $filtroCategoria = Categoria::where('status_categoria', 'ATIVO')
          ->orderBy('ordem_categoria')
          ->get();
          

        //dd($filtroCategoria);

        return view('site.home.home', compact('$filtroCategoria'));
        
    }


}