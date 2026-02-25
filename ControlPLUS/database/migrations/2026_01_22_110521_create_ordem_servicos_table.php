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
        Schema::create('ordem_servicos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('ordem_servicos_empresa_id_foreign');
            $table->unsignedBigInteger('cliente_id')->nullable()->index('ordem_servicos_cliente_id_foreign');
            $table->unsignedBigInteger('usuario_id')->nullable()->index('ordem_servicos_usuario_id_foreign');
            $table->unsignedBigInteger('funcionario_id')->nullable()->index('ordem_servicos_funcionario_id_foreign');
            $table->string('estado', 2)->default('pd');
            $table->text('descricao');
            $table->string('forma_pagamento', 10)->default('av');
            $table->decimal('valor', 10)->default(0);
            $table->timestamp('data_inicio');
            $table->timestamp('data_entrega')->nullable();
            $table->integer('nfe_id')->default(0);
            $table->integer('codigo_sequencial')->nullable();
            $table->unsignedBigInteger('caixa_id')->nullable()->index('ordem_servicos_caixa_id_foreign');
            $table->integer('local_id')->nullable();
            $table->integer('veiculo_id')->nullable();
            $table->decimal('adiantamento', 10)->nullable()->default(0);
            $table->string('hash_link', 30)->nullable();
            $table->string('tipo_servico', 30)->nullable();
            $table->text('diagnostico_cliente')->nullable();
            $table->text('diagnostico_tecnico')->nullable();
            $table->text('defeito_encontrado')->nullable();
            $table->string('equipamento', 100)->nullable();
            $table->string('numero_serie', 100)->nullable();
            $table->string('cor', 30)->nullable();
            $table->boolean('faturada')->nullable()->default(false);
            $table->timestamps();
            $table->time('horario_entrega')->nullable();
            $table->integer('convenio_id')->nullable();
            $table->boolean('status_entrega')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordem_servicos');
    }
};
