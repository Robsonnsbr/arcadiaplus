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
        Schema::create('empresas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome', 100);
            $table->string('nome_fantasia', 100)->nullable();
            $table->string('cpf_cnpj', 18);
            $table->string('aut_xml', 18)->nullable();
            $table->string('ie', 18)->nullable();
            $table->string('email', 60)->nullable();
            $table->string('celular', 20)->nullable();
            $table->binary('arquivo')->nullable();
            $table->string('senha', 30)->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('substituto_tributario')->nullable()->default(false);
            $table->string('cep', 9)->nullable();
            $table->string('rua', 50)->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('bairro', 30)->nullable();
            $table->string('complemento', 50)->nullable();
            $table->unsignedBigInteger('cidade_id')->nullable()->index('empresas_cidade_id_foreign');
            $table->integer('natureza_id_pdv')->nullable();
            $table->integer('numero_ultima_nfe_producao')->nullable();
            $table->integer('numero_ultima_nfe_homologacao')->nullable();
            $table->integer('numero_serie_nfe')->nullable();
            $table->integer('numero_ultima_nfce_producao')->nullable();
            $table->integer('numero_ultima_nfce_homologacao')->nullable();
            $table->integer('numero_serie_nfce')->nullable();
            $table->integer('numero_ultima_cte_producao')->nullable();
            $table->integer('numero_ultima_cte_homologacao')->nullable();
            $table->integer('numero_serie_cte')->nullable();
            $table->integer('numero_ultima_mdfe_producao')->nullable();
            $table->integer('numero_ultima_mdfe_homologacao')->nullable();
            $table->integer('numero_serie_mdfe')->nullable();
            $table->integer('numero_ultima_nfse')->nullable();
            $table->integer('numero_serie_nfse')->nullable();
            $table->string('csc', 50)->nullable();
            $table->string('csc_id', 20)->nullable();
            $table->integer('ambiente');
            $table->enum('tributacao', ['MEI', 'Simples Nacional', 'Regime Normal', 'Simples Nacional, excesso sublimite de receita bruta'])->nullable();
            $table->string('token', 50)->nullable();
            $table->string('token_nfse', 200)->nullable();
            $table->string('token_ibpt', 200)->nullable();
            $table->string('logo', 100)->nullable();
            $table->boolean('tipo_contador')->nullable()->default(false);
            $table->integer('limite_cadastro_empresas')->nullable()->default(0);
            $table->boolean('cadastrar_planos')->nullable()->default(false);
            $table->decimal('percentual_comissao', 10)->nullable()->default(0);
            $table->decimal('perc_ap_cred', 10)->nullable()->default(0);
            $table->integer('empresa_selecionada')->nullable();
            $table->boolean('exclusao_icms_pis_cofins')->nullable()->default(false);
            $table->text('observacao_padrao_nfe')->nullable();
            $table->text('observacao_padrao_nfce')->nullable();
            $table->text('mensagem_aproveitamento_credito')->nullable();
            $table->integer('dia_vencimento_boleto')->nullable();
            $table->boolean('receber_com_boleto')->nullable()->default(false);
            $table->string('asaas_id', 20)->nullable();
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
        Schema::dropIfExists('empresas');
    }
};
