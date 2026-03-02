<?php

namespace App\Services;

use App\Models\Estoque;
use App\Models\EstoqueStatusSaldo;
use App\Utils\QuantidadeUtil;
use App\Utils\StatusKeyUtil;
use App\Utils\VariacaoQueryUtil;

class EstoqueStatusService
{
    public function saldoFisicoLocalUnits(int $produto_id, $produto_variacao_id, int $local_id): int
    {
        $query = Estoque::where('produto_id', $produto_id)
            ->where('local_id', $local_id);
        $query = VariacaoQueryUtil::apply($query, $produto_variacao_id);

        return QuantidadeUtil::toUnits($query->sum('quantidade'));
    }

    public function somaReservasNaoAtivoLocalUnits(int $empresa_id, int $produto_id, $produto_variacao_id, int $local_id): int
    {
        $query = EstoqueStatusSaldo::where('empresa_id', $empresa_id)
            ->where('produto_id', $produto_id)
            ->where('local_id', $local_id)
            ->where('status_key', '!=', StatusKeyUtil::DEFAULT_STATUS);
        $query = VariacaoQueryUtil::apply($query, $produto_variacao_id);

        return QuantidadeUtil::toUnits($query->sum('quantidade'));
    }

    public function ativoDisponivelUnits(int $empresa_id, int $produto_id, $produto_variacao_id, int $local_id): int
    {
        $saldoFisico = $this->saldoFisicoLocalUnits($produto_id, $produto_variacao_id, $local_id);
        $reservadoNaoAtivo = $this->somaReservasNaoAtivoLocalUnits($empresa_id, $produto_id, $produto_variacao_id, $local_id);

        return max(0, $saldoFisico - $reservadoNaoAtivo);
    }

    public function reservasNaoAtivoLabels(int $empresa_id, int $produto_id, $produto_variacao_id, int $local_id): array
    {
        $query = EstoqueStatusSaldo::where('empresa_id', $empresa_id)
            ->where('produto_id', $produto_id)
            ->where('local_id', $local_id)
            ->where('status_key', '!=', StatusKeyUtil::DEFAULT_STATUS)
            ->where('quantidade', '>', 0);
        $query = VariacaoQueryUtil::apply($query, $produto_variacao_id);

        return $query->select('status_key')
            ->groupBy('status_key')
            ->pluck('status_key')
            ->map(function ($status) {
                return str_replace('_', ' ', (string)$status);
            })
            ->filter()
            ->values()
            ->all();
    }
}

