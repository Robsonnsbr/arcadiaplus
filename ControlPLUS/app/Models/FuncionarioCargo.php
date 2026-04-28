<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuncionarioCargo extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'nome', 'status'
    ];

    public function funcionarios()
    {
        return $this->hasMany(Funcionario::class, 'funcionario_cargo_id');
    }
}
