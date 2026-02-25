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
        Schema::create('ctes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('ctes_empresa_id_foreign');
            $table->unsignedBigInteger('remetente_id')->index('ctes_remetente_id_foreign');
            $table->unsignedBigInteger('destinatario_id')->index('ctes_destinatario_id_foreign');
            $table->unsignedBigInteger('recebedor_id')->nullable()->index('ctes_recebedor_id_foreign');
            $table->unsignedBigInteger('expedidor_id')->nullable()->index('ctes_expedidor_id_foreign');
            $table->unsignedBigInteger('veiculo_id')->nullable()->index('ctes_veiculo_id_foreign');
            $table->unsignedBigInteger('natureza_id')->nullable()->index('ctes_natureza_id_foreign');
            $table->integer('tomador');
            $table->unsignedBigInteger('municipio_envio')->index('ctes_municipio_envio_foreign');
            $table->unsignedBigInteger('municipio_inicio')->index('ctes_municipio_inicio_foreign');
            $table->unsignedBigInteger('municipio_fim')->index('ctes_municipio_fim_foreign');
            $table->string('cpf_cnpj_tomador', 18)->nullable();
            $table->string('ie_tomador', 15)->nullable();
            $table->string('razao_social_tomador', 60)->nullable();
            $table->string('nome_fantasia_tomador', 60)->nullable();
            $table->string('telefone_tomador', 20)->nullable();
            $table->string('email_tomador', 60)->nullable();
            $table->string('logradouro_tomador', 80)->nullable();
            $table->string('numero_tomador', 20)->nullable();
            $table->string('bairro_tomador', 40)->nullable();
            $table->string('cep_tomador', 10)->nullable();
            $table->unsignedBigInteger('municipio_tomador')->index('ctes_municipio_tomador_foreign');
            $table->decimal('valor_transporte', 10);
            $table->decimal('valor_receber', 10);
            $table->decimal('valor_carga', 10);
            $table->string('produto_predominante', 30);
            $table->date('data_prevista_entrega');
            $table->string('observacao')->nullable();
            $table->integer('sequencia_cce')->default(0);
            $table->string('chave', 44);
            $table->string('recibo', 30)->nullable();
            $table->string('numero_serie', 3);
            $table->integer('numero');
            $table->enum('estado', ['novo', 'rejeitado', 'cancelado', 'aprovado']);
            $table->string('motivo_rejeicao', 200)->nullable();
            $table->boolean('retira');
            $table->string('detalhes_retira', 100);
            $table->string('modal', 2);
            $table->integer('ambiente');
            $table->string('tpDoc', 2);
            $table->string('descOutros', 100);
            $table->integer('nDoc');
            $table->decimal('vDocFisc', 10);
            $table->integer('globalizado');
            $table->string('cst', 3)->default('00');
            $table->decimal('perc_icms', 5)->default(0);
            $table->decimal('perc_red_bc', 5)->default(0);
            $table->boolean('status_pagamento')->nullable()->default(false);
            $table->string('cfop', 4)->nullable();
            $table->boolean('api')->nullable()->default(false);
            $table->integer('local_id')->nullable();
            $table->integer('tipo_servico')->nullable();
            $table->integer('tipo_cte')->nullable()->default(0);
            $table->string('referencia_cte', 44)->nullable();
            $table->string('doc_anterior', 20)->nullable();
            $table->string('emitente_anterior', 100)->nullable();
            $table->string('tp_doc_anterior', 2)->nullable();
            $table->string('serie_anterior', 3)->nullable();
            $table->string('n_doc_anterior', 8)->nullable();
            $table->date('data_emissao_anterior')->nullable();
            $table->string('uf_anterior', 2)->nullable();
            $table->string('ie_anterior', 20)->nullable();
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
        Schema::dropIfExists('ctes');
    }
};
