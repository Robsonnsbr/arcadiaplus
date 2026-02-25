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
        Schema::create('financeiro_contadors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('contador_id')->nullable()->index('financeiro_contadors_contador_id_foreign');
            $table->decimal('percentual_comissao', 5);
            $table->decimal('valor_comissao', 10);
            $table->decimal('total_venda', 10);
            $table->string('mes', 20);
            $table->integer('ano');
            $table->string('tipo_pagamento', 30)->nullable();
            $table->string('observacao', 100)->nullable();
            $table->boolean('status_pagamento')->default(false);
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
        Schema::dropIfExists('financeiro_contadors');
    }
};
