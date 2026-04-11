<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('tradein_credit_movements')) {
            return;
        }

        if (!$this->indexExists('tradein_credit_movements', 'tcm_uniq_credit_origin')) {
            DB::statement(
                'CREATE UNIQUE INDEX tcm_uniq_credit_origin ON tradein_credit_movements (empresa_id, origem_tipo, origem_id, tipo)'
            );
        }

        if (!$this->indexExists('tradein_credit_movements', 'tcm_uniq_debit_origin')) {
            DB::statement(
                'CREATE UNIQUE INDEX tcm_uniq_debit_origin ON tradein_credit_movements (empresa_id, cliente_id, origem_tipo, origem_id, tipo)'
            );
        }
    }

    public function down()
    {
        if (!Schema::hasTable('tradein_credit_movements')) {
            return;
        }

        if ($this->indexExists('tradein_credit_movements', 'tcm_uniq_debit_origin')) {
            DB::statement('DROP INDEX IF EXISTS tcm_uniq_debit_origin');
        }

        if ($this->indexExists('tradein_credit_movements', 'tcm_uniq_credit_origin')) {
            DB::statement('DROP INDEX IF EXISTS tcm_uniq_credit_origin');
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $result = DB::select(
            "SELECT 1 FROM pg_indexes WHERE tablename = ? AND indexname = ? LIMIT 1",
            [$table, $indexName]
        );

        return !empty($result);
    }
};
