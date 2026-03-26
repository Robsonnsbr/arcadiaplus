<?php

namespace App\Services;

use App\Models\Deposito;
use App\Models\Estoque;
use App\Models\EstoqueStatusSaldo;
use App\Utils\QuantidadeUtil;
use App\Utils\StatusKeyUtil;
use App\Utils\VariacaoQueryUtil;

class EstoqueStatusService
{
    private function resolveDepositoContext(?int $deposito_id = null, ?int $local_id = null): array
    {
        if ($deposito_id) {
            $deposito = Deposito::select('id', 'local_id')->find($deposito_id);
            if (!$deposito) {
                throw new \Exception('Depósito de estoque inválido para a operação.');
            }

            return [
                'deposito_id' => (int)$deposito->id,
                'local_id' => (int)$deposito->local_id,
            ];
        }

        if ($local_id) {
            $depositoId = Deposito::resolveDefaultIdByLocalId($local_id);
            if (!$depositoId) {
                throw new \Exception('Depósito padrão não encontrado para o local informado.');
            }

            return [
                'deposito_id' => (int)$depositoId,
                'local_id' => (int)$local_id,
            ];
        }

        throw new \Exception('Depósito/local de estoque não definido para a operação.');
    }

    public function saldoFisicoDepositoUnits(int $produto_id, $produto_variacao_id, ?int $deposito_id = null, ?int $local_id = null): int
    {
        $contexto = $this->resolveDepositoContext($deposito_id, $local_id);
        $query = Estoque::where('produto_id', $produto_id)
            ->where(function ($q) use ($contexto) {
                $q->where('deposito_id', $contexto['deposito_id'])
                    ->orWhere(function ($legacy) use ($contexto) {
                        $legacy->whereNull('deposito_id')
                            ->where('local_id', $contexto['local_id']);
                    });
            });
        $query = VariacaoQueryUtil::apply($query, $produto_variacao_id);

        return QuantidadeUtil::toUnits($query->sum('quantidade'));
    }

    public function saldoFisicoLocalUnits(int $produto_id, $produto_variacao_id, int $local_id): int
    {
        return $this->saldoFisicoDepositoUnits($produto_id, $produto_variacao_id, null, $local_id);
    }

    public function somaReservasNaoAtivoDepositoUnits(int $empresa_id, int $produto_id, $produto_variacao_id, ?int $deposito_id = null, ?int $local_id = null): int
    {
        $contexto = $this->resolveDepositoContext($deposito_id, $local_id);
        $query = EstoqueStatusSaldo::where('empresa_id', $empresa_id)
            ->where('produto_id', $produto_id)
            ->where('status_key', '!=', StatusKeyUtil::DEFAULT_STATUS)
            ->where(function ($q) use ($contexto) {
                $q->where('deposito_id', $contexto['deposito_id'])
                    ->orWhere(function ($legacy) use ($contexto) {
                        $legacy->whereNull('deposito_id')
                            ->where('local_id', $contexto['local_id']);
                    });
            });
        $query = VariacaoQueryUtil::apply($query, $produto_variacao_id);

        return QuantidadeUtil::toUnits($query->sum('quantidade'));
    }

    public function somaReservasNaoAtivoLocalUnits(int $empresa_id, int $produto_id, $produto_variacao_id, int $local_id): int
    {
        return $this->somaReservasNaoAtivoDepositoUnits($empresa_id, $produto_id, $produto_variacao_id, null, $local_id);
    }

    public function ativoDisponivelDepositoUnits(int $empresa_id, int $produto_id, $produto_variacao_id, ?int $deposito_id = null, ?int $local_id = null): int
    {
        $saldoFisico = $this->saldoFisicoDepositoUnits($produto_id, $produto_variacao_id, $deposito_id, $local_id);
        $reservadoNaoAtivo = $this->somaReservasNaoAtivoDepositoUnits($empresa_id, $produto_id, $produto_variacao_id, $deposito_id, $local_id);

        return max(0, $saldoFisico - $reservadoNaoAtivo);
    }

    public function ativoDisponivelUnits(int $empresa_id, int $produto_id, $produto_variacao_id, int $local_id): int
    {
        return $this->ativoDisponivelDepositoUnits($empresa_id, $produto_id, $produto_variacao_id, null, $local_id);
    }

    public function reservasNaoAtivoDepositoLabels(int $empresa_id, int $produto_id, $produto_variacao_id, ?int $deposito_id = null, ?int $local_id = null): array
    {
        $contexto = $this->resolveDepositoContext($deposito_id, $local_id);
        $query = EstoqueStatusSaldo::where('empresa_id', $empresa_id)
            ->where('produto_id', $produto_id)
            ->where('status_key', '!=', StatusKeyUtil::DEFAULT_STATUS)
            ->where('quantidade', '>', 0)
            ->where(function ($q) use ($contexto) {
                $q->where('deposito_id', $contexto['deposito_id'])
                    ->orWhere(function ($legacy) use ($contexto) {
                        $legacy->whereNull('deposito_id')
                            ->where('local_id', $contexto['local_id']);
                    });
            });
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

    public function reservasNaoAtivoLabels(int $empresa_id, int $produto_id, $produto_variacao_id, int $local_id): array
    {
        return $this->reservasNaoAtivoDepositoLabels($empresa_id, $produto_id, $produto_variacao_id, null, $local_id);
    }
}
