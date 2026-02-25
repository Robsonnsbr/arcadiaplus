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
        Schema::create('woocommerce_item_pedidos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pedido_id')->index('woocommerce_item_pedidos_pedido_id_foreign');
            $table->unsignedBigInteger('produto_id')->nullable()->index('woocommerce_item_pedidos_produto_id_foreign');
            $table->string('item_id', 20);
            $table->string('item_nome', 100);
            $table->decimal('quantidade');
            $table->decimal('valor_unitario', 12);
            $table->decimal('sub_total', 12);
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
        Schema::dropIfExists('woocommerce_item_pedidos');
    }
};
