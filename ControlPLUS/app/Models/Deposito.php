<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposito extends Model
{
    use HasFactory;

    public const DEFAULT_NOME = 'Depósito Padrão';

    protected $fillable = [
        'empresa_id',
        'local_id',
        'nome',
        'descricao',
        'ativo',
        'padrao',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'padrao' => 'boolean',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class, 'local_id');
    }

    public function estoques()
    {
        return $this->hasMany(Estoque::class, 'deposito_id');
    }

    public function produtoUnicos()
    {
        return $this->hasMany(ProdutoUnico::class, 'deposito_id');
    }

    public function estoqueStatusSaldos()
    {
        return $this->hasMany(EstoqueStatusSaldo::class, 'deposito_id');
    }

    public static function resolveDefaultIdByLocalId(?int $localId): ?int
    {
        $deposito = static::ensureDefaultForLocalId($localId);
        return $deposito ? (int)$deposito->id : null;
    }

    public static function resolveIdForLocalId(?int $localId, ?int $depositoId = null): ?int
    {
        if ($depositoId) {
            $deposito = static::select('id', 'local_id')->find($depositoId);
            if (!$deposito) {
                return null;
            }

            if ($localId && (int)$deposito->local_id !== (int)$localId) {
                return null;
            }

            return (int)$deposito->id;
        }

        return static::resolveDefaultIdByLocalId($localId);
    }

    public static function resolveLocalIdByDepositoId(?int $depositoId): ?int
    {
        if (!$depositoId) {
            return null;
        }

        $deposito = static::select('id', 'local_id')->find($depositoId);
        return $deposito ? (int)$deposito->local_id : null;
    }

    public static function ensureDefaultForLocalId(?int $localId): ?self
    {
        if (!$localId) {
            return null;
        }

        $localizacao = Localizacao::select('id', 'empresa_id', 'descricao', 'status')->find($localId);
        if (!$localizacao) {
            return null;
        }

        $descricaoLocal = trim((string)($localizacao->descricao ?? ''));
        $descricaoDeposito = $descricaoLocal !== ''
            ? "Depósito padrão vinculado à unidade {$descricaoLocal}"
            : 'Depósito padrão vinculado à unidade';

        return static::firstOrCreate(
            [
                'local_id' => (int)$localizacao->id,
                'nome' => self::DEFAULT_NOME,
            ],
            [
                'empresa_id' => (int)$localizacao->empresa_id,
                'descricao' => $descricaoDeposito,
                'ativo' => (int)$localizacao->status === 1,
                'padrao' => true,
            ]
        );
    }
}
