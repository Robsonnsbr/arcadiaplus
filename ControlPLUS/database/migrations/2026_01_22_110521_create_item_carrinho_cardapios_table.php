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
        Schema::create('item_carrinho_cardapios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('carrinho_id')->index('item_carrinho_cardapios_carrinho_id_foreign');
            $table->unsignedBigInteger('produto_id')->nullable()->index('item_carrinho_cardapios_produto_id_foreign');
            $table->unsignedBigInteger('tamanho_id')->nullable()->index('item_carrinho_cardapios_tamanho_id_foreign');
            $table->decimal('quantidade', 8, 3);
            $table->decimal('valor_unitario', 10);
            $table->decimal('sub_total', 10, 3);
            $table->string('observacao', 50)->nullable();
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
        Schema::dropIfExists('item_carrinho_cardapios');
    }
};
