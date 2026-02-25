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
        Schema::create('item_venda_suspensas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('venda_id')->nullable()->index('item_venda_suspensas_venda_id_foreign');
            $table->unsignedBigInteger('produto_id')->nullable()->index('item_venda_suspensas_produto_id_foreign');
            $table->unsignedBigInteger('variacao_id')->nullable()->index('item_venda_suspensas_variacao_id_foreign');
            $table->decimal('quantidade', 7, 3);
            $table->decimal('valor_unitario', 10);
            $table->decimal('sub_total', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_venda_suspensas');
    }
};
