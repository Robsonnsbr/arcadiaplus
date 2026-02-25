<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeinInventoryItem extends Model
{
    use HasFactory;

    public const STATUS_PENDING_TRANSFER = 'pending_transfer';
    public const STATUS_TRANSFERRED = 'transferred';

    protected $fillable = [
        'empresa_id',
        'tradein_id',
        'cliente_id',
        'descricao_item',
        'serial',
        'valor',
        'status',
        'observacao_tecnica',
        'created_by_user_id',
    ];

    protected $casts = [
        'valor' => 'float',
    ];
}
