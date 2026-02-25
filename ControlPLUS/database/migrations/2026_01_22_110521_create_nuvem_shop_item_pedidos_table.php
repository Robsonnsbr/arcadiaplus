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
        Schema::create('nuvem_shop_item_pedidos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('produto_id')->nullable()->index('nuvem_shop_item_pedidos_produto_id_foreign');
            $table->unsignedBigInteger('pedido_id')->nullable()->index('nuvem_shop_item_pedidos_pedido_id_foreign');
            $table->decimal('quantidade');
            $table->decimal('valor_unitario', 10);
            $table->decimal('sub_total', 10);
            $table->string('nome', 100);
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
        Schema::dropIfExists('nuvem_shop_item_pedidos');
    }
};
