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
        Schema::create('plano_empresas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('plano_empresas_empresa_id_foreign');
            $table->unsignedBigInteger('plano_id')->index('plano_empresas_plano_id_foreign');
            $table->date('data_expiracao');
            $table->decimal('valor', 10);
            $table->string('forma_pagamento', 30);
            $table->integer('contador_id')->nullable();
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
        Schema::dropIfExists('plano_empresas');
    }
};
