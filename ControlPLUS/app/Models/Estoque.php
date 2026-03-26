<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'produto_id', 'quantidade', 'produto_variacao_id', 'local_id', 'deposito_id'
    ];

    protected static function booted()
    {
        static::saving(function (self $estoque) {
            if (empty($estoque->deposito_id) && !empty($estoque->local_id)) {
                $estoque->deposito_id = Deposito::resolveDefaultIdByLocalId((int)$estoque->local_id);
            }
        });
    }

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function local(){
        return $this->belongsTo(Localizacao::class, 'local_id');
    }

    public function deposito()
    {
        return $this->belongsTo(Deposito::class, 'deposito_id');
    }

    public function produtoVariacao(){
        return $this->belongsTo(ProdutoVariacao::class, 'produto_variacao_id');
    }

    public function descricao(){
        if($this->produto_variacao_id == null){
            return $this->produto->nome;
        }
        if($this->produtoVariacao){
            return $this->produto->nome . " - " . $this->produtoVariacao->descricao;
        }

        return $this->produto->nome;
    }

    public function resolveDepositoId(): ?int
    {
        if ($this->deposito_id) {
            return (int)$this->deposito_id;
        }

        if ($this->local_id) {
            return Deposito::resolveDefaultIdByLocalId((int)$this->local_id);
        }

        return null;
    }
}
