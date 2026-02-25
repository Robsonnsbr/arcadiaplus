<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('produtos')) {
            return;
        }

        Schema::table('produtos', function (Blueprint $table) {
            if (!Schema::hasColumn('produtos', 'origem_produto')) {
                $table->string('origem_produto', 20)->default('comum')->index();
            }
            if (!Schema::hasColumn('produtos', 'status_avaliacao')) {
                $table->string('status_avaliacao', 20)->nullable()->index();
            }
        });

        if (!Schema::hasColumn('produtos', 'tipo_produto')) {
            return;
        }

        // Origin: best-effort from existing tipo_produto (historical overwrites to "novo" can't be recovered).
        DB::table('produtos')
            ->whereIn('tipo_produto', ['trade_in', 'avaliacao'])
            ->update(['origem_produto' => 'trade_in']);

        DB::table('produtos')
            ->whereNotIn('tipo_produto', ['trade_in', 'avaliacao'])
            ->update(['origem_produto' => 'comum']);

        // Status: legacy "trade_in"/"avaliacao" rows are pending until explicitly approved.
        DB::table('produtos')
            ->where('origem_produto', 'trade_in')
            ->whereNull('status_avaliacao')
            ->update(['status_avaliacao' => 'pendente']);
    }

    public function down(): void
    {
        if (!Schema::hasTable('produtos')) {
            return;
        }

        Schema::table('produtos', function (Blueprint $table) {
            if (Schema::hasColumn('produtos', 'status_avaliacao')) {
                $table->dropColumn('status_avaliacao');
            }
            if (Schema::hasColumn('produtos', 'origem_produto')) {
                $table->dropColumn('origem_produto');
            }
        });
    }
};
