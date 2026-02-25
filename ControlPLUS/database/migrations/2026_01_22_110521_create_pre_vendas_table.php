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
        Schema::create('pre_vendas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('pre_vendas_empresa_id_foreign');
            $table->unsignedBigInteger('cliente_id')->nullable()->index('pre_vendas_cliente_id_foreign');
            $table->integer('lista_id')->nullable();
            $table->unsignedBigInteger('usuario_id')->index('pre_vendas_usuario_id_foreign');
            $table->unsignedBigInteger('funcionario_id')->nullable()->index('pre_vendas_funcionario_id_foreign');
            $table->unsignedBigInteger('natureza_id')->index('pre_vendas_natureza_id_foreign');
            $table->decimal('valor_total', 16, 7);
            $table->decimal('desconto', 10);
            $table->decimal('acrescimo', 10);
            $table->string('forma_pagamento', 20)->nullable();
            $table->string('tipo_pagamento', 2)->nullable();
            $table->string('observacao', 150);
            $table->integer('pedido_delivery_id')->nullable();
            $table->enum('tipo_finalizado', ['nfe', 'nfce']);
            $table->integer('venda_id')->nullable();
            $table->string('codigo', 8);
            $table->string('bandeira_cartao', 2)->default('99');
            $table->string('cnpj_cartao', 18)->default('');
            $table->string('cAut_cartao', 20)->default('');
            $table->string('descricao_pag_outros', 80)->default('');
            $table->boolean('rascunho')->default(false);
            $table->boolean('status')->default(true);
            $table->integer('local_id')->nullable();
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
        Schema::dropIfExists('pre_vendas');
    }
};
