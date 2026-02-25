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
        Schema::create('item_pedido_mercado_livres', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pedido_id')->index('item_pedido_mercado_livres_pedido_id_foreign');
            $table->unsignedBigInteger('produto_id')->nullable()->index('item_pedido_mercado_livres_produto_id_foreign');
            $table->string('item_id', 20);
            $table->string('item_nome', 100);
            $table->string('condicao', 20);
            $table->string('variacao_id', 20)->nullable();
            $table->decimal('quantidade');
            $table->decimal('valor_unitario', 12);
            $table->decimal('sub_total', 12);
            $table->decimal('taxa_venda', 12);
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
        Schema::dropIfExists('item_pedido_mercado_livres');
    }
};
