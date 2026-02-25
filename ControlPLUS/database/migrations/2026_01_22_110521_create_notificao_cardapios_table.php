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
        Schema::create('notificao_cardapios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('notificao_cardapios_empresa_id_foreign');
            $table->unsignedBigInteger('pedido_id')->nullable()->index('notificao_cardapios_pedido_id_foreign');
            $table->string('mesa', 20)->nullable();
            $table->string('comanda', 20)->nullable();
            $table->enum('tipo', ['garcom', 'fechar_mesa']);
            $table->string('tipo_pagamento', 2)->nullable();
            $table->integer('avaliacao')->nullable();
            $table->string('observacao', 150)->nullable();
            $table->boolean('status')->default(false);
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
        Schema::dropIfExists('notificao_cardapios');
    }
};
