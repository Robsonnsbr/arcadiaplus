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
        Schema::create('fatura_reservas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('reserva_id')->nullable()->index('fatura_reservas_reserva_id_foreign');
            $table->string('tipo_pagamento', 2);
            $table->date('data_vencimento');
            $table->decimal('valor', 10);
            $table->timestamps();
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
        Schema::dropIfExists('fatura_reservas');
    }
};
