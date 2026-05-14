<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{

    public function index(){

        $categoria = Categoria::orderBy('ordem_categoria')
        ->get();

        return view('admin.categoria.index', compact('categorias'));


    }




}