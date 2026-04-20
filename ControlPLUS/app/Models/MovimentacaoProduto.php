<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimentacaoProduto extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id',
        'quantidade',
        'tipo',
        'codigo_transacao',
        'tipo_transacao',
        'produto_variacao_id',
        'deposito_id',
        'deposito_origem_id',
        'deposito_destino_id',
        'user_id',
        'estoque_atual',
        'serial',
    ];

    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function produtoVariacao(){
        return $this->belongsTo(ProdutoVariacao::class, 'produto_variacao_id');
    }

    public function deposito()
    {
        return $this->belongsTo(Deposito::class, 'deposito_id');
    }

    public function depositoOrigem()
    {
        return $this->belongsTo(Deposito::class, 'deposito_origem_id');
    }

    public function depositoDestino()
    {
        return $this->belongsTo(Deposito::class, 'deposito_destino_id');
    }

    public function tipoTransacao(): string
    {
        return match ($this->tipo_transacao) {
            'venda_nfe'             => 'Venda NFe',
            'venda_nfce'            => 'Venda NFCe',
            'compra'                => 'Compra',
            'transferencia_estoque' => 'Transferência de estoque',
            'tradein_entrada'       => 'Entrada Trade-in',
            default                 => 'Alteração de estoque',
        };
    }
}
