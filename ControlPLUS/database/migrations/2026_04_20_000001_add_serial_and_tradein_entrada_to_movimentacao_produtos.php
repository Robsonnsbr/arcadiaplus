<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add serial column to log which serial/IMEI unit was moved.
        Schema::table('movimentacao_produtos', function (Blueprint $table) {
            $table->string('serial', 120)->nullable()->after('estoque_atual');
        });

        // Extend the tipo_transacao enum to include the tradein stock-entry type.
        DB::statement("ALTER TABLE `movimentacao_produtos`
            MODIFY COLUMN `tipo_transacao`
            ENUM('venda_nfe','venda_nfce','compra','alteracao_estoque','tradein_entrada')
            NOT NULL");
    }

    public function down(): void
    {
        // Revert enum first (rows with 'tradein_entrada' will be lost if any exist).
        DB::statement("ALTER TABLE `movimentacao_produtos`
            MODIFY COLUMN `tipo_transacao`
            ENUM('venda_nfe','venda_nfce','compra','alteracao_estoque')
            NOT NULL");

        Schema::table('movimentacao_produtos', function (Blueprint $table) {
            $table->dropColumn('serial');
        });
    }
};
