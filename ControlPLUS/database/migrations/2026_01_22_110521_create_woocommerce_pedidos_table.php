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
        Schema::create('woocommerce_pedidos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('woocommerce_pedidos_empresa_id_foreign');
            $table->unsignedBigInteger('cliente_id')->nullable()->index('woocommerce_pedidos_cliente_id_foreign');
            $table->string('pedido_id', 30);
            $table->string('rua', 80)->nullable();
            $table->string('numero', 80)->nullable();
            $table->string('bairro', 50)->nullable();
            $table->string('cidade', 60)->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('cep', 10)->nullable();
            $table->decimal('total', 10);
            $table->decimal('valor_frete', 10);
            $table->decimal('desconto', 10);
            $table->string('observacao', 150)->nullable();
            $table->string('nome', 50);
            $table->string('email', 50);
            $table->string('documento', 20)->nullable();
            $table->integer('nfe_id')->nullable();
            $table->string('tipo_pagamento', 150)->nullable();
            $table->string('status', 30);
            $table->string('numero_pedido', 30);
            $table->string('data', 30);
            $table->integer('venda_id')->nullable();
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
        Schema::dropIfExists('woocommerce_pedidos');
    }
};
