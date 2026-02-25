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
        Schema::create('carrinho_deliveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cliente_id')->nullable()->index('carrinho_deliveries_cliente_id_foreign');
            $table->unsignedBigInteger('empresa_id')->index('carrinho_deliveries_empresa_id_foreign');
            $table->unsignedBigInteger('endereco_id')->nullable()->index('carrinho_deliveries_endereco_id_foreign');
            $table->enum('estado', ['pendente', 'finalizado']);
            $table->decimal('valor_total', 10);
            $table->decimal('valor_desconto', 10);
            $table->string('cupom', 6)->nullable();
            $table->string('fone', 20)->nullable();
            $table->decimal('valor_frete', 10);
            $table->string('session_cart_delivery', 30);
            $table->timestamps();
            $table->string('observacao', 200)->nullable();
            $table->string('tipo_pagamento', 20)->nullable();
            $table->decimal('troco_para', 10)->nullable();
            $table->enum('tipo_entrega', ['delivery', 'retirada'])->nullable();
            $table->integer('funcionario_id_agendamento')->nullable();
            $table->string('inicio_agendamento', 5)->nullable();
            $table->string('fim_agendamento', 5)->nullable();
            $table->date('data_agendamento')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carrinho_deliveries');
    }
};
