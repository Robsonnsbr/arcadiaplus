<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstoqueStatusSaldo extends Model
{
    use HasFactory;

    protected $casts = [
        'quantidade' => 'decimal:4',
    ];

    protected $fillable = [
        'empresa_id',
        'produto_id',
        'produto_variacao_id',
        'local_id',
        'deposito_id',
        'status_key',
        'quantidade',
    ];

    protected static function booted()
    {
        static::saving(function (self $saldo) {
            if (empty($saldo->deposito_id) && !empty($saldo->local_id)) {
                $saldo->deposito_id = Deposito::resolveDefaultIdByLocalId((int)$saldo->local_id);
            }
        });
    }

    public function produto()
    {
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
