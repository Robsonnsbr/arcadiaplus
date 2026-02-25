<?php

namespace App\Utils;

use App\Models\Cliente;
use App\Models\Fornecedor;
use App\Models\Nfce;
use App\Models\Nfe;
use App\Models\TradeinCreditMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TradeinCreditUtil
{
    public function saldo(int $empresaId, ?string $documento): float
    {
        return TradeinCreditMovement::saldoDisponivel($empresaId, $documento);
    }

    public function registrarCreditoCompra(Nfe $nfe, ?Fornecedor $fornecedor, float $valor, ?int $userId = null): ?TradeinCreditMovement
    {
        if ($valor <= 0) {
            return null;
        }

        $doc = $fornecedor ? $fornecedor->cpf_cnpj : null;
        $documento = TradeinCreditMovement::sanitizeDocumento($doc);
        if (!$documento) {
            return null;
        }

        return TradeinCreditMovement::create([
            'empresa_id' => $nfe->empresa_id,
            'documento' => $documento,
            'fornecedor_id' => $fornecedor?->id,
            'tipo' => TradeinCreditMovement::TYPE_CREDIT,
            'valor' => $valor,
            'origem_tipo' => TradeinCreditMovement::ORIGEM_COMPRA,
            'origem_id' => $nfe->id,
            'ref_texto' => 'Crédito trade-in compra #' . $nfe->numero_sequencial,
            'user_id' => $userId ?? Auth::id(),
        ]);
    }

    public function consumirCreditoVenda(Model $documentoFiscal, ?Cliente $cliente, ?string $cpfCnpjFallback, float $valor, string $origemTipo): TradeinCreditMovement
    {
        if ($valor <= 0) {
            throw new \InvalidArgumentException('Valor de crédito inválido.');
        }

        $documento = $cliente?->cpf_cnpj ?? $cpfCnpjFallback;
        $sanitized = TradeinCreditMovement::sanitizeDocumento($documento);
        if (!$sanitized) {
            throw new \RuntimeException('Documento do cliente obrigatório para uso de crédito trade-in.');
        }

        $saldo = TradeinCreditMovement::saldoDisponivel($documentoFiscal->empresa_id, $sanitized, true);
        if ($saldo < $valor - 0.0001) {
            throw new \RuntimeException('Crédito trade-in insuficiente para concluir a venda.');
        }

        return TradeinCreditMovement::create([
            'empresa_id' => $documentoFiscal->empresa_id,
            'documento' => $sanitized,
            'cliente_id' => $cliente?->id,
            'tipo' => TradeinCreditMovement::TYPE_DEBIT,
            'valor' => $valor,
            'origem_tipo' => $origemTipo,
            'origem_id' => $documentoFiscal->id,
            'ref_texto' => 'Uso trade-in #' . $documentoFiscal->numero_sequencial,
            'user_id' => $documentoFiscal->user_id,
        ]);
    }

    public function estornarPorNfce(Nfce $nfce): void
    {
        $this->estornarMovimento(
            $nfce,
            $nfce->cliente?->cpf_cnpj ?? $nfce->cliente_cpf_cnpj,
            TradeinCreditMovement::ORIGEM_VENDA_NFCE,
            TradeinCreditMovement::ORIGEM_REVERSAL_NFCE
        );
    }

    public function estornarPorNfe(Nfe $nfe): void
    {
        $this->estornarMovimento(
            $nfe,
            $nfe->cliente?->cpf_cnpj ?? $nfe->cliente_cpf_cnpj,
            TradeinCreditMovement::ORIGEM_VENDA_NFE,
            TradeinCreditMovement::ORIGEM_REVERSAL_NFE
        );
    }

    private function estornarMovimento(Model $documentoFiscal, ?string $documento, string $origemTipo, string $origemEstorno): void
    {
        $sanitized = TradeinCreditMovement::sanitizeDocumento($documento);
        if (!$sanitized) {
            return;
        }

        $valorDebitado = TradeinCreditMovement::totalPorOrigem(
            $documentoFiscal->empresa_id,
            $sanitized,
            TradeinCreditMovement::TYPE_DEBIT,
            $origemTipo,
            $documentoFiscal->id
        );

        if ($valorDebitado <= 0) {
            return;
        }

        $jaEstornado = TradeinCreditMovement::where('empresa_id', $documentoFiscal->empresa_id)
            ->where('documento', $sanitized)
            ->where('tipo', TradeinCreditMovement::TYPE_CREDIT)
            ->where('origem_tipo', $origemEstorno)
            ->where('origem_id', $documentoFiscal->id)
            ->exists();

        if ($jaEstornado) {
            return;
        }

        TradeinCreditMovement::create([
            'empresa_id' => $documentoFiscal->empresa_id,
            'documento' => $sanitized,
            'cliente_id' => property_exists($documentoFiscal, 'cliente_id') ? $documentoFiscal->cliente_id : null,
            'tipo' => TradeinCreditMovement::TYPE_CREDIT,
            'valor' => $valorDebitado,
            'origem_tipo' => $origemEstorno,
            'origem_id' => $documentoFiscal->id,
            'ref_texto' => 'Estorno crédito trade-in #' . $documentoFiscal->numero_sequencial,
            'user_id' => $documentoFiscal->user_id,
        ]);
    }
}
