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
        Schema::create('configuracao_supers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('cpf_cnpj', 20);
            $table->string('name');
            $table->string('email');
            $table->string('telefone', 20);
            $table->boolean('usar_resp_tecnico')->nullable()->default(false);
            $table->boolean('auto_cadastro')->nullable()->default(true);
            $table->boolean('cobrar_apos_auto_cadastro')->nullable()->default(false);
            $table->boolean('landing_page')->nullable()->default(false);
            $table->string('mercadopago_public_key', 120)->nullable();
            $table->string('mercadopago_access_token', 120)->nullable();
            $table->string('sms_key', 120)->nullable();
            $table->string('token_whatsapp', 120)->nullable();
            $table->string('usuario_correios', 30)->nullable();
            $table->string('codigo_acesso_correios', 100)->nullable();
            $table->string('cartao_postagem_correios', 100)->nullable();
            $table->text('token_correios')->nullable();
            $table->string('token_expira_correios', 30)->nullable();
            $table->string('dr_correios', 30)->nullable();
            $table->string('contrato_correios', 30)->nullable();
            $table->string('token_auth_nfse')->nullable();
            $table->integer('timeout_nfe')->nullable()->default(8);
            $table->integer('timeout_nfce')->nullable()->default(8);
            $table->integer('timeout_cte')->nullable()->default(8);
            $table->integer('timeout_mdfe')->nullable()->default(8);
            $table->string('token_api', 50)->nullable();
            $table->string('token_integra_notas')->nullable();
            $table->enum('banco_plano', ['mercado_pago', 'asaas'])->nullable()->default('mercado_pago');
            $table->string('asaas_token')->nullable();
            $table->boolean('receber_com_boleto')->nullable()->default(false);
            $table->string('asaas_token_boleto')->nullable();
            $table->decimal('percentual_juros_padrao_boleto', 5)->nullable()->default(0);
            $table->decimal('percentual_multa_padrao_boleto', 5)->nullable()->default(0);
            $table->integer('dias_atraso_suspender_boleto')->nullable();
            $table->boolean('sandbox_boleto')->nullable()->default(true);
            $table->boolean('usuario_alterar_plano')->nullable()->default(true);
            $table->boolean('info_topo_menu')->nullable()->default(true);
            $table->integer('dias_alerta_boleto')->nullable()->default(10);
            $table->enum('tema_padrao', ['light', 'dark'])->nullable()->default('light');
            $table->boolean('duplicar_cpf_cnpj')->nullable()->default(true);
            $table->string('email_aviso_novo_cadastro', 120)->nullable();
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
        Schema::dropIfExists('configuracao_supers');
    }
};
