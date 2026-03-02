<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $indexName = 'estoques_produto_variacao_local_idx';

    public function up()
    {
        if (!Schema::hasTable('estoques')) {
            return;
        }

        if (!Schema::hasColumn('estoques', 'produto_id') || !Schema::hasColumn('estoques', 'produto_variacao_id') || !Schema::hasColumn('estoques', 'local_id')) {
            return;
        }

        if (!$this->indexExists('estoques', $this->indexName)) {
            Schema::table('estoques', function (Blueprint $table) {
                $table->index(['produto_id', 'produto_variacao_id', 'local_id'], 'estoques_produto_variacao_local_idx');
            });
        }
    }

    public function down()
    {
        if (!Schema::hasTable('estoques')) {
            return;
        }

        if ($this->indexExists('estoques', $this->indexName)) {
            Schema::table('estoques', function (Blueprint $table) {
                $table->dropIndex('estoques_produto_variacao_local_idx');
            });
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $result = DB::selectOne(
            'SELECT COUNT(1) AS total
             FROM information_schema.statistics
             WHERE table_schema = DATABASE()
               AND table_name = ?
               AND index_name = ?',
            [$table, $index]
        );

        return $result && (int)$result->total > 0;
    }
};
