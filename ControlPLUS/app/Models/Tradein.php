<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tradein extends Model
{
    use HasFactory;

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const ACEITE_PENDING = 'pending';
    public const ACEITE_ACCEPTED = 'accepted';
    public const ACEITE_REJECTED = 'rejected';

    protected $fillable = [
        'empresa_id',
        'cliente_id',
        'created_by_user_id',
        'assigned_to_user_id',
        'status',
        'nome_item',
        'serial_number',
        'valor_pretendido',
        'observacao_vendedor',
        'check_tela_ok',
        'check_bateria_ok',
        'check_carregamento_ok',
        'check_botoes_ok',
        'check_camera_ok',
        'observacao_tecnico',
        'valor_avaliado',
        'avaliado_em',
        'status_aceite_cliente',
        'aceite_em',
    ];

    protected $casts = [
        'check_tela_ok' => 'boolean',
        'check_bateria_ok' => 'boolean',
        'check_carregamento_ok' => 'boolean',
        'check_botoes_ok' => 'boolean',
        'check_camera_ok' => 'boolean',
        'valor_pretendido' => 'float',
        'valor_avaliado' => 'float',
        'avaliado_em' => 'datetime',
        'aceite_em' => 'datetime',
    ];
}
