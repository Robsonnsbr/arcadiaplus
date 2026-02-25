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
        Schema::create('nves', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('nves_empresa_id_foreign');
            $table->unsignedBigInteger('natureza_id')->nullable()->index('nves_natureza_id_foreign');
            $table->unsignedBigInteger('funcionario_id')->nullable();
            $table->string('emissor_nome', 100);
            $table->string('emissor_cpf_cnpj', 18);
            $table->string('aut_xml', 18)->nullable();
            $table->integer('ambiente');
            $table->integer('crt')->nullable();
            $table->unsignedBigInteger('cliente_id')->nullable()->index('nves_cliente_id_foreign');
            $table->unsignedBigInteger('fornecedor_id')->nullable()->index('nves_fornecedor_id_foreign');
            $table->unsignedBigInteger('caixa_id')->nullable()->index('nves_caixa_id_foreign');
            $table->unsignedBigInteger('transportadora_id')->nullable()->index('nves_transportadora_id_foreign');
            $table->string('chave', 44);
            $table->string('chave_importada', 44)->nullable();
            $table->string('recibo', 30)->nullable();
            $table->string('numero_serie', 3);
            $table->integer('numero');
            $table->integer('numero_sequencial')->nullable();
            $table->integer('sequencia_cce')->default(0);
            $table->string('motivo_rejeicao', 200)->nullable();
            $table->enum('estado', ['novo', 'rejeitado', 'cancelado', 'aprovado']);
            $table->decimal('total', 12);
            $table->decimal('valor_produtos', 12)->nullable();
            $table->decimal('valor_frete', 12)->nullable();
            $table->decimal('desconto', 12)->nullable();
            $table->decimal('acrescimo', 12)->nullable();
            $table->string('observacao')->nullable();
            $table->string('placa', 9)->nullable();
            $table->string('uf', 2)->nullable();
            $table->integer('tipo')->nullable();
            $table->integer('qtd_volumes')->nullable();
            $table->string('numeracao_volumes', 20)->nullable();
            $table->string('especie', 20)->nullable();
            $table->string('marca', 30)->nullable();
            $table->decimal('peso_liquido', 8, 3)->nullable();
            $table->decimal('peso_bruto', 8, 3)->nullable();
            $table->boolean('api')->default(false);
            $table->boolean('gerar_conta_receber')->default(false);
            $table->boolean('gerar_conta_pagar')->default(false);
            $table->string('referencia', 44)->nullable();
            $table->integer('tpNF')->default(1);
            $table->integer('tpEmis')->default(1);
            $table->integer('finNFe')->default(1);
            $table->timestamp('data_emissao')->nullable();
            $table->boolean('orcamento')->nullable()->default(false);
            $table->integer('ref_orcamento')->nullable();
            $table->date('data_emissao_saida')->nullable();
            $table->date('data_emissao_retroativa')->nullable();
            $table->date('data_entrega')->nullable();
            $table->string('bandeira_cartao', 2)->nullable();
            $table->string('cnpj_cartao', 18)->nullable();
            $table->string('cAut_cartao', 18)->nullable();
            $table->string('tipo_pagamento', 2)->nullable()->default('');
            $table->integer('local_id')->nullable();
            $table->text('signed_xml')->nullable();
            $table->integer('user_id')->nullable();
            $table->boolean('contigencia')->nullable()->default(false);
            $table->string('nome_entrega', 60)->nullable();
            $table->string('documento_entrega', 20)->nullable();
            $table->string('rua_entrega', 60)->nullable();
            $table->string('cep_entrega', 9)->nullable();
            $table->string('numero_entrega', 10)->nullable();
            $table->string('bairro_entrega', 40)->nullable();
            $table->string('complemento_entrega', 100)->nullable();
            $table->integer('cidade_id_entrega')->nullable();
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
        Schema::dropIfExists('nves');
    }
};
