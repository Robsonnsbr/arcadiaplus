<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContaReceber extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'nfe_id', 'nfce_id', 'cliente_id', 'descricao', 'valor_integral', 'valor_recebido', 'data_vencimento',
        'data_recebimento', 'status', 'observacao', 'tipo_pagamento', 'caixa_id', 'local_id', 'arquivo', 'motivo_estorno',
        'categoria_conta_id', 'valor_original', 'observacao2', 'observacao3', 'referencia', 'conta_empresa_id', 'ordem_servico_id'
    ];

    protected $appends = [ 'info' ];

    public function getInfoAttribute()
    {   
        if($this->cliente){
            return "Cliente: " . $this->cliente->info . " - valor: R$ " . __moeda($this->valor_integral) . ", vencimento: " . __data_pt($this->data_vencimento, 0);
        }else{
            return "Valor: R$ " . __moeda($this->valor_integral) . ", vencimento: " . __data_pt($this->data_vencimento, 0);
        }
    }

    public function categoria()
    {
        return $this->belongsTo(CategoriaConta::class, 'categoria_conta_id');
    }

    public function localizacao()
    {
        return $this->belongsTo(Localizacao::class, 'local_id');
    }

    public function nfce()
    {
        return $this->belongsTo(Nfce::class, 'nfce_id');
    }

    public function contaEmpresa()
    {
        return $this->belongsTo(ContaEmpresa::class, 'conta_empresa_id');
    }

    public function nfe()
    {
        return $this->belongsTo(Nfe::class, 'nfe_id');
    }

    public function ordemServico()
    {
        return $this->belongsTo(OrdemServico::class, 'ordem_servico_id');
    }

    public function contaFatura(){
        $nfe = $this->nfe;
        $total = sizeof($nfe->fatura);

        $posicao = 1;
        foreach($nfe->fatura as $fat){
            if($fat->data_vencimento != $this->data_vencimento){
                $posicao++;
            }else{
                return "$posicao/$total";
            }
        }
        return "$posicao/$total";
    }
    
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id')->with('cidade');
    }

    public function boleto()
    {
        return $this->hasOne(Boleto::class, 'conta_receber_id');
    }

    public static function tiposPagamento()
    {
        return [
            '01' => 'Dinheiro',
            '02' => 'Cheque',
            '03' => 'Cartão de Crédito',
            '04' => 'Cartão de Débito',
            '05' => 'Crédito Loja',
            '06' => 'Crediário',
            '10' => 'Vale Alimentação',
            '11' => 'Vale Refeição',
            '12' => 'Vale Presente',
            '13' => 'Vale Combustível',
            '14' => 'Duplicata Mercantil',
            '15' => 'Boleto Bancário',
            '16' => 'Depósito Bancário',
            '17' => 'Pagamento Instantâneo (PIX)',
            TradeinCreditMovement::PAYMENT_CODE => TradeinCreditMovement::PAYMENT_LABEL,
            '90' => 'Sem Pagamento',
            '99' => 'Outros',
        ];
    }

    public static function isPagamentoImediato($tipoPagamento): bool
    {
        $tipo = trim((string)$tipoPagamento);
        if ($tipo === '') {
            return false;
        }

        return in_array($tipo, [
            '01', // Dinheiro
            '03', // Cartão de Crédito
            '04', // Cartão de Débito
            '05', // Crédito Loja
            '10', // Vale Alimentação
            '11', // Vale Refeição
            '12', // Vale Presente
            '13', // Vale Combustível
            '16', // Depósito Bancário
            '17', // PIX
            '18', // Transferência/Carteira
            '19', // Fidelidade/Cashback
            '30', // Crédito TEF
            '31', // Débito TEF
            '32', // PIX TEF
            TradeinCreditMovement::PAYMENT_CODE,
        ], true);
    }

    private static function resolveStatusPorPagamento(string $tipoPagamento, ?int $statusInformado): int
    {
        if ($statusInformado === 1) {
            return 1;
        }

        if (self::isPagamentoImediato($tipoPagamento)) {
            return 1;
        }

        if ($statusInformado !== null) {
            return $statusInformado > 0 ? 1 : 0;
        }

        return 0;
    }

    public function diasAtraso(){
        $d = date('Y-m-d');
        $d2 = $this->data_vencimento;
        $dif = strtotime($d2) - strtotime($d);
        $dias = floor($dif / (60 * 60 * 24));
        if($dias == 0){
            return "conta vence hoje";
        }

        if($dias > 0){
            return "$dias dia(s) para o vencimento";
        }else{
            return "conta vencida à " . ($dias*-1) . " dia(s)";
        }
    } 

    public static function gerarDeFaturaNfce(array $data): ?self
    {
        return self::gerarDeFatura($data, 'nfce');
    }

    public static function gerarDeFaturaNfe(array $data): ?self
    {
        return self::gerarDeFatura($data, 'nfe');
    }

    private static function gerarDeFatura(array $data, string $tipoDocumento): ?self
    {
        $empresaId = isset($data['empresa_id']) ? (int)$data['empresa_id'] : 0;
        $documentoId = isset($data[$tipoDocumento . '_id']) ? (int)$data[$tipoDocumento . '_id'] : 0;
        if ($empresaId <= 0 || $documentoId <= 0) {
            return null;
        }

        $valorIntegral = self::normalizeValor($data['valor_integral'] ?? null);
        if ($valorIntegral === null || $valorIntegral <= 0) {
            return null;
        }

        $dataVencimento = $data['data_vencimento'] ?? null;
        if (empty($dataVencimento)) {
            return null;
        }

        $tipoPagamento = isset($data['tipo_pagamento']) ? trim((string)$data['tipo_pagamento']) : '';
        $referencia = (string)($data['referencia'] ?? '');
        $descricao = (string)($data['descricao'] ?? '');
        $statusInformado = array_key_exists('status', $data) ? (int)$data['status'] : null;
        $status = self::resolveStatusPorPagamento($tipoPagamento, $statusInformado);

        $dataRecebimento = $data['data_recebimento'] ?? null;
        if ($status === 1) {
            if (self::isPagamentoImediato($tipoPagamento)) {
                $dataRecebimento = $data['data_venda'] ?? date('Y-m-d');
            } elseif (!$dataRecebimento) {
                $dataRecebimento = $dataVencimento;
            }
        } else {
            $dataRecebimento = null;
        }

        $valorRecebidoInformado = self::normalizeValor($data['valor_recebido'] ?? null);
        $valorRecebido = $status === 1
            ? ($valorRecebidoInformado ?? $valorIntegral)
            : 0.0;

        $payload = [
            'empresa_id' => $empresaId,
            'nfe_id' => $tipoDocumento === 'nfe' ? $documentoId : null,
            'nfce_id' => $tipoDocumento === 'nfce' ? $documentoId : null,
            'cliente_id' => $data['cliente_id'] ?? null,
            'valor_integral' => $valorIntegral,
            'valor_original' => $data['valor_original'] ?? $valorIntegral,
            'valor_recebido' => $valorRecebido,
            'tipo_pagamento' => $tipoPagamento,
            'data_vencimento' => $dataVencimento,
            'data_recebimento' => $dataRecebimento,
            'status' => $status,
            'descricao' => $descricao,
            'referencia' => $referencia,
            'observacao' => $data['observacao'] ?? null,
            'caixa_id' => $data['caixa_id'] ?? null,
            'local_id' => $data['local_id'] ?? null,
        ];

        $lookup = [
            'empresa_id' => $payload['empresa_id'],
            'nfe_id' => $payload['nfe_id'],
            'nfce_id' => $payload['nfce_id'],
            'tipo_pagamento' => $payload['tipo_pagamento'],
            'data_vencimento' => $payload['data_vencimento'],
            'valor_integral' => $payload['valor_integral'],
        ];
        if ($payload['referencia'] !== '') {
            $lookup['referencia'] = $payload['referencia'];
        }
        if ($payload['descricao'] !== '') {
            $lookup['descricao'] = $payload['descricao'];
        }

        $conta = self::firstOrCreate($lookup, $payload);
        if (!$conta->wasRecentlyCreated && (int)$conta->status === 0 && $status === 1) {
            $conta->fill([
                'status' => 1,
                'valor_recebido' => $payload['valor_recebido'],
                'data_recebimento' => $payload['data_recebimento'],
                'tipo_pagamento' => $payload['tipo_pagamento'],
            ]);
            $conta->save();
        }

        return $conta;
    }

    private static function normalizeValor($valor): ?float
    {
        if ($valor === null || $valor === '') {
            return null;
        }
        if (is_numeric($valor)) {
            return round((float)$valor, 7);
        }
        if (function_exists('__convert_value_bd')) {
            return round((float)__convert_value_bd((string)$valor), 7);
        }
        return round((float)$valor, 7);
    }
}
