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
        Schema::create('financeiro_planos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('financeiro_planos_empresa_id_foreign');
            $table->unsignedBigInteger('plano_id')->nullable()->index('financeiro_planos_plano_id_foreign');
            $table->decimal('valor', 10);
            $table->string('tipo_pagamento', 50);
            $table->enum('status_pagamento', ['pendente', 'recebido', 'cancelado']);
            $table->integer('plano_empresa_id');
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
        Schema::dropIfExists('financeiro_planos');
    }
};
