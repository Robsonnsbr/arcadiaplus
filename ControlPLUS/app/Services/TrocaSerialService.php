<?php

namespace App\Services;

use App\Models\ProdutoUnico;
use App\Utils\StatusKeyUtil;

class TrocaSerialService
{
    public function restaurarUmaSaidaSerial(ProdutoUnico $saida, int $localId): void
    {
        $entrada = ProdutoUnico::query()
            ->where('produto_id', $saida->produto_id)
            ->where('codigo', $saida->codigo)
            ->where('tipo', 'entrada')
            ->lockForUpdate()
            ->first();
        if (!$entrada) {
            throw new \Exception("Entrada de estoque do serial {$saida->codigo} não encontrada.");
        }
        $entrada->em_estoque = 1;
        if (!$entrada->local_id) {
            $entrada->local_id = $localId;
        }
        $entrada->save();
        $saida->delete();
    }

    public function expedirSerialComoVendido(int $produtoId, string $codigo, int $localId, ?int $nfceId, ?int $nfeId): void
    {
        $serial = ProdutoUnico::query()
            ->where('produto_id', $produtoId)
            ->where('tipo', 'entrada')
            ->where('em_estoque', 1)
            ->where('codigo', $codigo)
            ->lockForUpdate()
            ->first();
        if (!$serial) {
            throw new \Exception("Serial {$codigo} não encontrado em estoque para reversão.");
        }

        $statusAtual = StatusKeyUtil::normalizeOrDefault($serial->status_key);
        if (!$serial->local_id) {
            $serial->local_id = $localId;
        }
        $serial->em_estoque = 0;
        $serial->save();

        ProdutoUnico::create([
            'nfe_id' => $nfeId,
            'nfce_id' => $nfceId,
            'produto_id' => $produtoId,
            'local_id' => (int) $serial->local_id,
            'codigo' => $serial->codigo,
            'observacao' => '',
            'tipo' => 'saida',
            'em_estoque' => 0,
            'status_key' => $statusAtual,
        ]);
    }
}
