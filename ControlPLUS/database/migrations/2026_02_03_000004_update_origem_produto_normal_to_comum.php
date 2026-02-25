<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('produtos') || !Schema::hasColumn('produtos', 'origem_produto')) {
            return;
        }

        DB::table('produtos')
            ->where(function ($q) {
                $q->whereNull('origem_produto')
                    ->orWhere('origem_produto', 'normal');
            })
            ->whereNotIn('tipo_produto', ['trade_in', 'avaliacao'])
            ->update(['origem_produto' => 'comum']);

        try {
            $driver = DB::getDriverName();
            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                DB::statement("ALTER TABLE `produtos` ALTER COLUMN `origem_produto` SET DEFAULT 'comum'");
            } elseif ($driver === 'pgsql') {
                DB::statement("ALTER TABLE produtos ALTER COLUMN origem_produto SET DEFAULT 'comum'");
            }
        } catch (\Throwable $e) {
            // best-effort; keep app-level defaults working even if this fails
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('produtos') || !Schema::hasColumn('produtos', 'origem_produto')) {
            return;
        }

        DB::table('produtos')
            ->where('origem_produto', 'comum')
            ->update(['origem_produto' => 'normal']);

        try {
            $driver = DB::getDriverName();
            if (in_array($driver, ['mysql', 'mariadb'], true)) {
                DB::statement("ALTER TABLE `produtos` ALTER COLUMN `origem_produto` SET DEFAULT 'normal'");
            } elseif ($driver === 'pgsql') {
                DB::statement("ALTER TABLE produtos ALTER COLUMN origem_produto SET DEFAULT 'normal'");
            }
        } catch (\Throwable $e) {
            // best-effort
        }
    }
};
