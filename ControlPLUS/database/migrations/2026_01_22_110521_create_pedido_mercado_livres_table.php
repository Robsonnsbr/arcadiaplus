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
        Schema::create('pedido_mercado_livres', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('pedido_mercado_livres_empresa_id_foreign');
            $table->unsignedBigInteger('cliente_id')->nullable()->index('pedido_mercado_livres_cliente_id_foreign');
            $table->bigInteger('_id');
            $table->string('tipo_pagamento', 50);
            $table->string('status', 20);
            $table->decimal('total', 10);
            $table->decimal('valor_entrega', 10);
            $table->string('nickname', 50);
            $table->bigInteger('seller_id');
            $table->string('entrega_id', 20)->nullable();
            $table->timestamp('data_pedido');
            $table->string('comentario', 200)->nullable();
            $table->integer('nfe_id')->nullable();
            $table->string('rua_entrega', 100)->nullable();
            $table->string('numero_entrega', 10)->nullable();
            $table->string('cep_entrega', 10)->nullable();
            $table->string('bairro_entrega', 50)->nullable();
            $table->string('cidade_entrega', 100)->nullable();
            $table->string('comentario_entrega', 200)->nullable();
            $table->string('codigo_rastreamento', 30)->nullable();
            $table->string('cliente_nome', 50)->nullable();
            $table->string('cliente_documento', 20)->nullable();
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
        Schema::dropIfExists('pedido_mercado_livres');
    }
};
