<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tradeins', function (Blueprint $table) {
            $table->unsignedBigInteger('produto_id')->nullable()->after('nome_item');
            $table->foreign('produto_id')->references('id')->on('produtos')->nullOnDelete();
        });

        Schema::table('tradein_inventory_items', function (Blueprint $table) {
            $table->unsignedBigInteger('produto_id')->nullable()->after('descricao_item');
            $table->foreign('produto_id')->references('id')->on('produtos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tradein_inventory_items', function (Blueprint $table) {
            $table->dropForeign(['produto_id']);
            $table->dropColumn('produto_id');
        });

        Schema::table('tradeins', function (Blueprint $table) {
            $table->dropForeign(['produto_id']);
            $table->dropColumn('produto_id');
        });
    }
};
