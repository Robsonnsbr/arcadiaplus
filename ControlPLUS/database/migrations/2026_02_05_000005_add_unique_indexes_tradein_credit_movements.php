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
            DB::statement('DROP INDEX tcm_uniq_debit_origin ON tradein_credit_movements');
        }

        if ($this->indexExists('tradein_credit_movements', 'tcm_uniq_credit_origin')) {
            DB::statement('DROP INDEX tcm_uniq_credit_origin ON tradein_credit_movements');
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $result = DB::select(
            'SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ? LIMIT 1',
            [$table, $indexName]
        );

        return !empty($result);
    }
};
