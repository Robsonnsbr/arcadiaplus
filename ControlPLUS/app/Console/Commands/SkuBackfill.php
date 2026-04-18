<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Produto;
use App\Models\ProdutoVariacao;
use Illuminate\Support\Facades\DB;

/**
 * Preenche a coluna `sku` para produtos e variações que ainda não a possuem.
 *
 * Estratégia de resolução por prioridade:
 *   1. Se `referencia` for única por empresa → usa como SKU.
 *   2. Se `codigo_barras` (não vazio) for único por empresa → usa como SKU.
 *   3. Gera automaticamente: P-{empresa_id}-{numero_sequencial ou id}.
 *
 * Para variações:
 *   1. Se `referencia` for única por produto → usa como SKU.
 *   2. Gera: V-{produto_id}-{variacao_id}.
 *
 * Opções:
 *   --dry-run  : Apenas lista o que faria, sem gravar nada.
 *   --empresa  : Processa apenas uma empresa específica (por ID).
 *   --force    : Sobrescreve SKUs já existentes (use com cuidado).
 */
class SkuBackfill extends Command
{
    protected $signature = 'sku:backfill
                            {--dry-run : Exibe o que seria feito sem gravar}
                            {--empresa= : Processa apenas a empresa com este ID}
                            {--force : Sobrescreve SKUs já existentes}';

    protected $description = 'Preenche a coluna SKU para produtos e variações sem SKU';

    public function handle(): int
    {
        $dryRun    = $this->option('dry-run');
        $empresaId = $this->option('empresa') ? (int)$this->option('empresa') : null;
        $force     = $this->option('force');

        if ($dryRun) {
            $this->warn('MODO DRY-RUN: nenhuma alteração será gravada.');
        }

        $this->info('Carregando produtos...');

        $query = Produto::query()->orderBy('empresa_id')->orderBy('id');

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        if (!$force) {
            $query->whereNull('sku');
        }

        $produtos = $query->get();

        $this->info("Produtos a processar: {$produtos->count()}");

        $stats = ['gerados' => 0, 'colisoes' => 0, 'ignorados' => 0];

        foreach ($produtos as $produto) {
            $sku = $this->resolveSkuParaProduto($produto);

            if (!$sku) {
                $this->warn("  [COLISÃO] Produto #{$produto->id} ({$produto->nome}) — não foi possível gerar SKU único.");
                $stats['colisoes']++;
                continue;
            }

            $this->line("  [PRODUTO] #{$produto->id} → SKU: {$sku}  ({$produto->nome})");

            if (!$dryRun) {
                $produto->sku = $sku;
                $produto->saveQuietly();
            }

            $stats['gerados']++;
        }

        // ---------- Variações ----------
        $this->info('');
        $this->info('Carregando variações...');

        $varQuery = ProdutoVariacao::query()->orderBy('produto_id')->orderBy('id');

        if ($empresaId) {
            $varQuery->whereHas('produto', fn ($q) => $q->where('empresa_id', $empresaId));
        }

        if (!$force) {
            $varQuery->whereNull('sku');
        }

        $variacoes = $varQuery->get();

        $this->info("Variações a processar: {$variacoes->count()}");

        foreach ($variacoes as $v) {
            $sku = $this->resolveSkuParaVariacao($v);

            if (!$sku) {
                $this->warn("  [COLISÃO] Variação #{$v->id} do produto #{$v->produto_id} — não foi possível gerar SKU único.");
                $stats['colisoes']++;
                continue;
            }

            $this->line("  [VARIAÇÃO] #{$v->id} (produto #{$v->produto_id}) → SKU: {$sku}");

            if (!$dryRun) {
                $v->sku = $sku;
                $v->saveQuietly();
            }

            $stats['gerados']++;
        }

        $this->info('');
        $this->info('=== Resultado ===');
        $this->info("Gerados/atualizados : {$stats['gerados']}");
        $this->warn("Colisões (ignorados) : {$stats['colisoes']}");

        if ($dryRun) {
            $this->warn('DRY-RUN: nenhum dado foi gravado.');
        }

        return self::SUCCESS;
    }

    private function resolveSkuParaProduto(Produto $produto): ?string
    {
        $empresaId = $produto->empresa_id;

        // 1. Tenta `referencia` se não vazia e única na empresa
        $ref = trim($produto->referencia ?? '');
        if ($ref !== '') {
            $skuCandidate = $this->normalizaSku($ref);
            if ($this->skuDisponivelEmProduto($skuCandidate, $empresaId, $produto->id)) {
                return $skuCandidate;
            }
        }

        // 2. Tenta `codigo_barras` se não vazio e único na empresa
        $ean = trim($produto->codigo_barras ?? '');
        if ($ean !== '') {
            $skuCandidate = $this->normalizaSku($ean);
            if ($this->skuDisponivelEmProduto($skuCandidate, $empresaId, $produto->id)) {
                return $skuCandidate;
            }
        }

        // 3. Gera automaticamente: P-{empresa_id}-{numero_sequencial ou id}
        $seq = $produto->numero_sequencial ?: $produto->id;
        $skuCandidate = "P-{$empresaId}-{$seq}";
        if ($this->skuDisponivelEmProduto($skuCandidate, $empresaId, $produto->id)) {
            return $skuCandidate;
        }

        // 4. Adiciona sufixo para desambiguar
        $skuCandidate = "P-{$empresaId}-{$produto->id}";
        if ($this->skuDisponivelEmProduto($skuCandidate, $empresaId, $produto->id)) {
            return $skuCandidate;
        }

        return null;
    }

    private function resolveSkuParaVariacao(ProdutoVariacao $v): ?string
    {
        $produtoId = $v->produto_id;

        // 1. Tenta `referencia` se não vazia e única por produto
        $ref = trim($v->referencia ?? '');
        if ($ref !== '') {
            $skuCandidate = $this->normalizaSku($ref);
            if ($this->skuDisponivelEmVariacao($skuCandidate, $produtoId, $v->id)) {
                return $skuCandidate;
            }
        }

        // 2. Gera: V-{produto_id}-{variacao_id}
        $skuCandidate = "V-{$produtoId}-{$v->id}";
        if ($this->skuDisponivelEmVariacao($skuCandidate, $produtoId, $v->id)) {
            return $skuCandidate;
        }

        return null;
    }

    private function normalizaSku(string $valor): string
    {
        $val = strtoupper(trim($valor));
        // Mantém apenas caracteres válidos: A-Z, 0-9, -, _
        $val = preg_replace('/[^A-Z0-9\-_]/', '-', $val);
        // Remove hífens múltiplos consecutivos
        $val = preg_replace('/-{2,}/', '-', $val);
        return substr(trim($val, '-'), 0, 40);
    }

    private function skuDisponivelEmProduto(string $sku, int $empresaId, int $ignorarId): bool
    {
        return !Produto::where('empresa_id', $empresaId)
            ->where('sku', $sku)
            ->where('id', '!=', $ignorarId)
            ->exists();
    }

    private function skuDisponivelEmVariacao(string $sku, int $produtoId, int $ignorarId): bool
    {
        return !ProdutoVariacao::where('produto_id', $produtoId)
            ->where('sku', $sku)
            ->where('id', '!=', $ignorarId)
            ->exists();
    }
}
