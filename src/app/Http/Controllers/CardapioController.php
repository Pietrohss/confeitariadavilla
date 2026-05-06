<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Produto;

@@ -12,36 +13,41 @@ public function cardapio()
    {
        // Buscar CATEGORIA para montar a lista de filtro
        $filtroCategoria = Categoria::where('status_categoria', 'ATIVO')
        ->orderBy('ordem_categoria')
        ->get();
            ->orderBy('ordem_categoria')
            ->get();


        // Buscar todos os PRODUTOS ativos COM a categoria
        $listaProduto = Produto::with('CategoriaProduto')
        ->where('status_produto', 'ATIVO')
        ->orderBy('ordem_produto')
        ->get();
            ->where('status_produto', 'ATIVO')
            ->orderBy('ordem_produto')
            ->get();

       //dd($listaProduto);
        //dd($listaProduto);

        return view('site.cardapio.cardapio', compact('filtroCategoria', 'listaProduto'));

    }

    public function showProduto($slug){
    public function showProduto($slug)
    {

        $produto = Produto::with('CategoriaProduto')
        ->where('status_produto', 'ATIVO')
        ->where('slug_produto', $slug)
        ->firstOrFail();
            ->where('status_produto', 'ATIVO')
            ->where('slug_produto', $slug)
            ->firstOrFail();

        //dd($produto);
        $produtosRelacionados = Produto::where('status_produto', 'ATIVO')
            ->where('id_categoria', $produto->id_categoria)
            ->where('id_produto', '!=', $produto->id_produto)
            ->orderBy('ordem_produto')
            ->get();

        return view('site.cardapio.produto', compact('produto'));
        $listaCategoria = Categoria::where('status_categoria', 'ATIVO')
            ->orderBy('ordem_categoria')
            ->get();

    }
        //dd($listaCategoria);



    
        return view('site.produto.produto', compact('produto', 'produtosRelacionados', 'listaCategoria'));
    }
}