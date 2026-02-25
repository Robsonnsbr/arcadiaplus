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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('pedidos_empresa_id_foreign');
            $table->unsignedBigInteger('cliente_id')->nullable()->index('pedidos_cliente_id_foreign');
            $table->unsignedBigInteger('funcionario_id')->nullable()->index('pedidos_funcionario_id_foreign');
            $table->string('cliente_nome', 100)->nullable();
            $table->string('cliente_fone', 20)->nullable();
            $table->string('comanda', 10);
            $table->string('observacao')->nullable();
            $table->string('tipo_pagamento', 2)->nullable();
            $table->string('mesa', 10)->nullable();
            $table->timestamp('data_fechamento')->nullable();
            $table->decimal('total', 12);
            $table->decimal('acrescimo', 10)->nullable()->default(0);
            $table->decimal('desconto', 10)->nullable()->default(0);
            $table->boolean('status')->default(true);
            $table->boolean('em_atendimento')->nullable()->default(true);
            $table->boolean('confirma_mesa')->nullable()->default(true);
            $table->integer('nfce_id')->nullable();
            $table->integer('mesa_id')->nullable();
            $table->string('local_pedido', 10)->nullable();
            $table->string('session_cart_cardapio', 30)->nullable();
            $table->string('session_cart_user', 30)->nullable();
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
        Schema::dropIfExists('pedidos');
    }
};
