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
        Schema::create('config_gerals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('config_gerals_empresa_id_foreign');
            $table->enum('balanca_valor_peso', ['peso', 'valor']);
            $table->integer('balanca_digito_verificador')->nullable();
            $table->boolean('confirmar_itens_prevenda')->nullable()->default(false);
            $table->boolean('gerenciar_estoque')->nullable()->default(false);
            $table->boolean('agrupar_itens')->nullable()->default(false);
            $table->text('notificacoes')->nullable();
            $table->decimal('margem_combo', 5)->nullable()->default(50);
            $table->decimal('percentual_desconto_orcamento', 5)->nullable();
            $table->decimal('percentual_lucro_produto', 10)->nullable()->default(0);
            $table->text('tipos_pagamento_pdv')->nullable();
            $table->string('senha_manipula_valor', 20)->nullable();
            $table->boolean('abrir_modal_cartao')->nullable()->default(true);
            $table->enum('tipo_comissao', ['percentual_vendedor', 'percentual_margem'])->nullable()->default('percentual_vendedor');
            $table->string('modelo', 20)->nullable()->default('light');
            $table->boolean('alerta_sonoro')->nullable()->default(true);
            $table->boolean('cabecalho_pdv')->nullable()->default(true);
            $table->boolean('definir_vendedor_pdv')->nullable()->default(false);
            $table->boolean('gerar_conta_receber_padrao')->nullable()->default(true);
            $table->boolean('gerar_conta_pagar_padrao')->nullable()->default(true);
            $table->integer('regime_nfse')->nullable();
            $table->string('mercadopago_public_key_pix', 120)->nullable();
            $table->string('mercadopago_access_token_pix', 120)->nullable();
            $table->boolean('definir_vendedor_pdv_off')->nullable()->default(false);
            $table->boolean('alterar_valor_pdv_off')->nullable()->default(false);
            $table->string('acessos_pdv_off')->nullable();
            $table->enum('tipo_menu', ['vertical', 'horizontal'])->nullable()->default('vertical');
            $table->enum('cor_menu', ['light', 'brand', 'dark'])->nullable()->default('light');
            $table->enum('cor_top_bar', ['light', 'brand', 'dark'])->nullable()->default('light');
            $table->boolean('usar_ibpt')->nullable()->default(true);
            $table->integer('casas_decimais_quantidade')->nullable()->default(2);
            $table->integer('cliente_padrao_pdv_off')->nullable();
            $table->text('mensagem_padrao_impressao_venda')->nullable();
            $table->text('mensagem_padrao_impressao_os')->nullable();
            $table->integer('ultimo_codigo_produto')->nullable()->default(0);
            $table->integer('ultimo_codigo_cliente')->nullable()->default(0);
            $table->integer('ultimo_codigo_fornecedor')->nullable()->default(0);
            $table->boolean('app_valor_aprazo')->nullable()->default(false);
            $table->boolean('impressao_sem_janela_cupom')->nullable()->default(false);
            $table->string('resp_tec_email', 80)->nullable();
            $table->string('resp_tec_cpf_cnpj', 18)->nullable();
            $table->string('resp_tec_nome', 60)->nullable();
            $table->string('resp_tec_telefone', 20)->nullable();
            $table->boolean('limitar_credito_cliente')->nullable()->default(false);
            $table->boolean('corrigir_numeracao_fiscal')->nullable()->default(true);
            $table->string('documento_pdv', 4)->nullable()->default('nfce');
            $table->integer('numero_inicial_comanda')->nullable();
            $table->integer('numero_final_comanda')->nullable();
            $table->text('home_componentes')->nullable();
            $table->string('token_whatsapp', 120)->nullable();
            $table->string('small_header_user', 50)->nullable()->default('small-4.png');
            $table->text('mensagem_wpp_link')->nullable();
            $table->boolean('status_wpp_link')->nullable()->default(false);
            $table->boolean('enviar_danfe_wpp_link')->nullable()->default(false);
            $table->boolean('enviar_xml_wpp_link')->nullable()->default(false);
            $table->boolean('enviar_pedido_a4_wpp_link')->nullable()->default(false);
            $table->boolean('produtos_exibe_tabela')->nullable()->default(true);
            $table->boolean('clientes_exibe_tabela')->nullable()->default(true);
            $table->integer('itens_por_pagina')->nullable()->default(30);
            $table->string('tipo_ordem_servico', 50)->nullable()->default('normal');
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
        Schema::dropIfExists('config_gerals');
    }
};
