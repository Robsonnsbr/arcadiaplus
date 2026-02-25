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
        Schema::create('suprimento_caixas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('caixa_id')->nullable()->index('suprimento_caixas_caixa_id_foreign');
            $table->decimal('valor', 10);
            $table->string('observacao', 200);
            $table->integer('conta_empresa_id')->nullable();
            $table->string('tipo_pagamento', 2)->nullable();
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
        Schema::dropIfExists('suprimento_caixas');
    }
};
