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
        Schema::create('fatura_nfces', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('nfce_id')->nullable()->index('fatura_nfces_nfce_id_foreign');
            $table->string('tipo_pagamento', 2);
            $table->date('data_vencimento');
            $table->decimal('valor', 10);
            $table->string('observacao', 100)->nullable();
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
        Schema::dropIfExists('fatura_nfces');
    }
};
