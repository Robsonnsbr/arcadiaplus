<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $indexStatus = 'produto_unicos_produto_estoque_status_idx';
    private string $indexStatusLocal = 'produto_unicos_produto_estoque_status_local_idx';
    private string $fkLocal = 'produto_unicos_local_id_foreign';

    public function up()
    {
        if (!Schema::hasTable('produto_unicos')) {
            return;
        }

        if (!Schema::hasColumn('produto_unicos', 'local_id')) {
            Schema::table('produto_unicos', function (Blueprint $table) {
                $table->unsignedBigInteger('local_id')->nullable()->after('produto_id');
            });
        }

        if (!Schema::hasColumn('produto_unicos', 'status_key')) {
            Schema::table('produto_unicos', function (Blueprint $table) {
                $table->string('status_key', 40)->default('ATIVO')->after('em_estoque');
            });
        }

        if (Schema::hasColumn('produto_unicos', 'local_id') && !$this->foreignKeyExists('produto_unicos', $this->fkLocal)) {
            Schema::table('produto_unicos', function (Blueprint $table) {
                $table->foreign('local_id', 'produto_unicos_local_id_foreign')
                    ->references('id')
                    ->on('localizacaos')
                    ->nullOnDelete();
            });
        }

        if (!$this->indexExists('produto_unicos', $this->indexStatus)) {
            Schema::table('produto_unicos', function (Blueprint $table) {
                $table->index(['produto_id', 'em_estoque', 'status_key'], 'produto_unicos_produto_estoque_status_idx');
            });
        }

        if (!$this->indexExists('produto_unicos', $this->indexStatusLocal)) {
            Schema::table('produto_unicos', function (Blueprint $table) {
                $table->index(['produto_id', 'em_estoque', 'status_key', 'local_id'], 'produto_unicos_produto_estoque_status_local_idx');
            });
        }

        $this->backfillStatusKey();
        $this->backfillLocalId();
    }

    public function down()
    {
        if (!Schema::hasTable('produto_unicos')) {
            return;
        }

        if ($this->indexExists('produto_unicos', $this->indexStatusLocal)) {
            Schema::table('produto_unicos', function (Blueprint $table) {
                $table->dropIndex('produto_unicos_produto_estoque_status_local_idx');
            });
        }

        if ($this->indexExists('produto_unicos', $this->indexStatus)) {
            Schema::table('produto_unicos', function (Blueprint $table) {
                $table->dropIndex('produto_unicos_produto_estoque_status_idx');
            });
        }

        if (Schema::hasColumn('produto_unicos', 'local_id') && $this->foreignKeyExists('produto_unicos', $this->fkLocal)) {
            Schema::table('produto_unicos', function (Blueprint $table) {
                $table->dropForeign('produto_unicos_local_id_foreign');
            });
        }

        if (Schema::hasColumn('produto_unicos', 'local_id')) {
            Schema::table('produto_unicos', function (Blueprint $table) {
                $table->dropColumn('local_id');
            });
        }

        if (Schema::hasColumn('produto_unicos', 'status_key')) {
            Schema::table('produto_unicos', function (Blueprint $table) {
                $table->dropColumn('status_key');
            });
        }
    }

    private function backfillStatusKey(): void
    {
        DB::table('produto_unicos')
            ->whereNull('status_key')
            ->orWhere('status_key', '')
            ->update(['status_key' => 'ATIVO']);

        DB::table('produto_unicos')
            ->select('id', 'status_key')
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                foreach ($rows as $row) {
                    $normalized = $this->normalizeStatus((string)$row->status_key);
                    if ($normalized === null) {
                        $normalized = 'ATIVO';
                    }
                    if ($normalized !== $row->status_key) {
                        DB::table('produto_unicos')
                            ->where('id', $row->id)
                            ->update(['status_key' => $normalized]);
                    }
                }
            });
    }

    private function backfillLocalId(): void
    {
        $padraoCache = [];

        DB::table('produto_unicos')
            ->select('id', 'produto_id', 'nfe_id')
            ->where('em_estoque', 1)
            ->whereNull('local_id')
            ->orderBy('id')
            ->chunkById(1000, function ($rows) use (&$padraoCache) {
                $nfeIds = collect($rows)
                    ->pluck('nfe_id')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                $produtoIds = collect($rows)
                    ->pluck('produto_id')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                $nfeLocalMap = [];
                if (!empty($nfeIds)) {
                    $nfeLocalMap = DB::table('nves')
                        ->whereIn('id', $nfeIds)
                        ->pluck('local_id', 'id')
                        ->all();
                }

                $localEmpresaMap = [];
                $nfeLocaisIds = collect($nfeLocalMap)
                    ->values()
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
                if (!empty($nfeLocaisIds)) {
                    $localEmpresaMap = DB::table('localizacaos')
                        ->whereIn('id', $nfeLocaisIds)
                        ->pluck('empresa_id', 'id')
                        ->all();
                }

                $produtoEmpresaMap = [];
                if (!empty($produtoIds)) {
                    $produtoEmpresaMap = DB::table('produtos')
                        ->whereIn('id', $produtoIds)
                        ->pluck('empresa_id', 'id')
                        ->all();
                }

                $updatesByLocal = [];

                foreach ($rows as $row) {
                    $empresaId = isset($produtoEmpresaMap[$row->produto_id]) ? (int) $produtoEmpresaMap[$row->produto_id] : null;
                    $localId = null;
                    if ($row->nfe_id && isset($nfeLocalMap[$row->nfe_id]) && $nfeLocalMap[$row->nfe_id]) {
                        $localNfeId = (int) $nfeLocalMap[$row->nfe_id];
                        $empresaLocalNfe = isset($localEmpresaMap[$localNfeId]) ? (int) $localEmpresaMap[$localNfeId] : null;
                        if ($empresaId && $empresaLocalNfe && $empresaLocalNfe === $empresaId) {
                            $localId = $localNfeId;
                        }
                    }

                    if (!$localId) {
                        if ($empresaId) {
                            $localId = $this->resolveLocalPadraoEmpresa($empresaId, $padraoCache);
                        }
                    }

                    if ($localId) {
                        $updatesByLocal[$localId][] = (int) $row->id;
                    }
                }

                foreach ($updatesByLocal as $localId => $ids) {
                    foreach (array_chunk($ids, 500) as $chunkIds) {
                        DB::table('produto_unicos')
                            ->whereIn('id', $chunkIds)
                            ->whereNull('local_id')
                            ->update(['local_id' => (int)$localId]);
                    }
                }
            });
    }

    private function resolveLocalPadraoEmpresa(int $empresaId, array &$cache): ?int
    {
        if (array_key_exists($empresaId, $cache)) {
            return $cache[$empresaId];
        }

        $local = DB::table('localizacaos')
            ->where('empresa_id', $empresaId)
            ->orderByRaw(
                "CASE
                    WHEN descricao = 'PADRÃO' THEN 0
                    WHEN UPPER(TRIM(descricao)) = 'PADRAO' THEN 1
                    WHEN status = 1 THEN 2
                    ELSE 3
                END"
            )
            ->orderBy('id')
            ->first();

        $cache[$empresaId] = $local ? (int)$local->id : null;
        return $cache[$empresaId];
    }

    private function normalizeStatus(?string $status): ?string
    {
        if ($status === null) {
            return null;
        }

        $status = trim($status);
        if ($status === '') {
            return null;
        }

        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $status);
        if ($ascii !== false) {
            $status = $ascii;
        }

        $status = strtoupper($status);
        $status = preg_replace('/[^A-Z0-9]+/', '_', $status);
        $status = preg_replace('/_+/', '_', (string)$status);
        $status = trim((string)$status, '_');

        if ($status === '') {
            return null;
        }

        return substr($status, 0, 40);
    }

    private function indexExists(string $table, string $index): bool
    {
        $database = DB::getDatabaseName();

        $exists = DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();

        return (bool)$exists;
    }

    private function foreignKeyExists(string $table, string $constraint): bool
    {
        $database = DB::getDatabaseName();

        $exists = DB::table('information_schema.table_constraints')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('constraint_name', $constraint)
            ->where('constraint_type', 'FOREIGN KEY')
            ->exists();

        return (bool)$exists;
    }
};
