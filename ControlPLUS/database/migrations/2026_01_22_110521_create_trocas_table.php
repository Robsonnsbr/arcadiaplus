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
        Schema::create('trocas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('trocas_empresa_id_foreign');
            $table->unsignedBigInteger('nfce_id')->nullable()->index('trocas_nfce_id_foreign');
            $table->string('observacao', 200)->nullable();
            $table->decimal('valor_troca', 12);
            $table->decimal('valor_original', 12);
            $table->integer('numero_sequencial')->nullable();
            $table->string('codigo', 8);
            $table->string('tipo_pagamento', 2);
            $table->timestamps();
            $table->integer('nfe_id')->nullable();
            $table->unsignedBigInteger('caixa_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trocas');
    }
};
