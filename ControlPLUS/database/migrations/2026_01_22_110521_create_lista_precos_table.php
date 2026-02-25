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
        Schema::create('lista_precos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('lista_precos_empresa_id_foreign');
            $table->string('nome', 50);
            $table->enum('ajuste_sobre', ['valor_compra', 'valor_venda']);
            $table->enum('tipo', ['incremento', 'reducao']);
            $table->decimal('percentual_alteracao', 5);
            $table->string('tipo_pagamento', 2)->nullable();
            $table->unsignedBigInteger('funcionario_id')->nullable()->index('lista_precos_funcionario_id_foreign');
            $table->timestamps();
            $table->boolean('status')->nullable()->default(true);
            $table->decimal('valor_alteracao', 10)->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lista_precos');
    }
};
