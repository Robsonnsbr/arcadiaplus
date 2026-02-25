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
        Schema::create('mdves', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('mdves_empresa_id_foreign');
            $table->string('uf_inicio', 2);
            $table->string('uf_fim', 2);
            $table->boolean('encerrado');
            $table->date('data_inicio_viagem');
            $table->boolean('carga_posterior');
            $table->string('cnpj_contratante', 18);
            $table->unsignedBigInteger('veiculo_tracao_id')->nullable()->index('mdves_veiculo_tracao_id_foreign');
            $table->unsignedBigInteger('veiculo_reboque_id')->nullable()->index('mdves_veiculo_reboque_id_foreign');
            $table->unsignedBigInteger('veiculo_reboque2_id')->nullable()->index('mdves_veiculo_reboque2_id_foreign');
            $table->unsignedBigInteger('veiculo_reboque3_id')->nullable()->index('mdves_veiculo_reboque3_id_foreign');
            $table->enum('estado_emissao', ['novo', 'aprovado', 'rejeitado', 'cancelado'])->nullable();
            $table->integer('mdfe_numero')->nullable();
            $table->string('chave', 44);
            $table->string('protocolo', 16);
            $table->string('seguradora_nome', 30);
            $table->string('seguradora_cnpj', 18);
            $table->string('numero_apolice', 15);
            $table->string('numero_averbacao', 40);
            $table->decimal('valor_carga', 10);
            $table->decimal('quantidade_carga', 16, 4)->nullable()->default(0);
            $table->string('info_complementar', 60);
            $table->string('info_adicional_fisco', 60);
            $table->string('condutor_nome', 60);
            $table->string('condutor_cpf', 15);
            $table->string('lac_rodo', 8);
            $table->integer('tp_emit');
            $table->integer('tp_transp');
            $table->string('produto_pred_nome', 50);
            $table->string('produto_pred_ncm', 8);
            $table->string('produto_pred_cod_barras', 13);
            $table->string('cep_carrega', 8);
            $table->string('cep_descarrega', 8);
            $table->string('tp_carga', 2);
            $table->string('latitude_carregamento', 15)->nullable()->default('');
            $table->string('longitude_carregamento', 15)->nullable()->default('');
            $table->string('latitude_descarregamento', 15)->nullable()->default('');
            $table->string('longitude_descarregamento', 15)->nullable()->default('');
            $table->integer('local_id')->nullable();
            $table->integer('tipo_modal')->nullable()->default(1);
            $table->string('nome_pagador', 100)->nullable();
            $table->string('documento_pagador', 20)->nullable();
            $table->string('ind_pag', 2)->nullable();
            $table->decimal('valor_transporte', 10)->nullable();
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
        Schema::dropIfExists('mdves');
    }
};
