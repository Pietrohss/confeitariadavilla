<?php

/*Site*/
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\CardapioController;
use App\Http\Controllers\Site\ContatoController;
use App\Http\Controllers\Site\PedidosController;
use App\Http\Controllers\Site\RegiaoController;
use App\Http\Controllers\Site\SobreController;
use Illuminate\Support\Facades\Route;

/*Admin*/
use App\Http\Controllers\DashController;

Route::get('/', [HomeController::class, 'home'])->name('home');
Route::get('/sobre', [SobreController::class, 'sobre'])->name('sobre');
Route::get('/cardapio', [CardapioController::class, 'cardapio'])->name('cardapio.index');
Route::get('/pedidos', [PedidosController::class, 'pedidos'])->name('pedidos');
Route::get('/regiao', [RegiaoController::class, 'regiao'])->name('regiao.index');
Route::get('/contato', [ContatoController::class, 'contato'])->name('contato');

/** Submenu de cardapio */
Route::get('/cardapio/categoria/{id}', [CardapioController::class, 'show'])->name('cardapio.categoria');

/** Submenu de produto */
Route::get('/cardapio/produto/{slug}', [CardapioController::class, 'showProduto'])->name('cardapio.produto');

/** Submenu de regitão */
Route::get('/regiao/area/{id}', [RegiaoController::class, 'show'])->name('regiao.area');


Route::get('/home/produto/{slug}', [HomeController::class, 'linkProduto'])->name('banner');


route::prefix('admin')->name('admin.')->group(function(){


    Route::get('/', [DashController::class, 'index'])->name('dash');

});

