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
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('pagamentos_empresa_id_foreign');
            $table->unsignedBigInteger('plano_id')->nullable()->index('pagamentos_plano_id_foreign');
            $table->decimal('valor', 10);
            $table->string('transacao_id', 100);
            $table->string('status', 15);
            $table->string('forma_pagamento', 15);
            $table->text('qr_code_base64');
            $table->text('qr_code');
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
        Schema::dropIfExists('pagamentos');
    }
};
