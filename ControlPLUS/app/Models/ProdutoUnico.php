<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoUnico extends Model
{
    use HasFactory;

    protected $fillable = [
        'nfe_id',
        'nfce_id',
        'produto_id',
        'local_id',
        'deposito_id',
        'codigo',
        'observacao',
        'tipo',
        'em_estoque',
        'status_key',
    ];

    protected static function booted()
    {
        static::saving(function (self $produtoUnico) {
            if (empty($produtoUnico->deposito_id) && !empty($produtoUnico->local_id)) {
                $produtoUnico->deposito_id = Deposito::resolveDefaultIdByLocalId((int)$produtoUnico->local_id);
            }
        });
    }

    public function nfe(){
        return $this->belongsTo(Nfe::class, 'nfe_id');
    }

    public function nfce(){
        return $this->belongsTo(Nfce::class, 'nfce_id');
    }

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function local()
    {
        return $this->belongsTo(Localizacao::class, 'local_id');
    }

    public function deposito()
    {
        return $this->belongsTo(Deposito::class, 'deposito_id');
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
