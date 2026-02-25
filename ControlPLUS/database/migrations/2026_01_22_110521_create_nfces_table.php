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
        Schema::create('nfces', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('nfces_empresa_id_foreign');
            $table->unsignedBigInteger('natureza_id')->nullable()->index('nfces_natureza_id_foreign');
            $table->string('emissor_nome', 100);
            $table->string('emissor_cpf_cnpj', 18);
            $table->integer('ambiente');
            $table->integer('lista_id')->nullable();
            $table->unsignedBigInteger('funcionario_id')->nullable();
            $table->unsignedBigInteger('cliente_id')->nullable()->index('nfces_cliente_id_foreign');
            $table->unsignedBigInteger('caixa_id')->nullable()->index('nfces_caixa_id_foreign');
            $table->string('cliente_nome', 100)->nullable();
            $table->string('cliente_cpf_cnpj', 18)->nullable();
            $table->string('chave', 44)->nullable();
            $table->string('chave_sat', 44)->nullable();
            $table->string('recibo', 30)->nullable();
            $table->string('numero_serie', 3);
            $table->integer('numero')->nullable();
            $table->string('motivo_rejeicao', 200)->nullable();
            $table->enum('estado', ['novo', 'rejeitado', 'cancelado', 'aprovado']);
            $table->integer('numero_sequencial')->nullable();
            $table->decimal('total', 12);
            $table->decimal('desconto', 12)->nullable();
            $table->decimal('valor_cashback', 10)->nullable();
            $table->decimal('acrescimo', 12)->nullable();
            $table->decimal('valor_entrega', 10)->nullable()->default(0);
            $table->string('observacao', 200)->nullable();
            $table->boolean('api')->default(false);
            $table->timestamp('data_emissao')->nullable();
            $table->decimal('dinheiro_recebido', 10);
            $table->decimal('troco', 10);
            $table->string('tipo_pagamento', 2);
            $table->string('bandeira_cartao', 2)->nullable()->default('99');
            $table->string('cnpj_cartao', 18)->nullable();
            $table->string('cAut_cartao', 18)->nullable();
            $table->boolean('gerar_conta_receber')->nullable()->default(false);
            $table->integer('local_id')->nullable();
            $table->text('signed_xml')->nullable();
            $table->integer('user_id')->nullable();
            $table->boolean('contigencia')->nullable()->default(false);
            $table->boolean('reenvio_contigencia')->nullable()->default(false);
            $table->string('placa', 9)->nullable();
            $table->string('uf', 2)->nullable();
            $table->integer('tipo')->nullable();
            $table->integer('qtd_volumes')->nullable();
            $table->string('numeracao_volumes', 20)->nullable();
            $table->string('especie', 20)->nullable();
            $table->decimal('peso_liquido', 8, 3)->nullable();
            $table->decimal('peso_bruto', 8, 3)->nullable();
            $table->decimal('valor_frete', 12)->nullable();
            $table->unsignedBigInteger('transportadora_id')->nullable()->index('nfces_transportadora_id_foreign');
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
        Schema::dropIfExists('nfces');
    }
};
