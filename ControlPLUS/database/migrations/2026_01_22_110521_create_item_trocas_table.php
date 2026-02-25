<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_trocas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('troca_id')->index('item_trocas_troca_id_foreign');
            $table->unsignedBigInteger('produto_id')->index('item_trocas_produto_id_foreign');
            $table->decimal('quantidade', 7, 3);
            $table->timestamps();
            $table->decimal('valor_unitario', 10)->nullable();
            $table->decimal('sub_total', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_trocas');
    }
};
