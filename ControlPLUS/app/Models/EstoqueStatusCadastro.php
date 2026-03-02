<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstoqueStatusCadastro extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'status_key',
        'descricao',
        'is_system',
        'ativo',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'ativo' => 'boolean',
    ];
}
