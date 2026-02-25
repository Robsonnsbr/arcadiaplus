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
        Schema::create('conta_recebers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('conta_recebers_empresa_id_foreign');
            $table->unsignedBigInteger('nfe_id')->nullable()->index('conta_recebers_nfe_id_foreign');
            $table->unsignedBigInteger('nfce_id')->nullable()->index('conta_recebers_nfce_id_foreign');
            $table->integer('ordem_servico_id')->nullable();
            $table->unsignedBigInteger('cliente_id')->nullable()->index('conta_recebers_cliente_id_foreign');
            $table->string('descricao', 200)->nullable();
            $table->string('referencia', 60)->nullable();
            $table->string('arquivo', 25)->nullable();
            $table->decimal('valor_integral', 16, 7);
            $table->decimal('valor_original', 16, 7)->nullable();
            $table->decimal('valor_recebido', 16, 7)->nullable();
            $table->date('data_vencimento');
            $table->date('data_recebimento')->nullable();
            $table->boolean('status')->default(false);
            $table->boolean('recebimento_parcial')->nullable()->default(false);
            $table->string('observacao', 100)->nullable();
            $table->string('observacao2', 100)->nullable();
            $table->string('observacao3', 100)->nullable();
            $table->string('tipo_pagamento', 2)->nullable();
            $table->unsignedBigInteger('caixa_id')->nullable()->index('conta_recebers_caixa_id_foreign');
            $table->integer('local_id')->nullable();
            $table->integer('categoria_conta_id')->nullable();
            $table->string('motivo_estorno')->nullable();
            $table->integer('conta_empresa_id')->nullable();
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
        Schema::dropIfExists('conta_recebers');
    }
};
