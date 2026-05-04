<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Produto;

class Categoria extends Model
{
    protected $fillable = [
        'nome_categoria',
        'descricao_categoria',
        'status__categoria',
        'ordem_categoria',
        'status_categoria',
        'ordem_categoria',
    ];

    // hasMany: tem muitos
    public function ProdutosCategoria() {
        return $this->hasMany(Produto::class, 'id_categoria',  'id_categoria');
    }

}