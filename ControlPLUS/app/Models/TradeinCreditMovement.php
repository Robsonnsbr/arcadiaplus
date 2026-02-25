<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeinCreditMovement extends Model
{
    use HasFactory;

    public const TYPE_CREDIT = 'credit';
    public const TYPE_DEBIT = 'debit';
    public const PAYMENT_CODE = '98';
    public const PAYMENT_LABEL = 'Crédito (trade-in)';
    public const ORIGEM_COMPRA = 'nfe_tradein_credit';
    public const ORIGEM_VENDA_NFCE = 'nfce';
    public const ORIGEM_VENDA_NFE = 'nfe';
    public const ORIGEM_REVERSAL_NFCE = 'nfce_reversal';
    public const ORIGEM_REVERSAL_NFE = 'nfe_reversal';

    protected $fillable = [
        'empresa_id',
        'documento',
        'cliente_id',
        'fornecedor_id',
        'tipo',
        'valor',
        'origem_tipo',
        'origem_id',
        'ref_texto',
        'user_id',
    ];

    protected $casts = [
        'valor' => 'float',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'fornecedor_id');
    }

    public static function sanitizeDocumento(?string $documento): ?string
    {
        $doc = preg_replace('/\D/', '', $documento ?? '');
        return $doc ? substr($doc, 0, 20) : null;
    }

    public static function saldoDisponivel(int $empresaId, ?string $documento, bool $lock = false): float
    {
        $doc = self::sanitizeDocumento($documento);
        if (!$doc) {
            return 0.0;
        }

        $query = self::where('empresa_id', $empresaId)
            ->where('documento', $doc);

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->get()->reduce(function ($carry, TradeinCreditMovement $movement) {
            return $carry + ($movement->tipo === self::TYPE_CREDIT ? $movement->valor : -$movement->valor);
        }, 0.0);
    }

    public static function totalPorOrigem(int $empresaId, string $documento, string $tipo, string $origemTipo, int $origemId): float
    {
        $doc = self::sanitizeDocumento($documento);
        if (!$doc) {
            return 0.0;
        }

        return (float) self::where('empresa_id', $empresaId)
            ->where('documento', $doc)
            ->where('tipo', $tipo)
            ->where('origem_tipo', $origemTipo)
            ->where('origem_id', $origemId)
            ->sum('valor');
    }
}
