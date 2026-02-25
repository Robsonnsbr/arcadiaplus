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
        Schema::create('carrinho_cardapios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('carrinho_cardapios_empresa_id_foreign');
            $table->string('session_cart_cardapio', 30);
            $table->decimal('valor_total', 10);
            $table->string('observacao', 200)->nullable();
            $table->enum('estado', ['pendente', 'finalizado']);
            $table->timestamps();
            $table->string('cliente_nome', 50)->nullable();
            $table->string('session_cart_user', 30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carrinho_cardapios');
    }
};
