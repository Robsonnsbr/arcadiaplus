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
        Schema::create('apuracao_mensals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('funcionario_id')->index('apuracao_mensals_funcionario_id_foreign');
            $table->string('mes', 20);
            $table->integer('ano');
            $table->decimal('valor_final', 10);
            $table->string('forma_pagamento', 30);
            $table->string('observacao', 100);
            $table->integer('conta_pagar_id')->default(0);
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
        Schema::dropIfExists('apuracao_mensals');
    }
};
