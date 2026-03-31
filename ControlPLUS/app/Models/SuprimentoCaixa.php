<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuprimentoCaixa extends Model
{
    use HasFactory;

    protected $fillable = [
        'caixa_id', 'valor', 'observacao', 'conta_empresa_id', 'tipo_pagamento', 'funcionario_id'
    ];

    public function contaEmpresa()
    {
        return $this->belongsTo(ContaEmpresa::class, 'conta_empresa_id');
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }
}
