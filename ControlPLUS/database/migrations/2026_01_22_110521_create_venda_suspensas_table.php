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
        Schema::create('venda_suspensas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('venda_suspensas_empresa_id_foreign');
            $table->unsignedBigInteger('cliente_id')->nullable()->index('venda_suspensas_cliente_id_foreign');
            $table->decimal('total', 12);
            $table->decimal('desconto', 12)->nullable();
            $table->decimal('acrescimo', 12)->nullable();
            $table->string('observacao', 100)->nullable();
            $table->string('tipo_pagamento', 2);
            $table->integer('local_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('funcionario_id')->nullable();
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
        Schema::dropIfExists('venda_suspensas');
    }
};
