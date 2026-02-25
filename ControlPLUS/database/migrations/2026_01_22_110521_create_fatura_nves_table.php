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
        Schema::create('fatura_nves', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('nfe_id')->nullable()->index('fatura_nves_nfe_id_foreign');
            $table->string('tipo_pagamento', 2)->nullable();
            $table->date('data_vencimento');
            $table->decimal('valor', 10);
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
        Schema::dropIfExists('fatura_nves');
    }
};
