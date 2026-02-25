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
        Schema::create('item_carrinho_adicional_cardapios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_carrinho_id')->index('item_carrinho_adicional_cardapios_item_carrinho_id_foreign');
            $table->unsignedBigInteger('adicional_id')->index('item_carrinho_adicional_cardapios_adicional_id_foreign');
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
        Schema::dropIfExists('item_carrinho_adicional_cardapios');
    }
};
