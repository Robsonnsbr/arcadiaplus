<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('produtos') || !Schema::hasColumn('produtos', 'tipo_produto')) {
            return;
        }

        DB::table('produtos')
            ->where('tipo_produto', 'avaliacao')
            ->update(['tipo_produto' => 'trade_in']);
    }

    public function down(): void
    {
        if (!Schema::hasTable('produtos') || !Schema::hasColumn('produtos', 'tipo_produto')) {
            return;
        }

        DB::table('produtos')
            ->where('tipo_produto', 'trade_in')
            ->update(['tipo_produto' => 'avaliacao']);
    }
};
