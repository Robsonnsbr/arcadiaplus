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
        'status_key',
        'quantidade',
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function local()
    {
        return $this->belongsTo(Localizacao::class, 'local_id');
    }
}
