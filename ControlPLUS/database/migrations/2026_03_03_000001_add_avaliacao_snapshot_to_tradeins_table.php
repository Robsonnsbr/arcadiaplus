<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tradeins')) {
            return;
        }

        if (!Schema::hasColumn('tradeins', 'avaliacao_snapshot')) {
            Schema::table('tradeins', function (Blueprint $table) {
                $table->json('avaliacao_snapshot')->nullable()->after('term_generated_at');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('tradeins')) {
            return;
        }

        if (Schema::hasColumn('tradeins', 'avaliacao_snapshot')) {
            Schema::table('tradeins', function (Blueprint $table) {
                $table->dropColumn('avaliacao_snapshot');
            });
        }
    }
};
