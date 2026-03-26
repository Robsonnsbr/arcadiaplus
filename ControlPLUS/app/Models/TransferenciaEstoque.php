<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferenciaEstoque extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'local_saida_id',
        'deposito_saida_id',
        'local_entrada_id',
        'deposito_entrada_id',
        'usuario_id',
        'observacao',
        'codigo_transacao',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $transferencia) {
            if ($transferencia->deposito_saida_id) {
                $localSaidaDeposito = Deposito::resolveLocalIdByDepositoId((int)$transferencia->deposito_saida_id);
                if (!$localSaidaDeposito) {
                    throw new \Exception('Depósito de saída inválido para a transferência.');
                }

                if ($transferencia->local_saida_id && (int)$transferencia->local_saida_id !== (int)$localSaidaDeposito) {
                    throw new \Exception('Depósito de saída incompatível com a unidade informada.');
                }

                $transferencia->local_saida_id = (int)$localSaidaDeposito;
            }

            if ($transferencia->deposito_entrada_id) {
                $localEntradaDeposito = Deposito::resolveLocalIdByDepositoId((int)$transferencia->deposito_entrada_id);
                if (!$localEntradaDeposito) {
                    throw new \Exception('Depósito de entrada inválido para a transferência.');
                }

                if ($transferencia->local_entrada_id && (int)$transferencia->local_entrada_id !== (int)$localEntradaDeposito) {
                    throw new \Exception('Depósito de entrada incompatível com a unidade informada.');
                }

                $transferencia->local_entrada_id = (int)$localEntradaDeposito;
            }

            if (
                $transferencia->deposito_saida_id &&
                $transferencia->deposito_entrada_id &&
                (int)$transferencia->deposito_saida_id === (int)$transferencia->deposito_entrada_id
            ) {
                throw new \Exception('Depósitos de saída e entrada devem ser diferentes.');
            }
        });
    }

    public function local_saida(){
        return $this->belongsTo(Localizacao::class, 'local_saida_id');
    }

    public function deposito_saida()
    {
        return $this->belongsTo(Deposito::class, 'deposito_saida_id');
    }

    public function local_entrada(){
        return $this->belongsTo(Localizacao::class, 'local_entrada_id');
    }

    public function deposito_entrada()
    {
        return $this->belongsTo(Deposito::class, 'deposito_entrada_id');
    }

    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function itens(){
        return $this->hasMany(ItemTransferenciaEstoque::class, 'transferencia_id');
    }
}
