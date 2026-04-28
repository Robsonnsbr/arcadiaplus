<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $this->addDepositoIdToRetiradaEstoques();
        $this->addDepositoIdToMovimentacaoProdutos();

        $this->backfillRetiradaEstoques();
    }

    public function down()
    {
        $this->dropDepositoIdFromMovimentacaoProdutos();
        $this->dropDepositoIdFromRetiradaEstoques();
    }

    private function addDepositoIdToRetiradaEstoques(): void
    {
        if (!Schema::hasTable('retirada_estoques')) {
            return;
        }

        if (!Schema::hasColumn('retirada_estoques', 'deposito_id')) {
            Schema::table('retirada_estoques', function (Blueprint $table) {
                $table->unsignedBigInteger('deposito_id')->nullable()->after('local_id');
            });
        }

        if (!$this->indexExists('retirada_estoques', 'retirada_estoques_deposito_id_foreign')) {
            Schema::table('retirada_estoques', function (Blueprint $table) {
                $table->index('deposito_id', 'retirada_estoques_deposito_id_foreign');
            });
        }

        if (!$this->foreignKeyExists('retirada_estoques', 'retirada_estoques_deposito_id_foreign_fk')) {
            Schema::table('retirada_estoques', function (Blueprint $table) {
                $table->foreign('deposito_id', 'retirada_estoques_deposito_id_foreign_fk')
                    ->references('id')
                    ->on('depositos')
                    ->nullOnDelete();
            });
        }
    }

    private function addDepositoIdToMovimentacaoProdutos(): void
    {
        if (!Schema::hasTable('movimentacao_produtos')) {
            return;
        }

        if (!Schema::hasColumn('movimentacao_produtos', 'deposito_id')) {
            Schema::table('movimentacao_produtos', function (Blueprint $table) {
                $table->unsignedBigInteger('deposito_id')->nullable()->after('produto_variacao_id');
            });
        }

        if (!$this->indexExists('movimentacao_produtos', 'movimentacao_produtos_deposito_id_foreign')) {
            Schema::table('movimentacao_produtos', function (Blueprint $table) {
                $table->index('deposito_id', 'movimentacao_produtos_deposito_id_foreign');
            });
        }

        if (!$this->indexExists('movimentacao_produtos', 'movimentacao_produtos_produto_deposito_idx')) {
            Schema::table('movimentacao_produtos', function (Blueprint $table) {
                $table->index(['produto_id', 'deposito_id', 'created_at'], 'movimentacao_produtos_produto_deposito_idx');
            });
        }

        if (!$this->foreignKeyExists('movimentacao_produtos', 'movimentacao_produtos_deposito_id_foreign_fk')) {
            Schema::table('movimentacao_produtos', function (Blueprint $table) {
                $table->foreign('deposito_id', 'movimentacao_produtos_deposito_id_foreign_fk')
                    ->references('id')
                    ->on('depositos')
                    ->nullOnDelete();
            });
        }
    }

    private function backfillRetiradaEstoques(): void
    {
        if (!Schema::hasTable('retirada_estoques') || !Schema::hasColumn('retirada_estoques', 'deposito_id')) {
            return;
        }

        $depositoPorLocal = DB::table('depositos')
            ->where('padrao', 1)
            ->pluck('id', 'local_id');

        if ($depositoPorLocal->isEmpty()) {
            return;
        }

        DB::table('retirada_estoques')
            ->select('id', 'local_id')
            ->whereNull('deposito_id')
            ->whereNotNull('local_id')
            ->orderBy('id')
            ->chunkById(1000, function ($rows) use ($depositoPorLocal) {
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
                        DB::table('retirada_estoques')
                            ->whereIn('id', $chunkIds)
                            ->whereNull('deposito_id')
                            ->update(['deposito_id' => $depositoId]);
                    }
                }
            });
    }

    private function dropDepositoIdFromRetiradaEstoques(): void
    {
        if (!Schema::hasTable('retirada_estoques') || !Schema::hasColumn('retirada_estoques', 'deposito_id')) {
            return;
        }

        if ($this->foreignKeyExists('retirada_estoques', 'retirada_estoques_deposito_id_foreign_fk')) {
            Schema::table('retirada_estoques', function (Blueprint $table) {
                $table->dropForeign('retirada_estoques_deposito_id_foreign_fk');
            });
        }

        if ($this->indexExists('retirada_estoques', 'retirada_estoques_deposito_id_foreign')) {
            Schema::table('retirada_estoques', function (Blueprint $table) {
                $table->dropIndex('retirada_estoques_deposito_id_foreign');
            });
        }

        Schema::table('retirada_estoques', function (Blueprint $table) {
            $table->dropColumn('deposito_id');
        });
    }

    private function dropDepositoIdFromMovimentacaoProdutos(): void
    {
        if (!Schema::hasTable('movimentacao_produtos') || !Schema::hasColumn('movimentacao_produtos', 'deposito_id')) {
            return;
        }

        if ($this->foreignKeyExists('movimentacao_produtos', 'movimentacao_produtos_deposito_id_foreign_fk')) {
            Schema::table('movimentacao_produtos', function (Blueprint $table) {
                $table->dropForeign('movimentacao_produtos_deposito_id_foreign_fk');
            });
        }

        if ($this->indexExists('movimentacao_produtos', 'movimentacao_produtos_produto_deposito_idx')) {
            Schema::table('movimentacao_produtos', function (Blueprint $table) {
                $table->dropIndex('movimentacao_produtos_produto_deposito_idx');
            });
        }

        if ($this->indexExists('movimentacao_produtos', 'movimentacao_produtos_deposito_id_foreign')) {
            Schema::table('movimentacao_produtos', function (Blueprint $table) {
                $table->dropIndex('movimentacao_produtos_deposito_id_foreign');
            });
        }

        Schema::table('movimentacao_produtos', function (Blueprint $table) {
            $table->dropColumn('deposito_id');
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        $result = DB::select("SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ? LIMIT 1", [$table, $index]);
        return !empty($result);
    }

    private function foreignKeyExists(string $table, string $constraint): bool
    {
        $result = DB::select("SELECT 1 FROM information_schema.table_constraints WHERE table_name = ? AND constraint_name = ? AND constraint_type = 'FOREIGN KEY' LIMIT 1", [$table, $constraint]);
        return !empty($result);
    }
};
