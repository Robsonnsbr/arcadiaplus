<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $this->addDepositoContextToTransferencias();
        $this->addDepositoContextToMovimentacoes();
        $this->backfillTransferencias();
        $this->expandMovimentacaoTipoTransacaoEnum();
    }

    public function down()
    {
        $this->shrinkMovimentacaoTipoTransacaoEnum();
        $this->dropDepositoContextFromMovimentacoes();
        $this->dropDepositoContextFromTransferencias();
    }

    private function addDepositoContextToTransferencias(): void
    {
        if (!Schema::hasTable('transferencia_estoques')) {
            return;
        }

        if (!Schema::hasColumn('transferencia_estoques', 'deposito_saida_id')) {
            Schema::table('transferencia_estoques', function (Blueprint $table) {
                $table->unsignedBigInteger('deposito_saida_id')->nullable()->after('local_saida_id');
            });
        }

        if (!Schema::hasColumn('transferencia_estoques', 'deposito_entrada_id')) {
            Schema::table('transferencia_estoques', function (Blueprint $table) {
                $table->unsignedBigInteger('deposito_entrada_id')->nullable()->after('local_entrada_id');
            });
        }

        if (!$this->indexExists('transferencia_estoques', 'transferencia_estoques_deposito_saida_id_idx')) {
            Schema::table('transferencia_estoques', function (Blueprint $table) {
                $table->index('deposito_saida_id', 'transferencia_estoques_deposito_saida_id_idx');
            });
        }

        if (!$this->indexExists('transferencia_estoques', 'transferencia_estoques_deposito_entrada_id_idx')) {
            Schema::table('transferencia_estoques', function (Blueprint $table) {
                $table->index('deposito_entrada_id', 'transferencia_estoques_deposito_entrada_id_idx');
            });
        }

        if (!$this->foreignKeyExists('transferencia_estoques', 'transferencia_estoques_deposito_saida_id_fk')) {
            Schema::table('transferencia_estoques', function (Blueprint $table) {
                $table->foreign('deposito_saida_id', 'transferencia_estoques_deposito_saida_id_fk')
                    ->references('id')
                    ->on('depositos')
                    ->nullOnDelete();
            });
        }

        if (!$this->foreignKeyExists('transferencia_estoques', 'transferencia_estoques_deposito_entrada_id_fk')) {
            Schema::table('transferencia_estoques', function (Blueprint $table) {
                $table->foreign('deposito_entrada_id', 'transferencia_estoques_deposito_entrada_id_fk')
                    ->references('id')
                    ->on('depositos')
                    ->nullOnDelete();
            });
        }
    }

    private function addDepositoContextToMovimentacoes(): void
    {
        if (!Schema::hasTable('movimentacao_produtos')) {
            return;
        }

        if (!Schema::hasColumn('movimentacao_produtos', 'deposito_origem_id')) {
            Schema::table('movimentacao_produtos', function (Blueprint $table) {
                $table->unsignedBigInteger('deposito_origem_id')->nullable()->after('deposito_id');
            });
        }

        if (!Schema::hasColumn('movimentacao_produtos', 'deposito_destino_id')) {
            Schema::table('movimentacao_produtos', function (Blueprint $table) {
                $table->unsignedBigInteger('deposito_destino_id')->nullable()->after('deposito_origem_id');
            });
        }

        if (!$this->indexExists('movimentacao_produtos', 'movimentacao_produtos_deposito_origem_id_idx')) {
            Schema::table('movimentacao_produtos', function (Blueprint $table) {
                $table->index('deposito_origem_id', 'movimentacao_produtos_deposito_origem_id_idx');
            });
        }

        if (!$this->indexExists('movimentacao_produtos', 'movimentacao_produtos_deposito_destino_id_idx')) {
            Schema::table('movimentacao_produtos', function (Blueprint $table) {
                $table->index('deposito_destino_id', 'movimentacao_produtos_deposito_destino_id_idx');
            });
        }

        if (!$this->foreignKeyExists('movimentacao_produtos', 'movimentacao_produtos_deposito_origem_id_fk')) {
            Schema::table('movimentacao_produtos', function (Blueprint $table) {
                $table->foreign('deposito_origem_id', 'movimentacao_produtos_deposito_origem_id_fk')
                    ->references('id')
                    ->on('depositos')
                    ->nullOnDelete();
            });
        }

        if (!$this->foreignKeyExists('movimentacao_produtos', 'movimentacao_produtos_deposito_destino_id_fk')) {
            Schema::table('movimentacao_produtos', function (Blueprint $table) {
                $table->foreign('deposito_destino_id', 'movimentacao_produtos_deposito_destino_id_fk')
                    ->references('id')
                    ->on('depositos')
                    ->nullOnDelete();
            });
        }
    }

    private function backfillTransferencias(): void
    {
        if (
            !Schema::hasTable('transferencia_estoques') ||
            !Schema::hasColumn('transferencia_estoques', 'deposito_saida_id') ||
            !Schema::hasColumn('transferencia_estoques', 'deposito_entrada_id')
        ) {
            return;
        }

        $localIds = DB::table('transferencia_estoques')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('deposito_saida_id')
                        ->whereNotNull('local_saida_id');
                })->orWhere(function ($q) {
                    $q->whereNull('deposito_entrada_id')
                        ->whereNotNull('local_entrada_id');
                });
            })
            ->select('local_saida_id', 'local_entrada_id')
            ->get()
            ->flatMap(function ($row) {
                return [$row->local_saida_id, $row->local_entrada_id];
            })
            ->filter()
            ->map(function ($id) {
                return (int)$id;
            })
            ->unique()
            ->values();

        if ($localIds->isEmpty()) {
            return;
        }

        $this->ensureDefaultDepositosForLocais($localIds->all());

        $depositoPorLocal = DB::table('depositos')
            ->where('padrao', 1)
            ->whereIn('local_id', $localIds->all())
            ->pluck('id', 'local_id');

        foreach ($depositoPorLocal as $localId => $depositoId) {
            DB::table('transferencia_estoques')
                ->where('local_saida_id', (int)$localId)
                ->whereNull('deposito_saida_id')
                ->update(['deposito_saida_id' => (int)$depositoId]);

            DB::table('transferencia_estoques')
                ->where('local_entrada_id', (int)$localId)
                ->whereNull('deposito_entrada_id')
                ->update(['deposito_entrada_id' => (int)$depositoId]);
        }
    }

    private function ensureDefaultDepositosForLocais(array $localIds): void
    {
        if (empty($localIds)) {
            return;
        }

        $now = now();
        $locais = DB::table('localizacaos')
            ->whereIn('id', $localIds)
            ->select('id', 'empresa_id', 'descricao', 'status')
            ->get();

        foreach ($locais as $local) {
            $deposito = DB::table('depositos')
                ->where('local_id', (int)$local->id)
                ->where('nome', 'Depósito Padrão')
                ->first();

            if ($deposito) {
                continue;
            }

            $descricaoLocal = trim((string)($local->descricao ?? ''));
            $descricaoDeposito = $descricaoLocal !== ''
                ? "Depósito padrão vinculado à unidade {$descricaoLocal}"
                : 'Depósito padrão vinculado à unidade';

            DB::table('depositos')->insert([
                'empresa_id' => (int)$local->empresa_id,
                'local_id' => (int)$local->id,
                'nome' => 'Depósito Padrão',
                'descricao' => $descricaoDeposito,
                'ativo' => (int)$local->status === 1 ? 1 : 0,
                'padrao' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function expandMovimentacaoTipoTransacaoEnum(): void
    {
        if (!Schema::hasTable('movimentacao_produtos')) {
            return;
        }

        DB::statement("
            ALTER TABLE movimentacao_produtos
            MODIFY tipo_transacao ENUM('venda_nfe', 'venda_nfce', 'compra', 'alteracao_estoque', 'transferencia_estoque') NOT NULL
        ");
    }

    private function shrinkMovimentacaoTipoTransacaoEnum(): void
    {
        if (!Schema::hasTable('movimentacao_produtos')) {
            return;
        }

        DB::table('movimentacao_produtos')
            ->where('tipo_transacao', 'transferencia_estoque')
            ->update(['tipo_transacao' => 'alteracao_estoque']);

        DB::statement("
            ALTER TABLE movimentacao_produtos
            MODIFY tipo_transacao ENUM('venda_nfe', 'venda_nfce', 'compra', 'alteracao_estoque') NOT NULL
        ");
    }

    private function dropDepositoContextFromTransferencias(): void
    {
        if (!Schema::hasTable('transferencia_estoques')) {
            return;
        }

        if (Schema::hasColumn('transferencia_estoques', 'deposito_saida_id')) {
            if ($this->foreignKeyExists('transferencia_estoques', 'transferencia_estoques_deposito_saida_id_fk')) {
                Schema::table('transferencia_estoques', function (Blueprint $table) {
                    $table->dropForeign('transferencia_estoques_deposito_saida_id_fk');
                });
            }

            if ($this->indexExists('transferencia_estoques', 'transferencia_estoques_deposito_saida_id_idx')) {
                Schema::table('transferencia_estoques', function (Blueprint $table) {
                    $table->dropIndex('transferencia_estoques_deposito_saida_id_idx');
                });
            }

            Schema::table('transferencia_estoques', function (Blueprint $table) {
                $table->dropColumn('deposito_saida_id');
            });
        }

        if (Schema::hasColumn('transferencia_estoques', 'deposito_entrada_id')) {
            if ($this->foreignKeyExists('transferencia_estoques', 'transferencia_estoques_deposito_entrada_id_fk')) {
                Schema::table('transferencia_estoques', function (Blueprint $table) {
                    $table->dropForeign('transferencia_estoques_deposito_entrada_id_fk');
                });
            }

            if ($this->indexExists('transferencia_estoques', 'transferencia_estoques_deposito_entrada_id_idx')) {
                Schema::table('transferencia_estoques', function (Blueprint $table) {
                    $table->dropIndex('transferencia_estoques_deposito_entrada_id_idx');
                });
            }

            Schema::table('transferencia_estoques', function (Blueprint $table) {
                $table->dropColumn('deposito_entrada_id');
            });
        }
    }

    private function dropDepositoContextFromMovimentacoes(): void
    {
        if (!Schema::hasTable('movimentacao_produtos')) {
            return;
        }

        if (Schema::hasColumn('movimentacao_produtos', 'deposito_origem_id')) {
            if ($this->foreignKeyExists('movimentacao_produtos', 'movimentacao_produtos_deposito_origem_id_fk')) {
                Schema::table('movimentacao_produtos', function (Blueprint $table) {
                    $table->dropForeign('movimentacao_produtos_deposito_origem_id_fk');
                });
            }

            if ($this->indexExists('movimentacao_produtos', 'movimentacao_produtos_deposito_origem_id_idx')) {
                Schema::table('movimentacao_produtos', function (Blueprint $table) {
                    $table->dropIndex('movimentacao_produtos_deposito_origem_id_idx');
                });
            }

            Schema::table('movimentacao_produtos', function (Blueprint $table) {
                $table->dropColumn('deposito_origem_id');
            });
        }

        if (Schema::hasColumn('movimentacao_produtos', 'deposito_destino_id')) {
            if ($this->foreignKeyExists('movimentacao_produtos', 'movimentacao_produtos_deposito_destino_id_fk')) {
                Schema::table('movimentacao_produtos', function (Blueprint $table) {
                    $table->dropForeign('movimentacao_produtos_deposito_destino_id_fk');
                });
            }

            if ($this->indexExists('movimentacao_produtos', 'movimentacao_produtos_deposito_destino_id_idx')) {
                Schema::table('movimentacao_produtos', function (Blueprint $table) {
                    $table->dropIndex('movimentacao_produtos_deposito_destino_id_idx');
                });
            }

            Schema::table('movimentacao_produtos', function (Blueprint $table) {
                $table->dropColumn('deposito_destino_id');
            });
        }
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
