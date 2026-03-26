<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const DEFAULT_NOME = 'Depósito Padrão';

    public function up()
    {
        $this->addDepositoIdToEstoques();
        $this->addDepositoIdToProdutoUnicos();
        $this->addDepositoIdToEstoqueStatusSaldos();

        $this->backfillDepositoIds();
    }

    public function down()
    {
        $this->dropDepositoIdFromEstoqueStatusSaldos();
        $this->dropDepositoIdFromProdutoUnicos();
        $this->dropDepositoIdFromEstoques();
    }

    private function addDepositoIdToEstoques(): void
    {
        if (!Schema::hasTable('estoques')) {
            return;
        }

        if (!Schema::hasColumn('estoques', 'deposito_id')) {
            Schema::table('estoques', function (Blueprint $table) {
                $table->unsignedBigInteger('deposito_id')->nullable()->after('local_id');
            });
        }

        if (!$this->indexExists('estoques', 'estoques_deposito_id_foreign')) {
            Schema::table('estoques', function (Blueprint $table) {
                $table->index('deposito_id', 'estoques_deposito_id_foreign');
            });
        }

        if (!$this->indexExists('estoques', 'estoques_produto_variacao_deposito_idx')) {
            Schema::table('estoques', function (Blueprint $table) {
                $table->index(['produto_id', 'produto_variacao_id', 'deposito_id'], 'estoques_produto_variacao_deposito_idx');
            });
        }

        if (!$this->foreignKeyExists('estoques', 'estoques_deposito_id_foreign_fk')) {
            Schema::table('estoques', function (Blueprint $table) {
                $table->foreign('deposito_id', 'estoques_deposito_id_foreign_fk')
                    ->references('id')
                    ->on('depositos')
                    ->nullOnDelete();
            });
        }
    }

    private function addDepositoIdToProdutoUnicos(): void
    {
        if (!Schema::hasTable('produto_unicos')) {
            return;
        }

        if (!Schema::hasColumn('produto_unicos', 'deposito_id')) {
            Schema::table('produto_unicos', function (Blueprint $table) {
                $table->unsignedBigInteger('deposito_id')->nullable()->after('local_id');
            });
        }

        if (!$this->indexExists('produto_unicos', 'produto_unicos_deposito_id_foreign')) {
            Schema::table('produto_unicos', function (Blueprint $table) {
                $table->index('deposito_id', 'produto_unicos_deposito_id_foreign');
            });
        }

        if (!$this->indexExists('produto_unicos', 'produto_unicos_produto_estoque_status_deposito_idx')) {
            Schema::table('produto_unicos', function (Blueprint $table) {
                $table->index(
                    ['produto_id', 'em_estoque', 'status_key', 'deposito_id'],
                    'produto_unicos_produto_estoque_status_deposito_idx'
                );
            });
        }

        if (!$this->foreignKeyExists('produto_unicos', 'produto_unicos_deposito_id_foreign_fk')) {
            Schema::table('produto_unicos', function (Blueprint $table) {
                $table->foreign('deposito_id', 'produto_unicos_deposito_id_foreign_fk')
                    ->references('id')
                    ->on('depositos')
                    ->nullOnDelete();
            });
        }
    }

    private function addDepositoIdToEstoqueStatusSaldos(): void
    {
        if (!Schema::hasTable('estoque_status_saldos')) {
            return;
        }

        if (!Schema::hasColumn('estoque_status_saldos', 'deposito_id')) {
            Schema::table('estoque_status_saldos', function (Blueprint $table) {
                $table->unsignedBigInteger('deposito_id')->nullable()->after('local_id');
            });
        }

        if (!$this->indexExists('estoque_status_saldos', 'estoque_status_saldos_deposito_id_foreign')) {
            Schema::table('estoque_status_saldos', function (Blueprint $table) {
                $table->index('deposito_id', 'estoque_status_saldos_deposito_id_foreign');
            });
        }

        if (!$this->indexExists('estoque_status_saldos', 'estoque_status_saldos_produto_deposito_idx')) {
            Schema::table('estoque_status_saldos', function (Blueprint $table) {
                $table->index(['produto_id', 'deposito_id'], 'estoque_status_saldos_produto_deposito_idx');
            });
        }

        if (!$this->indexExists('estoque_status_saldos', 'estoque_status_saldos_empresa_deposito_status_idx')) {
            Schema::table('estoque_status_saldos', function (Blueprint $table) {
                $table->index(
                    ['empresa_id', 'deposito_id', 'status_key'],
                    'estoque_status_saldos_empresa_deposito_status_idx'
                );
            });
        }

        if (!$this->foreignKeyExists('estoque_status_saldos', 'estoque_status_saldos_deposito_id_foreign_fk')) {
            Schema::table('estoque_status_saldos', function (Blueprint $table) {
                $table->foreign('deposito_id', 'estoque_status_saldos_deposito_id_foreign_fk')
                    ->references('id')
                    ->on('depositos')
                    ->nullOnDelete();
            });
        }
    }

    private function backfillDepositoIds(): void
    {
        if (!Schema::hasTable('depositos')) {
            return;
        }

        $this->ensureDepositosPadraoParaLocaisEmUso();

        $depositoPorLocal = DB::table('depositos')
            ->where('nome', self::DEFAULT_NOME)
            ->pluck('id', 'local_id');

        if ($depositoPorLocal->isEmpty()) {
            return;
        }

        $this->backfillTable('estoques', $depositoPorLocal);
        $this->backfillTable('produto_unicos', $depositoPorLocal);
        $this->backfillTable('estoque_status_saldos', $depositoPorLocal);
    }

    private function ensureDepositosPadraoParaLocaisEmUso(): void
    {
        $locaisIds = collect();

        if (Schema::hasTable('estoques') && Schema::hasColumn('estoques', 'local_id')) {
            $locaisIds = $locaisIds->merge(DB::table('estoques')->whereNotNull('local_id')->pluck('local_id'));
        }

        if (Schema::hasTable('produto_unicos') && Schema::hasColumn('produto_unicos', 'local_id')) {
            $locaisIds = $locaisIds->merge(DB::table('produto_unicos')->whereNotNull('local_id')->pluck('local_id'));
        }

        if (Schema::hasTable('estoque_status_saldos') && Schema::hasColumn('estoque_status_saldos', 'local_id')) {
            $locaisIds = $locaisIds->merge(DB::table('estoque_status_saldos')->whereNotNull('local_id')->pluck('local_id'));
        }

        $locaisIds = $locaisIds
            ->filter()
            ->map(function ($id) {
                return (int)$id;
            })
            ->unique()
            ->values();

        if ($locaisIds->isEmpty()) {
            return;
        }

        $locais = DB::table('localizacaos')
            ->whereIn('id', $locaisIds->all())
            ->select('id', 'empresa_id', 'descricao', 'status')
            ->get();

        foreach ($locais as $local) {
            $descricaoLocal = trim((string)($local->descricao ?? ''));
            $descricaoDeposito = $descricaoLocal !== ''
                ? "Depósito padrão vinculado à unidade {$descricaoLocal}"
                : 'Depósito padrão vinculado à unidade';

            $existente = DB::table('depositos')
                ->where('local_id', (int)$local->id)
                ->where('nome', self::DEFAULT_NOME)
                ->first();

            if ($existente) {
                continue;
            }

            DB::table('depositos')->insert([
                'empresa_id' => (int)$local->empresa_id,
                'local_id' => (int)$local->id,
                'nome' => self::DEFAULT_NOME,
                'descricao' => $descricaoDeposito,
                'ativo' => (int)$local->status === 1,
                'padrao' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function backfillTable(string $table, $depositoPorLocal): void
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, 'deposito_id') || !Schema::hasColumn($table, 'local_id')) {
            return;
        }

        DB::table($table)
            ->select('id', 'local_id')
            ->whereNull('deposito_id')
            ->whereNotNull('local_id')
            ->orderBy('id')
            ->chunkById(1000, function ($rows) use ($table, $depositoPorLocal) {
                $idsPorDeposito = [];

                foreach ($rows as $row) {
                    $localId = (int)$row->local_id;
                    $depositoId = isset($depositoPorLocal[$localId]) ? (int)$depositoPorLocal[$localId] : null;
                    if (!$depositoId) {
                        continue;
                    }
                    $idsPorDeposito[$depositoId][] = (int)$row->id;
                }

                foreach ($idsPorDeposito as $depositoId => $ids) {
                    foreach (array_chunk($ids, 500) as $chunkIds) {
                        DB::table($table)
                            ->whereIn('id', $chunkIds)
                            ->whereNull('deposito_id')
                            ->update(['deposito_id' => $depositoId]);
                    }
                }
            });
    }

    private function dropDepositoIdFromEstoques(): void
    {
        if (!Schema::hasTable('estoques') || !Schema::hasColumn('estoques', 'deposito_id')) {
            return;
        }

        if ($this->foreignKeyExists('estoques', 'estoques_deposito_id_foreign_fk')) {
            Schema::table('estoques', function (Blueprint $table) {
                $table->dropForeign('estoques_deposito_id_foreign_fk');
            });
        }

        if ($this->indexExists('estoques', 'estoques_produto_variacao_deposito_idx')) {
            Schema::table('estoques', function (Blueprint $table) {
                $table->dropIndex('estoques_produto_variacao_deposito_idx');
            });
        }

        if ($this->indexExists('estoques', 'estoques_deposito_id_foreign')) {
            Schema::table('estoques', function (Blueprint $table) {
                $table->dropIndex('estoques_deposito_id_foreign');
            });
        }

        Schema::table('estoques', function (Blueprint $table) {
            $table->dropColumn('deposito_id');
        });
    }

    private function dropDepositoIdFromProdutoUnicos(): void
    {
        if (!Schema::hasTable('produto_unicos') || !Schema::hasColumn('produto_unicos', 'deposito_id')) {
            return;
        }

        if ($this->foreignKeyExists('produto_unicos', 'produto_unicos_deposito_id_foreign_fk')) {
            Schema::table('produto_unicos', function (Blueprint $table) {
                $table->dropForeign('produto_unicos_deposito_id_foreign_fk');
            });
        }

        if ($this->indexExists('produto_unicos', 'produto_unicos_produto_estoque_status_deposito_idx')) {
            Schema::table('produto_unicos', function (Blueprint $table) {
                $table->dropIndex('produto_unicos_produto_estoque_status_deposito_idx');
            });
        }

        if ($this->indexExists('produto_unicos', 'produto_unicos_deposito_id_foreign')) {
            Schema::table('produto_unicos', function (Blueprint $table) {
                $table->dropIndex('produto_unicos_deposito_id_foreign');
            });
        }

        Schema::table('produto_unicos', function (Blueprint $table) {
            $table->dropColumn('deposito_id');
        });
    }

    private function dropDepositoIdFromEstoqueStatusSaldos(): void
    {
        if (!Schema::hasTable('estoque_status_saldos') || !Schema::hasColumn('estoque_status_saldos', 'deposito_id')) {
            return;
        }

        if ($this->foreignKeyExists('estoque_status_saldos', 'estoque_status_saldos_deposito_id_foreign_fk')) {
            Schema::table('estoque_status_saldos', function (Blueprint $table) {
                $table->dropForeign('estoque_status_saldos_deposito_id_foreign_fk');
            });
        }

        if ($this->indexExists('estoque_status_saldos', 'estoque_status_saldos_empresa_deposito_status_idx')) {
            Schema::table('estoque_status_saldos', function (Blueprint $table) {
                $table->dropIndex('estoque_status_saldos_empresa_deposito_status_idx');
            });
        }

        if ($this->indexExists('estoque_status_saldos', 'estoque_status_saldos_produto_deposito_idx')) {
            Schema::table('estoque_status_saldos', function (Blueprint $table) {
                $table->dropIndex('estoque_status_saldos_produto_deposito_idx');
            });
        }

        if ($this->indexExists('estoque_status_saldos', 'estoque_status_saldos_deposito_id_foreign')) {
            Schema::table('estoque_status_saldos', function (Blueprint $table) {
                $table->dropIndex('estoque_status_saldos_deposito_id_foreign');
            });
        }

        Schema::table('estoque_status_saldos', function (Blueprint $table) {
            $table->dropColumn('deposito_id');
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();
    }

    private function foreignKeyExists(string $table, string $constraint): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.table_constraints')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('constraint_name', $constraint)
            ->where('constraint_type', 'FOREIGN KEY')
            ->exists();
    }
};
