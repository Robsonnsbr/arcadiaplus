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

        // Dedupe credits by (empresa_id, origem_tipo, origem_id, tipo)
        $creditGroups = DB::table('tradein_credit_movements')
            ->select(
                'empresa_id',
                'origem_tipo',
                'origem_id',
                'tipo',
                DB::raw('COUNT(*) as total')
            )
            ->where('tipo', 'credit')
            ->groupBy('empresa_id', 'origem_tipo', 'origem_id', 'tipo')
            ->having('total', '>', 1)
            ->get();

        foreach ($creditGroups as $group) {
            $ids = DB::table('tradein_credit_movements')
                ->where('empresa_id', $group->empresa_id)
                ->where('origem_tipo', $group->origem_tipo)
                ->where('origem_id', $group->origem_id)
                ->where('tipo', $group->tipo)
                ->orderBy('id')
                ->pluck('id')
                ->toArray();

            $removeIds = array_slice($ids, 1);
            if (!empty($removeIds)) {
                DB::table('tradein_credit_movements')->whereIn('id', $removeIds)->delete();
                echo "tradein_credit_movements credit duplicates removed: " . implode(',', $removeIds) . PHP_EOL;
            }
        }

        // Dedupe debits by (empresa_id, cliente_id, origem_tipo, origem_id, tipo)
        $debitGroups = DB::table('tradein_credit_movements')
            ->select(
                'empresa_id',
                'cliente_id',
                'origem_tipo',
                'origem_id',
                'tipo',
                DB::raw('COUNT(*) as total')
            )
            ->where('tipo', 'debit')
            ->groupBy('empresa_id', 'cliente_id', 'origem_tipo', 'origem_id', 'tipo')
            ->having('total', '>', 1)
            ->get();

        foreach ($debitGroups as $group) {
            $ids = DB::table('tradein_credit_movements')
                ->where('empresa_id', $group->empresa_id)
                ->where('cliente_id', $group->cliente_id)
                ->where('origem_tipo', $group->origem_tipo)
                ->where('origem_id', $group->origem_id)
                ->where('tipo', $group->tipo)
                ->orderBy('id')
                ->pluck('id')
                ->toArray();

            $removeIds = array_slice($ids, 1);
            if (!empty($removeIds)) {
                DB::table('tradein_credit_movements')->whereIn('id', $removeIds)->delete();
                echo "tradein_credit_movements debit duplicates removed: " . implode(',', $removeIds) . PHP_EOL;
            }
        }
    }

    public function down()
    {
        // no-op: data cleanup cannot be safely reversed
    }
};
