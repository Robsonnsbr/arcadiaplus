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
        Schema::create('item_conta_empresas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('conta_id')->nullable()->index('item_conta_empresas_conta_id_foreign');
            $table->string('descricao', 150)->nullable();
            $table->integer('caixa_id')->nullable();
            $table->string('tipo_pagamento', 2);
            $table->decimal('valor', 16)->nullable();
            $table->decimal('saldo_atual', 16)->nullable();
            $table->enum('tipo', ['entrada', 'saida']);
            $table->timestamps();
            $table->integer('cliente_id')->nullable();
            $table->integer('fornecedor_id')->nullable();
            $table->string('numero_documento', 100)->nullable();
            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->integer('conta_pagar_id')->nullable();
            $table->integer('conta_receber_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_conta_empresas');
    }
};
