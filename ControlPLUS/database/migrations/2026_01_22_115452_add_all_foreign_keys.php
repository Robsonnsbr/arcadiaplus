<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllForeignKeys extends Migration
{
    public function up()
    {
        Schema::table('acao_logs', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('acesso_logs', function (Blueprint $table) {
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('acomodacaos', function (Blueprint $table) {
                    $table->foreign(['categoria_id'])->references(['id'])->on('categoria_acomodacaos')->onUpdate('NO ACTION')->onDelete('CASCADE');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('adicionals', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('agendamentos', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['funcionario_id'])->references(['id'])->on('funcionarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('api_configs', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('api_logs', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('apontamentos', function (Blueprint $table) {
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('apuracao_mensal_eventos', function (Blueprint $table) {
                    $table->foreign(['apuracao_id'])->references(['id'])->on('apuracao_mensals')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['evento_id'])->references(['id'])->on('evento_salarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('apuracao_mensals', function (Blueprint $table) {
                    $table->foreign(['funcionario_id'])->references(['id'])->on('funcionarios')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('bairro_deliveries', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('bairro_delivery_masters', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('boletos', function (Blueprint $table) {
                    $table->foreign(['conta_boleto_id'])->references(['id'])->on('conta_boletos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['conta_receber_id'])->references(['id'])->on('conta_recebers')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('c_te_descargas', function (Blueprint $table) {
                    $table->foreign(['info_id'])->references(['id'])->on('info_descargas')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('caixas', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('carrinho_cardapios', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('carrinho_deliveries', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['endereco_id'])->references(['id'])->on('endereco_deliveries')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('carrinhos', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['endereco_id'])->references(['id'])->on('endereco_ecommerces')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('carrossel_cardapios', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('cash_back_clientes', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('cash_back_configs', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('categoria_acomodacaos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('categoria_adicionals', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('categoria_contas', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('categoria_nuvem_shops', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('categoria_produto_ifoods', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('categoria_produtos', function (Blueprint $table) {
                    $table->foreign(['categoria_id'])->references(['id'])->on('categoria_produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('categoria_servicos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('categoria_vendi_zaps', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('categoria_woocommerces', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('chave_nfe_ctes', function (Blueprint $table) {
                    $table->foreign(['cte_id'])->references(['id'])->on('ctes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('ciots', function (Blueprint $table) {
                    $table->foreign(['mdfe_id'])->references(['id'])->on('mdves')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('clientes', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('comissao_vendas', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['funcionario_id'])->references(['id'])->on('funcionarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('componente_ctes', function (Blueprint $table) {
                    $table->foreign(['cte_id'])->references(['id'])->on('ctes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('componente_mdves', function (Blueprint $table) {
                    $table->foreign(['mdfe_id'])->references(['id'])->on('mdves')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('config_gerals', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('configuracao_agendamentos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('configuracao_cardapios', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('consumo_reservas', function (Blueprint $table) {
                    $table->foreign(['produto_id'])->references(['id'])->on('reservas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['reserva_id'])->references(['id'])->on('reservas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('conta_boletos', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('conta_empresas', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('conta_pagars', function (Blueprint $table) {
                    $table->foreign(['caixa_id'])->references(['id'])->on('caixas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['fornecedor_id'])->references(['id'])->on('fornecedors')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['nfe_id'])->references(['id'])->on('nves')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('conta_recebers', function (Blueprint $table) {
                    $table->foreign(['caixa_id'])->references(['id'])->on('caixas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['nfce_id'])->references(['id'])->on('nfces')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['nfe_id'])->references(['id'])->on('nves')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('contador_empresas', function (Blueprint $table) {
                    $table->foreign(['contador_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('contigencias', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('contrato_empresas', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('convenios', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('cotacaos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['fornecedor_id'])->references(['id'])->on('fornecedors')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('credito_clientes', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('crm_anotacao_notas', function (Blueprint $table) {
                    $table->foreign(['crm_anotacao_id'])->references(['id'])->on('crm_anotacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('crm_anotacaos', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['fornecedor_id'])->references(['id'])->on('fornecedors')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('cte_os', function (Blueprint $table) {
                    $table->foreign(['emitente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['municipio_envio'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['municipio_fim'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['municipio_inicio'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['natureza_id'])->references(['id'])->on('natureza_operacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['tomador_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['veiculo_id'])->references(['id'])->on('veiculos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('ctes', function (Blueprint $table) {
                    $table->foreign(['destinatario_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['expedidor_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['municipio_envio'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['municipio_fim'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['municipio_inicio'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['municipio_tomador'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['natureza_id'])->references(['id'])->on('natureza_operacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['recebedor_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['remetente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['veiculo_id'])->references(['id'])->on('veiculos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('cupom_desconto_clientes', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['cupom_id'])->references(['id'])->on('cupom_descontos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['pedido_id'])->references(['id'])->on('pedido_deliveries')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('cupom_descontos', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('custo_adm_planejamento_custos', function (Blueprint $table) {
                    $table->foreign(['planejamento_id'])->references(['id'])->on('planejamento_custos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('despesa_fretes', function (Blueprint $table) {
                    $table->foreign(['fornecedor_id'])->references(['id'])->on('fornecedors')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['frete_id'])->references(['id'])->on('fretes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['tipo_despesa_id'])->references(['id'])->on('tipo_despesa_fretes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('destaque_market_places', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['servico_id'])->references(['id'])->on('servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('dia_semanas', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['funcionario_id'])->references(['id'])->on('funcionarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('difals', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('ecommerce_configs', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('email_configs', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('empresas', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('endereco_deliveries', function (Blueprint $table) {
                    $table->foreign(['bairro_id'])->references(['id'])->on('bairro_deliveries')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('endereco_ecommerces', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('escritorio_contabils', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('estoque_atual_produtos', function (Blueprint $table) {
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('estoques', function (Blueprint $table) {
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_variacao_id'])->references(['id'])->on('produto_variacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('etiqueta_configuracaos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('evento_salarios', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('fatura_clientes', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('fatura_cotacaos', function (Blueprint $table) {
                    $table->foreign(['cotacao_id'])->references(['id'])->on('cotacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('fatura_nfces', function (Blueprint $table) {
                    $table->foreign(['nfce_id'])->references(['id'])->on('nfces')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('fatura_nves', function (Blueprint $table) {
                    $table->foreign(['nfe_id'])->references(['id'])->on('nves')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('fatura_ordem_servicos', function (Blueprint $table) {
                    $table->foreign(['ordem_servico_id'])->references(['id'])->on('ordem_servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('fatura_pre_vendas', function (Blueprint $table) {
                    $table->foreign(['pre_venda_id'])->references(['id'])->on('pre_vendas')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('fatura_reservas', function (Blueprint $table) {
                    $table->foreign(['reserva_id'])->references(['id'])->on('reservas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('fila_envio_crons', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('financeiro_boletos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('financeiro_contadors', function (Blueprint $table) {
                    $table->foreign(['contador_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('financeiro_planos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['plano_id'])->references(['id'])->on('planos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('formato_armacao_oticas', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('fornecedors', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('frete_anexos', function (Blueprint $table) {
                    $table->foreign(['frete_id'])->references(['id'])->on('fretes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('fretes', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['veiculo_id'])->references(['id'])->on('veiculos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('frigobars', function (Blueprint $table) {
                    $table->foreign(['acomodacao_id'])->references(['id'])->on('acomodacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('funcionamento_deliveries', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('funcionamentos', function (Blueprint $table) {
                    $table->foreign(['funcionario_id'])->references(['id'])->on('funcionarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('funcionario_eventos', function (Blueprint $table) {
                    $table->foreign(['evento_id'])->references(['id'])->on('evento_salarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['funcionario_id'])->references(['id'])->on('funcionarios')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('funcionario_os', function (Blueprint $table) {
                    $table->foreign(['funcionario_id'])->references(['id'])->on('funcionarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['ordem_servico_id'])->references(['id'])->on('ordem_servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('funcionario_servicos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['funcionario_id'])->references(['id'])->on('funcionarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['servico_id'])->references(['id'])->on('servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('funcionarios', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('galeria_produtos', function (Blueprint $table) {
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('garantias', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('gestao_custo_producao_outro_custos', function (Blueprint $table) {
                    $table->foreign(['gestao_custo_id'])->references(['id'])->on('gestao_custo_producaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('gestao_custo_producao_produtos', function (Blueprint $table) {
                    $table->foreign(['gestao_custo_id'])->references(['id'])->on('gestao_custo_producaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('gestao_custo_producao_servicos', function (Blueprint $table) {
                    $table->foreign(['gestao_custo_id'])->references(['id'])->on('gestao_custo_producaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['servico_id'])->references(['id'])->on('servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('gestao_custo_producaos', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('hospede_reservas', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['reserva_id'])->references(['id'])->on('reservas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('ifood_configs', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('impressora_pedido_produtos', function (Blueprint $table) {
                    $table->foreign(['impressora_id'])->references(['id'])->on('impressora_pedidos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('impressora_pedidos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('info_descargas', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['mdfe_id'])->references(['id'])->on('mdves')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('informacao_bancaria_mdves', function (Blueprint $table) {
                    $table->foreign(['mdfe_id'])->references(['id'])->on('mdves')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('interrupcoes', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['funcionario_id'])->references(['id'])->on('funcionarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('inutilizacaos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('inventarios', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_adicional_deliveries', function (Blueprint $table) {
                    $table->foreign(['adicional_id'])->references(['id'])->on('adicionals')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['item_pedido_id'])->references(['id'])->on('item_pedido_deliveries')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_adicional_nfces', function (Blueprint $table) {
                    $table->foreign(['adicional_id'])->references(['id'])->on('adicionals')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['item_nfce_id'])->references(['id'])->on('item_nfces')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_adicionals', function (Blueprint $table) {
                    $table->foreign(['adicional_id'])->references(['id'])->on('adicionals')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['item_pedido_id'])->references(['id'])->on('item_pedidos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_agendamentos', function (Blueprint $table) {
                    $table->foreign(['agendamento_id'])->references(['id'])->on('agendamentos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['servico_id'])->references(['id'])->on('servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_carrinho_adicional_cardapios', function (Blueprint $table) {
                    $table->foreign(['adicional_id'])->references(['id'])->on('adicionals')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['item_carrinho_id'])->references(['id'])->on('item_carrinho_cardapios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_carrinho_adicional_deliveries', function (Blueprint $table) {
                    $table->foreign(['adicional_id'])->references(['id'])->on('adicionals')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['item_carrinho_id'])->references(['id'])->on('item_carrinho_deliveries')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_carrinho_cardapios', function (Blueprint $table) {
                    $table->foreign(['carrinho_id'])->references(['id'])->on('carrinho_cardapios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['tamanho_id'])->references(['id'])->on('tamanho_pizzas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_carrinho_deliveries', function (Blueprint $table) {
                    $table->foreign(['carrinho_id'])->references(['id'])->on('carrinho_deliveries')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['tamanho_id'])->references(['id'])->on('tamanho_pizzas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_carrinhos', function (Blueprint $table) {
                    $table->foreign(['carrinho_id'])->references(['id'])->on('carrinhos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['variacao_id'])->references(['id'])->on('produto_variacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_conta_empresas', function (Blueprint $table) {
                    $table->foreign(['conta_id'])->references(['id'])->on('conta_empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_cotacaos', function (Blueprint $table) {
                    $table->foreign(['cotacao_id'])->references(['id'])->on('cotacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_dimensao_nves', function (Blueprint $table) {
                    $table->foreign(['item_nfe_id'])->references(['id'])->on('item_nves')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_ibpts', function (Blueprint $table) {
                    $table->foreign(['ibpt_id'])->references(['id'])->on('ibpts')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_inventario_impressaos', function (Blueprint $table) {
                    $table->foreign(['inventario_id'])->references(['id'])->on('inventarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_inventarios', function (Blueprint $table) {
                    $table->foreign(['inventario_id'])->references(['id'])->on('inventarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_lista_precos', function (Blueprint $table) {
                    $table->foreign(['lista_id'])->references(['id'])->on('lista_precos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_nfces', function (Blueprint $table) {
                    $table->foreign(['nfce_id'])->references(['id'])->on('nfces')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['variacao_id'])->references(['id'])->on('produto_variacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_nota_servicos', function (Blueprint $table) {
                    $table->foreign(['nota_servico_id'])->references(['id'])->on('nota_servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['servico_id'])->references(['id'])->on('servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_nves', function (Blueprint $table) {
                    $table->foreign(['nfe_id'])->references(['id'])->on('nves')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['variacao_id'])->references(['id'])->on('produto_variacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_ordem_producaos', function (Blueprint $table) {
                    $table->foreign(['item_producao_id'])->references(['id'])->on('item_producaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['ordem_producao_id'])->references(['id'])->on('ordem_producaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_pedido_deliveries', function (Blueprint $table) {
                    $table->foreign(['pedido_id'])->references(['id'])->on('pedido_deliveries')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['tamanho_id'])->references(['id'])->on('tamanho_pizzas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_pedido_ecommerces', function (Blueprint $table) {
                    $table->foreign(['pedido_id'])->references(['id'])->on('pedido_ecommerces')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['variacao_id'])->references(['id'])->on('produto_variacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_pedido_mercado_livres', function (Blueprint $table) {
                    $table->foreign(['pedido_id'])->references(['id'])->on('pedido_mercado_livres')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_pedido_servicos', function (Blueprint $table) {
                    $table->foreign(['pedido_id'])->references(['id'])->on('pedidos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['servico_id'])->references(['id'])->on('servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_pedido_vendi_zaps', function (Blueprint $table) {
                    $table->foreign(['pedido_id'])->references(['id'])->on('pedido_vendi_zaps')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_pedidos', function (Blueprint $table) {
                    $table->foreign(['funcionario_id'])->references(['id'])->on('funcionarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['pedido_id'])->references(['id'])->on('pedidos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['tamanho_id'])->references(['id'])->on('tamanho_pizzas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_pizza_carrinho_cardapios', function (Blueprint $table) {
                    $table->foreign(['item_carrinho_id'])->references(['id'])->on('item_carrinho_cardapios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_pizza_carrinhos', function (Blueprint $table) {
                    $table->foreign(['item_carrinho_id'])->references(['id'])->on('item_carrinho_deliveries')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_pizza_nfces', function (Blueprint $table) {
                    $table->foreign(['item_nfce_id'])->references(['id'])->on('item_nfces')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_pizza_pedido_deliveries', function (Blueprint $table) {
                    $table->foreign(['item_pedido_id'])->references(['id'])->on('item_pedido_deliveries')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_pizza_pedidos', function (Blueprint $table) {
                    $table->foreign(['item_pedido_id'])->references(['id'])->on('item_pedidos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_pre_vendas', function (Blueprint $table) {
                    $table->foreign(['pre_venda_id'])->references(['id'])->on('pre_vendas')->onUpdate('NO ACTION')->onDelete('CASCADE');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['variacao_id'])->references(['id'])->on('produto_variacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_producaos', function (Blueprint $table) {
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_proposta_planejamento_custos', function (Blueprint $table) {
                    $table->foreign(['planejamento_id'])->references(['id'])->on('planejamento_custos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_servico_nfces', function (Blueprint $table) {
                    $table->foreign(['nfce_id'])->references(['id'])->on('nfces')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['servico_id'])->references(['id'])->on('servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_transferencia_estoques', function (Blueprint $table) {
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['transferencia_id'])->references(['id'])->on('transferencia_estoques')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_trocas', function (Blueprint $table) {
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['troca_id'])->references(['id'])->on('trocas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('item_venda_suspensas', function (Blueprint $table) {
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['variacao_id'])->references(['id'])->on('produto_variacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['venda_id'])->references(['id'])->on('venda_suspensas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('laboratorios', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('lacre_transportes', function (Blueprint $table) {
                    $table->foreign(['info_id'])->references(['id'])->on('info_descargas')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('lacre_unidade_cargas', function (Blueprint $table) {
                    $table->foreign(['info_id'])->references(['id'])->on('info_descargas')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('lista_preco_usuarios', function (Blueprint $table) {
                    $table->foreign(['lista_preco_id'])->references(['id'])->on('lista_precos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('lista_precos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['funcionario_id'])->references(['id'])->on('funcionarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('localizacaos', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('log_boletos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('manifesto_dves', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('manutencao_veiculo_anexos', function (Blueprint $table) {
                    $table->foreign(['manutencao_id'])->references(['id'])->on('manutencao_veiculos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('manutencao_veiculo_produtos', function (Blueprint $table) {
                    $table->foreign(['manutencao_id'])->references(['id'])->on('manutencao_veiculos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('manutencao_veiculo_servicos', function (Blueprint $table) {
                    $table->foreign(['manutencao_id'])->references(['id'])->on('manutencao_veiculos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['servico_id'])->references(['id'])->on('servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('manutencao_veiculos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['fornecedor_id'])->references(['id'])->on('fornecedors')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['veiculo_id'])->references(['id'])->on('veiculos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('marcas', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('margem_comissaos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('market_place_configs', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('mdves', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['veiculo_reboque2_id'])->references(['id'])->on('veiculos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['veiculo_reboque3_id'])->references(['id'])->on('veiculos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['veiculo_reboque_id'])->references(['id'])->on('veiculos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['veiculo_tracao_id'])->references(['id'])->on('veiculos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('medicao_receita_os', function (Blueprint $table) {
                    $table->foreign(['ordem_servico_id'])->references(['id'])->on('ordem_servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('medicos', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('medida_ctes', function (Blueprint $table) {
                    $table->foreign(['cte_id'])->references(['id'])->on('ctes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('mensagem_agendamento_logs', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('mensagem_padrao_crms', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('mercado_livre_configs', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('mercado_livre_perguntas', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('mesas', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('meta_resultados', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['funcionario_id'])->references(['id'])->on('funcionarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('model_has_permissions', function (Blueprint $table) {
                    $table->foreign(['permission_id'])->references(['id'])->on('permissions')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('model_has_roles', function (Blueprint $table) {
                    $table->foreign(['role_id'])->references(['id'])->on('roles')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('modelo_etiquetas', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('motivo_interrupcaos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('motoboy_comissaos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['motoboy_id'])->references(['id'])->on('motoboys')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['pedido_id'])->references(['id'])->on('pedido_deliveries')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('motoboys', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('movimentacao_produtos', function (Blueprint $table) {
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_variacao_id'])->references(['id'])->on('produto_variacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('municipio_carregamentos', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['mdfe_id'])->references(['id'])->on('mdves')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('n_fe_descargas', function (Blueprint $table) {
                    $table->foreign(['info_id'])->references(['id'])->on('info_descargas')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('natureza_operacaos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('nfces', function (Blueprint $table) {
                    $table->foreign(['caixa_id'])->references(['id'])->on('caixas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['natureza_id'])->references(['id'])->on('natureza_operacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['transportadora_id'])->references(['id'])->on('transportadoras')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('nota_servico_configs', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('nota_servicos', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('notas_reservas', function (Blueprint $table) {
                    $table->foreign(['reserva_id'])->references(['id'])->on('reservas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('notificacaos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('notificao_cardapios', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['pedido_id'])->references(['id'])->on('pedidos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('nuvem_shop_configs', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('nuvem_shop_item_pedidos', function (Blueprint $table) {
                    $table->foreign(['pedido_id'])->references(['id'])->on('nuvem_shop_pedidos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('nuvem_shop_pedidos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('nves', function (Blueprint $table) {
                    $table->foreign(['caixa_id'])->references(['id'])->on('caixas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['fornecedor_id'])->references(['id'])->on('fornecedors')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['natureza_id'])->references(['id'])->on('natureza_operacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['transportadora_id'])->references(['id'])->on('transportadoras')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('ordem_producaos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['funcionario_id'])->references(['id'])->on('funcionarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('ordem_servicos', function (Blueprint $table) {
                    $table->foreign(['caixa_id'])->references(['id'])->on('caixas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['funcionario_id'])->references(['id'])->on('funcionarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('otica_os', function (Blueprint $table) {
                    $table->foreign(['ordem_servico_id'])->references(['id'])->on('ordem_servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('padrao_frigobars', function (Blueprint $table) {
                    $table->foreign(['frigobar_id'])->references(['id'])->on('frigobars')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('padrao_tributacao_produtos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('pagamentos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['plano_id'])->references(['id'])->on('planos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('parcelamento_mdves', function (Blueprint $table) {
                    $table->foreign(['mdfe_id'])->references(['id'])->on('mdves')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('pdv_logs', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('pedido_deliveries', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['cupom_id'])->references(['id'])->on('cupom_descontos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['endereco_id'])->references(['id'])->on('endereco_deliveries')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['motoboy_id'])->references(['id'])->on('motoboys')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('pedido_ecommerces', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['endereco_id'])->references(['id'])->on('endereco_ecommerces')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('pedido_mercado_livres', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('pedido_vendi_zaps', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('pedidos', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['funcionario_id'])->references(['id'])->on('funcionarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('percurso_cte_os', function (Blueprint $table) {
                    $table->foreign(['cteos_id'])->references(['id'])->on('cte_os')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('percursos', function (Blueprint $table) {
                    $table->foreign(['mdfe_id'])->references(['id'])->on('mdves')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('planejamento_custo_logs', function (Blueprint $table) {
                    $table->foreign(['planejamento_id'])->references(['id'])->on('planejamento_custos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('planejamento_custos', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('plano_contas', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['plano_conta_id'])->references(['id'])->on('plano_contas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('plano_empresas', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['plano_id'])->references(['id'])->on('planos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('plano_pendentes', function (Blueprint $table) {
                    $table->foreign(['contador_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['plano_id'])->references(['id'])->on('planos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('pre_vendas', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['funcionario_id'])->references(['id'])->on('funcionarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['natureza_id'])->references(['id'])->on('natureza_operacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('produto_adicionals', function (Blueprint $table) {
                    $table->foreign(['adicional_id'])->references(['id'])->on('adicionals')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('produto_combos', function (Blueprint $table) {
                    $table->foreign(['item_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('produto_composicaos', function (Blueprint $table) {
                    $table->foreign(['ingrediente_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('produto_fornecedors', function (Blueprint $table) {
                    $table->foreign(['fornecedor_id'])->references(['id'])->on('fornecedors')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('produto_ibpts', function (Blueprint $table) {
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('produto_ifoods', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('produto_ingredientes', function (Blueprint $table) {
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('produto_localizacaos', function (Blueprint $table) {
                    $table->foreign(['localizacao_id'])->references(['id'])->on('localizacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('produto_os', function (Blueprint $table) {
                    $table->foreign(['ordem_servico_id'])->references(['id'])->on('ordem_servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('produto_pizza_valors', function (Blueprint $table) {
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['tamanho_id'])->references(['id'])->on('tamanho_pizzas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('produto_planejamento_custos', function (Blueprint $table) {
                    $table->foreign(['planejamento_id'])->references(['id'])->on('planejamento_custos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('produto_tributacao_locals', function (Blueprint $table) {
                    $table->foreign(['local_id'])->references(['id'])->on('localizacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('produto_unicos', function (Blueprint $table) {
                    $table->foreign(['nfce_id'])->references(['id'])->on('nfces')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['nfe_id'])->references(['id'])->on('nves')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('produto_variacaos', function (Blueprint $table) {
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('produtos', function (Blueprint $table) {
                    $table->foreign(['categoria_id'])->references(['id'])->on('categoria_produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['marca_id'])->references(['id'])->on('marcas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['padrao_id'])->references(['id'])->on('padrao_tributacao_produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['sub_categoria_id'])->references(['id'])->on('categoria_produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('projeto_custo_configs', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('projeto_custos', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('promocao_produtos', function (Blueprint $table) {
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('registro_tefs', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['nfce_id'])->references(['id'])->on('nfces')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('relacao_dados_fornecedors', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('relatorio_os', function (Blueprint $table) {
                    $table->foreign(['ordem_servico_id'])->references(['id'])->on('ordem_servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('remessa_boleto_items', function (Blueprint $table) {
                    $table->foreign(['boleto_id'])->references(['id'])->on('boletos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['remessa_id'])->references(['id'])->on('remessa_boletos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('remessa_boletos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('reserva_configs', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('reservas', function (Blueprint $table) {
                    $table->foreign(['acomodacao_id'])->references(['id'])->on('acomodacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('retirada_estoques', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('role_has_permissions', function (Blueprint $table) {
                    $table->foreign(['permission_id'])->references(['id'])->on('permissions')->onUpdate('NO ACTION')->onDelete('CASCADE');
                    $table->foreign(['role_id'])->references(['id'])->on('roles')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('roles', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('sangria_caixas', function (Blueprint $table) {
                    $table->foreign(['caixa_id'])->references(['id'])->on('caixas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('segmento_empresas', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['segmento_id'])->references(['id'])->on('segmentos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('servico_os', function (Blueprint $table) {
                    $table->foreign(['ordem_servico_id'])->references(['id'])->on('ordem_servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['servico_id'])->references(['id'])->on('servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('servico_planejamento_custos', function (Blueprint $table) {
                    $table->foreign(['planejamento_id'])->references(['id'])->on('planejamento_custos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['servico_id'])->references(['id'])->on('servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('servico_reservas', function (Blueprint $table) {
                    $table->foreign(['reserva_id'])->references(['id'])->on('reservas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['servico_id'])->references(['id'])->on('servicos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('servicos', function (Blueprint $table) {
                    $table->foreign(['categoria_id'])->references(['id'])->on('categoria_servicos')->onUpdate('NO ACTION')->onDelete('CASCADE');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('sped_configs', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('speds', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('suprimento_caixas', function (Blueprint $table) {
                    $table->foreign(['caixa_id'])->references(['id'])->on('caixas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('tamanho_pizzas', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('taxa_pagamentos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('tef_multi_plus_cards', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('ticket_mensagem_anexos', function (Blueprint $table) {
                    $table->foreign(['ticket_mensagem_id'])->references(['id'])->on('ticket_mensagems')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('ticket_mensagems', function (Blueprint $table) {
                    $table->foreign(['ticket_id'])->references(['id'])->on('tickets')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('tickets', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('tipo_armacaos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('tipo_despesa_fretes', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('tradein_credit_movements', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('SET NULL');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['fornecedor_id'])->references(['id'])->on('fornecedors')->onUpdate('NO ACTION')->onDelete('SET NULL');
                    $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                });

        Schema::table('transferencia_estoques', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['local_entrada_id'])->references(['id'])->on('localizacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['local_saida_id'])->references(['id'])->on('localizacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('transportadoras', function (Blueprint $table) {
                    $table->foreign(['cidade_id'])->references(['id'])->on('cidades')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('tratamento_oticas', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('tributacao_clientes', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('trocas', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['nfce_id'])->references(['id'])->on('nfces')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('unidade_cargas', function (Blueprint $table) {
                    $table->foreign(['info_id'])->references(['id'])->on('info_descargas')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('unidade_medidas', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('usuario_emissaos', function (Blueprint $table) {
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('usuario_empresas', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('CASCADE');
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });

        Schema::table('usuario_localizacaos', function (Blueprint $table) {
                    $table->foreign(['localizacao_id'])->references(['id'])->on('localizacaos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['usuario_id'])->references(['id'])->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('vale_pedagios', function (Blueprint $table) {
                    $table->foreign(['mdfe_id'])->references(['id'])->on('mdves')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('variacao_mercado_livres', function (Blueprint $table) {
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('variacao_modelo_items', function (Blueprint $table) {
                    $table->foreign(['variacao_modelo_id'])->references(['id'])->on('variacao_modelos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('variacao_modelos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('veiculos', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('venda_suspensas', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('vendi_zap_configs', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('woocommerce_configs', function (Blueprint $table) {
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('woocommerce_item_pedidos', function (Blueprint $table) {
                    $table->foreign(['pedido_id'])->references(['id'])->on('woocommerce_pedidos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['produto_id'])->references(['id'])->on('produtos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });

        Schema::table('woocommerce_pedidos', function (Blueprint $table) {
                    $table->foreign(['cliente_id'])->references(['id'])->on('clientes')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                    $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
                });
    }

    public function down()
    {
        Schema::table('woocommerce_pedidos', function (Blueprint $table) {
                    $table->dropForeign('woocommerce_pedidos_cliente_id_foreign');
                    $table->dropForeign('woocommerce_pedidos_empresa_id_foreign');
                });

        Schema::table('woocommerce_item_pedidos', function (Blueprint $table) {
                    $table->dropForeign('woocommerce_item_pedidos_pedido_id_foreign');
                    $table->dropForeign('woocommerce_item_pedidos_produto_id_foreign');
                });

        Schema::table('woocommerce_configs', function (Blueprint $table) {
                    $table->dropForeign('woocommerce_configs_empresa_id_foreign');
                });

        Schema::table('vendi_zap_configs', function (Blueprint $table) {
                    $table->dropForeign('vendi_zap_configs_empresa_id_foreign');
                });

        Schema::table('venda_suspensas', function (Blueprint $table) {
                    $table->dropForeign('venda_suspensas_cliente_id_foreign');
                    $table->dropForeign('venda_suspensas_empresa_id_foreign');
                });

        Schema::table('veiculos', function (Blueprint $table) {
                    $table->dropForeign('veiculos_empresa_id_foreign');
                });

        Schema::table('variacao_modelos', function (Blueprint $table) {
                    $table->dropForeign('variacao_modelos_empresa_id_foreign');
                });

        Schema::table('variacao_modelo_items', function (Blueprint $table) {
                    $table->dropForeign('variacao_modelo_items_variacao_modelo_id_foreign');
                });

        Schema::table('variacao_mercado_livres', function (Blueprint $table) {
                    $table->dropForeign('variacao_mercado_livres_produto_id_foreign');
                });

        Schema::table('vale_pedagios', function (Blueprint $table) {
                    $table->dropForeign('vale_pedagios_mdfe_id_foreign');
                });

        Schema::table('usuario_localizacaos', function (Blueprint $table) {
                    $table->dropForeign('usuario_localizacaos_localizacao_id_foreign');
                    $table->dropForeign('usuario_localizacaos_usuario_id_foreign');
                });

        Schema::table('usuario_empresas', function (Blueprint $table) {
                    $table->dropForeign('usuario_empresas_empresa_id_foreign');
                    $table->dropForeign('usuario_empresas_usuario_id_foreign');
                });

        Schema::table('usuario_emissaos', function (Blueprint $table) {
                    $table->dropForeign('usuario_emissaos_usuario_id_foreign');
                });

        Schema::table('unidade_medidas', function (Blueprint $table) {
                    $table->dropForeign('unidade_medidas_empresa_id_foreign');
                });

        Schema::table('unidade_cargas', function (Blueprint $table) {
                    $table->dropForeign('unidade_cargas_info_id_foreign');
                });

        Schema::table('trocas', function (Blueprint $table) {
                    $table->dropForeign('trocas_empresa_id_foreign');
                    $table->dropForeign('trocas_nfce_id_foreign');
                });

        Schema::table('tributacao_clientes', function (Blueprint $table) {
                    $table->dropForeign('tributacao_clientes_cliente_id_foreign');
                });

        Schema::table('tratamento_oticas', function (Blueprint $table) {
                    $table->dropForeign('tratamento_oticas_empresa_id_foreign');
                });

        Schema::table('transportadoras', function (Blueprint $table) {
                    $table->dropForeign('transportadoras_cidade_id_foreign');
                    $table->dropForeign('transportadoras_empresa_id_foreign');
                });

        Schema::table('transferencia_estoques', function (Blueprint $table) {
                    $table->dropForeign('transferencia_estoques_empresa_id_foreign');
                    $table->dropForeign('transferencia_estoques_local_entrada_id_foreign');
                    $table->dropForeign('transferencia_estoques_local_saida_id_foreign');
                    $table->dropForeign('transferencia_estoques_usuario_id_foreign');
                });

        Schema::table('tradein_credit_movements', function (Blueprint $table) {
                    $table->dropForeign('tradein_credit_movements_cliente_id_foreign');
                    $table->dropForeign('tradein_credit_movements_empresa_id_foreign');
                    $table->dropForeign('tradein_credit_movements_fornecedor_id_foreign');
                    $table->dropForeign('tradein_credit_movements_user_id_foreign');
                });

        Schema::table('tipo_despesa_fretes', function (Blueprint $table) {
                    $table->dropForeign('tipo_despesa_fretes_empresa_id_foreign');
                });

        Schema::table('tipo_armacaos', function (Blueprint $table) {
                    $table->dropForeign('tipo_armacaos_empresa_id_foreign');
                });

        Schema::table('tickets', function (Blueprint $table) {
                    $table->dropForeign('tickets_empresa_id_foreign');
                });

        Schema::table('ticket_mensagems', function (Blueprint $table) {
                    $table->dropForeign('ticket_mensagems_ticket_id_foreign');
                });

        Schema::table('ticket_mensagem_anexos', function (Blueprint $table) {
                    $table->dropForeign('ticket_mensagem_anexos_ticket_mensagem_id_foreign');
                });

        Schema::table('tef_multi_plus_cards', function (Blueprint $table) {
                    $table->dropForeign('tef_multi_plus_cards_empresa_id_foreign');
                    $table->dropForeign('tef_multi_plus_cards_usuario_id_foreign');
                });

        Schema::table('taxa_pagamentos', function (Blueprint $table) {
                    $table->dropForeign('taxa_pagamentos_empresa_id_foreign');
                });

        Schema::table('tamanho_pizzas', function (Blueprint $table) {
                    $table->dropForeign('tamanho_pizzas_empresa_id_foreign');
                });

        Schema::table('suprimento_caixas', function (Blueprint $table) {
                    $table->dropForeign('suprimento_caixas_caixa_id_foreign');
                });

        Schema::table('speds', function (Blueprint $table) {
                    $table->dropForeign('speds_empresa_id_foreign');
                });

        Schema::table('sped_configs', function (Blueprint $table) {
                    $table->dropForeign('sped_configs_empresa_id_foreign');
                });

        Schema::table('servicos', function (Blueprint $table) {
                    $table->dropForeign('servicos_categoria_id_foreign');
                    $table->dropForeign('servicos_empresa_id_foreign');
                });

        Schema::table('servico_reservas', function (Blueprint $table) {
                    $table->dropForeign('servico_reservas_reserva_id_foreign');
                    $table->dropForeign('servico_reservas_servico_id_foreign');
                });

        Schema::table('servico_planejamento_custos', function (Blueprint $table) {
                    $table->dropForeign('servico_planejamento_custos_planejamento_id_foreign');
                    $table->dropForeign('servico_planejamento_custos_servico_id_foreign');
                });

        Schema::table('servico_os', function (Blueprint $table) {
                    $table->dropForeign('servico_os_ordem_servico_id_foreign');
                    $table->dropForeign('servico_os_servico_id_foreign');
                });

        Schema::table('segmento_empresas', function (Blueprint $table) {
                    $table->dropForeign('segmento_empresas_empresa_id_foreign');
                    $table->dropForeign('segmento_empresas_segmento_id_foreign');
                });

        Schema::table('sangria_caixas', function (Blueprint $table) {
                    $table->dropForeign('sangria_caixas_caixa_id_foreign');
                });

        Schema::table('roles', function (Blueprint $table) {
                    $table->dropForeign('roles_empresa_id_foreign');
                });

        Schema::table('role_has_permissions', function (Blueprint $table) {
                    $table->dropForeign('role_has_permissions_permission_id_foreign');
                    $table->dropForeign('role_has_permissions_role_id_foreign');
                });

        Schema::table('retirada_estoques', function (Blueprint $table) {
                    $table->dropForeign('retirada_estoques_empresa_id_foreign');
                    $table->dropForeign('retirada_estoques_produto_id_foreign');
                });

        Schema::table('reservas', function (Blueprint $table) {
                    $table->dropForeign('reservas_acomodacao_id_foreign');
                    $table->dropForeign('reservas_cliente_id_foreign');
                    $table->dropForeign('reservas_empresa_id_foreign');
                });

        Schema::table('reserva_configs', function (Blueprint $table) {
                    $table->dropForeign('reserva_configs_cidade_id_foreign');
                    $table->dropForeign('reserva_configs_empresa_id_foreign');
                });

        Schema::table('remessa_boletos', function (Blueprint $table) {
                    $table->dropForeign('remessa_boletos_empresa_id_foreign');
                });

        Schema::table('remessa_boleto_items', function (Blueprint $table) {
                    $table->dropForeign('remessa_boleto_items_boleto_id_foreign');
                    $table->dropForeign('remessa_boleto_items_remessa_id_foreign');
                });

        Schema::table('relatorio_os', function (Blueprint $table) {
                    $table->dropForeign('relatorio_os_ordem_servico_id_foreign');
                    $table->dropForeign('relatorio_os_usuario_id_foreign');
                });

        Schema::table('relacao_dados_fornecedors', function (Blueprint $table) {
                    $table->dropForeign('relacao_dados_fornecedors_empresa_id_foreign');
                });

        Schema::table('registro_tefs', function (Blueprint $table) {
                    $table->dropForeign('registro_tefs_empresa_id_foreign');
                    $table->dropForeign('registro_tefs_nfce_id_foreign');
                });

        Schema::table('promocao_produtos', function (Blueprint $table) {
                    $table->dropForeign('promocao_produtos_produto_id_foreign');
                });

        Schema::table('projeto_custos', function (Blueprint $table) {
                    $table->dropForeign('projeto_custos_cliente_id_foreign');
                    $table->dropForeign('projeto_custos_empresa_id_foreign');
                });

        Schema::table('projeto_custo_configs', function (Blueprint $table) {
                    $table->dropForeign('projeto_custo_configs_empresa_id_foreign');
                });

        Schema::table('produtos', function (Blueprint $table) {
                    $table->dropForeign('produtos_categoria_id_foreign');
                    $table->dropForeign('produtos_empresa_id_foreign');
                    $table->dropForeign('produtos_marca_id_foreign');
                    $table->dropForeign('produtos_padrao_id_foreign');
                    $table->dropForeign('produtos_sub_categoria_id_foreign');
                });

        Schema::table('produto_variacaos', function (Blueprint $table) {
                    $table->dropForeign('produto_variacaos_produto_id_foreign');
                });

        Schema::table('produto_unicos', function (Blueprint $table) {
                    $table->dropForeign('produto_unicos_nfce_id_foreign');
                    $table->dropForeign('produto_unicos_nfe_id_foreign');
                    $table->dropForeign('produto_unicos_produto_id_foreign');
                });

        Schema::table('produto_tributacao_locals', function (Blueprint $table) {
                    $table->dropForeign('produto_tributacao_locals_local_id_foreign');
                    $table->dropForeign('produto_tributacao_locals_produto_id_foreign');
                });

        Schema::table('produto_planejamento_custos', function (Blueprint $table) {
                    $table->dropForeign('produto_planejamento_custos_planejamento_id_foreign');
                    $table->dropForeign('produto_planejamento_custos_produto_id_foreign');
                });

        Schema::table('produto_pizza_valors', function (Blueprint $table) {
                    $table->dropForeign('produto_pizza_valors_produto_id_foreign');
                    $table->dropForeign('produto_pizza_valors_tamanho_id_foreign');
                });

        Schema::table('produto_os', function (Blueprint $table) {
                    $table->dropForeign('produto_os_ordem_servico_id_foreign');
                    $table->dropForeign('produto_os_produto_id_foreign');
                });

        Schema::table('produto_localizacaos', function (Blueprint $table) {
                    $table->dropForeign('produto_localizacaos_localizacao_id_foreign');
                    $table->dropForeign('produto_localizacaos_produto_id_foreign');
                });

        Schema::table('produto_ingredientes', function (Blueprint $table) {
                    $table->dropForeign('produto_ingredientes_produto_id_foreign');
                });

        Schema::table('produto_ifoods', function (Blueprint $table) {
                    $table->dropForeign('produto_ifoods_empresa_id_foreign');
                    $table->dropForeign('produto_ifoods_produto_id_foreign');
                });

        Schema::table('produto_ibpts', function (Blueprint $table) {
                    $table->dropForeign('produto_ibpts_produto_id_foreign');
                });

        Schema::table('produto_fornecedors', function (Blueprint $table) {
                    $table->dropForeign('produto_fornecedors_fornecedor_id_foreign');
                    $table->dropForeign('produto_fornecedors_produto_id_foreign');
                });

        Schema::table('produto_composicaos', function (Blueprint $table) {
                    $table->dropForeign('produto_composicaos_ingrediente_id_foreign');
                    $table->dropForeign('produto_composicaos_produto_id_foreign');
                });

        Schema::table('produto_combos', function (Blueprint $table) {
                    $table->dropForeign('produto_combos_item_id_foreign');
                    $table->dropForeign('produto_combos_produto_id_foreign');
                });

        Schema::table('produto_adicionals', function (Blueprint $table) {
                    $table->dropForeign('produto_adicionals_adicional_id_foreign');
                    $table->dropForeign('produto_adicionals_produto_id_foreign');
                });

        Schema::table('pre_vendas', function (Blueprint $table) {
                    $table->dropForeign('pre_vendas_cliente_id_foreign');
                    $table->dropForeign('pre_vendas_empresa_id_foreign');
                    $table->dropForeign('pre_vendas_funcionario_id_foreign');
                    $table->dropForeign('pre_vendas_natureza_id_foreign');
                    $table->dropForeign('pre_vendas_usuario_id_foreign');
                });

        Schema::table('plano_pendentes', function (Blueprint $table) {
                    $table->dropForeign('plano_pendentes_contador_id_foreign');
                    $table->dropForeign('plano_pendentes_empresa_id_foreign');
                    $table->dropForeign('plano_pendentes_plano_id_foreign');
                });

        Schema::table('plano_empresas', function (Blueprint $table) {
                    $table->dropForeign('plano_empresas_empresa_id_foreign');
                    $table->dropForeign('plano_empresas_plano_id_foreign');
                });

        Schema::table('plano_contas', function (Blueprint $table) {
                    $table->dropForeign('plano_contas_empresa_id_foreign');
                    $table->dropForeign('plano_contas_plano_conta_id_foreign');
                });

        Schema::table('planejamento_custos', function (Blueprint $table) {
                    $table->dropForeign('planejamento_custos_cliente_id_foreign');
                    $table->dropForeign('planejamento_custos_empresa_id_foreign');
                });

        Schema::table('planejamento_custo_logs', function (Blueprint $table) {
                    $table->dropForeign('planejamento_custo_logs_planejamento_id_foreign');
                });

        Schema::table('percursos', function (Blueprint $table) {
                    $table->dropForeign('percursos_mdfe_id_foreign');
                });

        Schema::table('percurso_cte_os', function (Blueprint $table) {
                    $table->dropForeign('percurso_cte_os_cteos_id_foreign');
                });

        Schema::table('pedidos', function (Blueprint $table) {
                    $table->dropForeign('pedidos_cliente_id_foreign');
                    $table->dropForeign('pedidos_empresa_id_foreign');
                    $table->dropForeign('pedidos_funcionario_id_foreign');
                });

        Schema::table('pedido_vendi_zaps', function (Blueprint $table) {
                    $table->dropForeign('pedido_vendi_zaps_cliente_id_foreign');
                    $table->dropForeign('pedido_vendi_zaps_empresa_id_foreign');
                });

        Schema::table('pedido_mercado_livres', function (Blueprint $table) {
                    $table->dropForeign('pedido_mercado_livres_cliente_id_foreign');
                    $table->dropForeign('pedido_mercado_livres_empresa_id_foreign');
                });

        Schema::table('pedido_ecommerces', function (Blueprint $table) {
                    $table->dropForeign('pedido_ecommerces_cliente_id_foreign');
                    $table->dropForeign('pedido_ecommerces_empresa_id_foreign');
                    $table->dropForeign('pedido_ecommerces_endereco_id_foreign');
                });

        Schema::table('pedido_deliveries', function (Blueprint $table) {
                    $table->dropForeign('pedido_deliveries_cliente_id_foreign');
                    $table->dropForeign('pedido_deliveries_cupom_id_foreign');
                    $table->dropForeign('pedido_deliveries_empresa_id_foreign');
                    $table->dropForeign('pedido_deliveries_endereco_id_foreign');
                    $table->dropForeign('pedido_deliveries_motoboy_id_foreign');
                });

        Schema::table('pdv_logs', function (Blueprint $table) {
                    $table->dropForeign('pdv_logs_empresa_id_foreign');
                    $table->dropForeign('pdv_logs_produto_id_foreign');
                    $table->dropForeign('pdv_logs_usuario_id_foreign');
                });

        Schema::table('parcelamento_mdves', function (Blueprint $table) {
                    $table->dropForeign('parcelamento_mdves_mdfe_id_foreign');
                });

        Schema::table('pagamentos', function (Blueprint $table) {
                    $table->dropForeign('pagamentos_empresa_id_foreign');
                    $table->dropForeign('pagamentos_plano_id_foreign');
                });

        Schema::table('padrao_tributacao_produtos', function (Blueprint $table) {
                    $table->dropForeign('padrao_tributacao_produtos_empresa_id_foreign');
                });

        Schema::table('padrao_frigobars', function (Blueprint $table) {
                    $table->dropForeign('padrao_frigobars_frigobar_id_foreign');
                    $table->dropForeign('padrao_frigobars_produto_id_foreign');
                });

        Schema::table('otica_os', function (Blueprint $table) {
                    $table->dropForeign('otica_os_ordem_servico_id_foreign');
                });

        Schema::table('ordem_servicos', function (Blueprint $table) {
                    $table->dropForeign('ordem_servicos_caixa_id_foreign');
                    $table->dropForeign('ordem_servicos_cliente_id_foreign');
                    $table->dropForeign('ordem_servicos_empresa_id_foreign');
                    $table->dropForeign('ordem_servicos_funcionario_id_foreign');
                    $table->dropForeign('ordem_servicos_usuario_id_foreign');
                });

        Schema::table('ordem_producaos', function (Blueprint $table) {
                    $table->dropForeign('ordem_producaos_empresa_id_foreign');
                    $table->dropForeign('ordem_producaos_funcionario_id_foreign');
                    $table->dropForeign('ordem_producaos_usuario_id_foreign');
                });

        Schema::table('nves', function (Blueprint $table) {
                    $table->dropForeign('nves_caixa_id_foreign');
                    $table->dropForeign('nves_cliente_id_foreign');
                    $table->dropForeign('nves_empresa_id_foreign');
                    $table->dropForeign('nves_fornecedor_id_foreign');
                    $table->dropForeign('nves_natureza_id_foreign');
                    $table->dropForeign('nves_transportadora_id_foreign');
                });

        Schema::table('nuvem_shop_pedidos', function (Blueprint $table) {
                    $table->dropForeign('nuvem_shop_pedidos_empresa_id_foreign');
                });

        Schema::table('nuvem_shop_item_pedidos', function (Blueprint $table) {
                    $table->dropForeign('nuvem_shop_item_pedidos_pedido_id_foreign');
                    $table->dropForeign('nuvem_shop_item_pedidos_produto_id_foreign');
                });

        Schema::table('nuvem_shop_configs', function (Blueprint $table) {
                    $table->dropForeign('nuvem_shop_configs_empresa_id_foreign');
                });

        Schema::table('notificao_cardapios', function (Blueprint $table) {
                    $table->dropForeign('notificao_cardapios_empresa_id_foreign');
                    $table->dropForeign('notificao_cardapios_pedido_id_foreign');
                });

        Schema::table('notificacaos', function (Blueprint $table) {
                    $table->dropForeign('notificacaos_empresa_id_foreign');
                });

        Schema::table('notas_reservas', function (Blueprint $table) {
                    $table->dropForeign('notas_reservas_reserva_id_foreign');
                });

        Schema::table('nota_servicos', function (Blueprint $table) {
                    $table->dropForeign('nota_servicos_cidade_id_foreign');
                    $table->dropForeign('nota_servicos_cliente_id_foreign');
                    $table->dropForeign('nota_servicos_empresa_id_foreign');
                });

        Schema::table('nota_servico_configs', function (Blueprint $table) {
                    $table->dropForeign('nota_servico_configs_cidade_id_foreign');
                    $table->dropForeign('nota_servico_configs_empresa_id_foreign');
                });

        Schema::table('nfces', function (Blueprint $table) {
                    $table->dropForeign('nfces_caixa_id_foreign');
                    $table->dropForeign('nfces_cliente_id_foreign');
                    $table->dropForeign('nfces_empresa_id_foreign');
                    $table->dropForeign('nfces_natureza_id_foreign');
                    $table->dropForeign('nfces_transportadora_id_foreign');
                });

        Schema::table('natureza_operacaos', function (Blueprint $table) {
                    $table->dropForeign('natureza_operacaos_empresa_id_foreign');
                });

        Schema::table('n_fe_descargas', function (Blueprint $table) {
                    $table->dropForeign('n_fe_descargas_info_id_foreign');
                });

        Schema::table('municipio_carregamentos', function (Blueprint $table) {
                    $table->dropForeign('municipio_carregamentos_cidade_id_foreign');
                    $table->dropForeign('municipio_carregamentos_mdfe_id_foreign');
                });

        Schema::table('movimentacao_produtos', function (Blueprint $table) {
                    $table->dropForeign('movimentacao_produtos_produto_id_foreign');
                    $table->dropForeign('movimentacao_produtos_produto_variacao_id_foreign');
                });

        Schema::table('motoboys', function (Blueprint $table) {
                    $table->dropForeign('motoboys_empresa_id_foreign');
                });

        Schema::table('motoboy_comissaos', function (Blueprint $table) {
                    $table->dropForeign('motoboy_comissaos_empresa_id_foreign');
                    $table->dropForeign('motoboy_comissaos_motoboy_id_foreign');
                    $table->dropForeign('motoboy_comissaos_pedido_id_foreign');
                });

        Schema::table('motivo_interrupcaos', function (Blueprint $table) {
                    $table->dropForeign('motivo_interrupcaos_empresa_id_foreign');
                });

        Schema::table('modelo_etiquetas', function (Blueprint $table) {
                    $table->dropForeign('modelo_etiquetas_empresa_id_foreign');
                });

        Schema::table('model_has_roles', function (Blueprint $table) {
                    $table->dropForeign('model_has_roles_role_id_foreign');
                });

        Schema::table('model_has_permissions', function (Blueprint $table) {
                    $table->dropForeign('model_has_permissions_permission_id_foreign');
                });

        Schema::table('meta_resultados', function (Blueprint $table) {
                    $table->dropForeign('meta_resultados_empresa_id_foreign');
                    $table->dropForeign('meta_resultados_funcionario_id_foreign');
                });

        Schema::table('mesas', function (Blueprint $table) {
                    $table->dropForeign('mesas_empresa_id_foreign');
                });

        Schema::table('mercado_livre_perguntas', function (Blueprint $table) {
                    $table->dropForeign('mercado_livre_perguntas_empresa_id_foreign');
                });

        Schema::table('mercado_livre_configs', function (Blueprint $table) {
                    $table->dropForeign('mercado_livre_configs_empresa_id_foreign');
                });

        Schema::table('mensagem_padrao_crms', function (Blueprint $table) {
                    $table->dropForeign('mensagem_padrao_crms_empresa_id_foreign');
                });

        Schema::table('mensagem_agendamento_logs', function (Blueprint $table) {
                    $table->dropForeign('mensagem_agendamento_logs_cliente_id_foreign');
                    $table->dropForeign('mensagem_agendamento_logs_empresa_id_foreign');
                });

        Schema::table('medida_ctes', function (Blueprint $table) {
                    $table->dropForeign('medida_ctes_cte_id_foreign');
                });

        Schema::table('medicos', function (Blueprint $table) {
                    $table->dropForeign('medicos_cidade_id_foreign');
                    $table->dropForeign('medicos_empresa_id_foreign');
                });

        Schema::table('medicao_receita_os', function (Blueprint $table) {
                    $table->dropForeign('medicao_receita_os_ordem_servico_id_foreign');
                });

        Schema::table('mdves', function (Blueprint $table) {
                    $table->dropForeign('mdves_empresa_id_foreign');
                    $table->dropForeign('mdves_veiculo_reboque2_id_foreign');
                    $table->dropForeign('mdves_veiculo_reboque3_id_foreign');
                    $table->dropForeign('mdves_veiculo_reboque_id_foreign');
                    $table->dropForeign('mdves_veiculo_tracao_id_foreign');
                });

        Schema::table('market_place_configs', function (Blueprint $table) {
                    $table->dropForeign('market_place_configs_cidade_id_foreign');
                    $table->dropForeign('market_place_configs_empresa_id_foreign');
                });

        Schema::table('margem_comissaos', function (Blueprint $table) {
                    $table->dropForeign('margem_comissaos_empresa_id_foreign');
                });

        Schema::table('marcas', function (Blueprint $table) {
                    $table->dropForeign('marcas_empresa_id_foreign');
                });

        Schema::table('manutencao_veiculos', function (Blueprint $table) {
                    $table->dropForeign('manutencao_veiculos_empresa_id_foreign');
                    $table->dropForeign('manutencao_veiculos_fornecedor_id_foreign');
                    $table->dropForeign('manutencao_veiculos_veiculo_id_foreign');
                });

        Schema::table('manutencao_veiculo_servicos', function (Blueprint $table) {
                    $table->dropForeign('manutencao_veiculo_servicos_manutencao_id_foreign');
                    $table->dropForeign('manutencao_veiculo_servicos_servico_id_foreign');
                });

        Schema::table('manutencao_veiculo_produtos', function (Blueprint $table) {
                    $table->dropForeign('manutencao_veiculo_produtos_manutencao_id_foreign');
                    $table->dropForeign('manutencao_veiculo_produtos_produto_id_foreign');
                });

        Schema::table('manutencao_veiculo_anexos', function (Blueprint $table) {
                    $table->dropForeign('manutencao_veiculo_anexos_manutencao_id_foreign');
                });

        Schema::table('manifesto_dves', function (Blueprint $table) {
                    $table->dropForeign('manifesto_dves_empresa_id_foreign');
                });

        Schema::table('log_boletos', function (Blueprint $table) {
                    $table->dropForeign('log_boletos_empresa_id_foreign');
                });

        Schema::table('localizacaos', function (Blueprint $table) {
                    $table->dropForeign('localizacaos_cidade_id_foreign');
                    $table->dropForeign('localizacaos_empresa_id_foreign');
                });

        Schema::table('lista_precos', function (Blueprint $table) {
                    $table->dropForeign('lista_precos_empresa_id_foreign');
                    $table->dropForeign('lista_precos_funcionario_id_foreign');
                });

        Schema::table('lista_preco_usuarios', function (Blueprint $table) {
                    $table->dropForeign('lista_preco_usuarios_lista_preco_id_foreign');
                    $table->dropForeign('lista_preco_usuarios_usuario_id_foreign');
                });

        Schema::table('lacre_unidade_cargas', function (Blueprint $table) {
                    $table->dropForeign('lacre_unidade_cargas_info_id_foreign');
                });

        Schema::table('lacre_transportes', function (Blueprint $table) {
                    $table->dropForeign('lacre_transportes_info_id_foreign');
                });

        Schema::table('laboratorios', function (Blueprint $table) {
                    $table->dropForeign('laboratorios_cidade_id_foreign');
                    $table->dropForeign('laboratorios_empresa_id_foreign');
                });

        Schema::table('item_venda_suspensas', function (Blueprint $table) {
                    $table->dropForeign('item_venda_suspensas_produto_id_foreign');
                    $table->dropForeign('item_venda_suspensas_variacao_id_foreign');
                    $table->dropForeign('item_venda_suspensas_venda_id_foreign');
                });

        Schema::table('item_trocas', function (Blueprint $table) {
                    $table->dropForeign('item_trocas_produto_id_foreign');
                    $table->dropForeign('item_trocas_troca_id_foreign');
                });

        Schema::table('item_transferencia_estoques', function (Blueprint $table) {
                    $table->dropForeign('item_transferencia_estoques_produto_id_foreign');
                    $table->dropForeign('item_transferencia_estoques_transferencia_id_foreign');
                });

        Schema::table('item_servico_nfces', function (Blueprint $table) {
                    $table->dropForeign('item_servico_nfces_nfce_id_foreign');
                    $table->dropForeign('item_servico_nfces_servico_id_foreign');
                });

        Schema::table('item_proposta_planejamento_custos', function (Blueprint $table) {
                    $table->dropForeign('item_proposta_planejamento_custos_planejamento_id_foreign');
                });

        Schema::table('item_producaos', function (Blueprint $table) {
                    $table->dropForeign('item_producaos_produto_id_foreign');
                });

        Schema::table('item_pre_vendas', function (Blueprint $table) {
                    $table->dropForeign('item_pre_vendas_pre_venda_id_foreign');
                    $table->dropForeign('item_pre_vendas_produto_id_foreign');
                    $table->dropForeign('item_pre_vendas_variacao_id_foreign');
                });

        Schema::table('item_pizza_pedidos', function (Blueprint $table) {
                    $table->dropForeign('item_pizza_pedidos_item_pedido_id_foreign');
                    $table->dropForeign('item_pizza_pedidos_produto_id_foreign');
                });

        Schema::table('item_pizza_pedido_deliveries', function (Blueprint $table) {
                    $table->dropForeign('item_pizza_pedido_deliveries_item_pedido_id_foreign');
                    $table->dropForeign('item_pizza_pedido_deliveries_produto_id_foreign');
                });

        Schema::table('item_pizza_nfces', function (Blueprint $table) {
                    $table->dropForeign('item_pizza_nfces_item_nfce_id_foreign');
                    $table->dropForeign('item_pizza_nfces_produto_id_foreign');
                });

        Schema::table('item_pizza_carrinhos', function (Blueprint $table) {
                    $table->dropForeign('item_pizza_carrinhos_item_carrinho_id_foreign');
                    $table->dropForeign('item_pizza_carrinhos_produto_id_foreign');
                });

        Schema::table('item_pizza_carrinho_cardapios', function (Blueprint $table) {
                    $table->dropForeign('item_pizza_carrinho_cardapios_item_carrinho_id_foreign');
                    $table->dropForeign('item_pizza_carrinho_cardapios_produto_id_foreign');
                });

        Schema::table('item_pedidos', function (Blueprint $table) {
                    $table->dropForeign('item_pedidos_funcionario_id_foreign');
                    $table->dropForeign('item_pedidos_pedido_id_foreign');
                    $table->dropForeign('item_pedidos_produto_id_foreign');
                    $table->dropForeign('item_pedidos_tamanho_id_foreign');
                });

        Schema::table('item_pedido_vendi_zaps', function (Blueprint $table) {
                    $table->dropForeign('item_pedido_vendi_zaps_pedido_id_foreign');
                    $table->dropForeign('item_pedido_vendi_zaps_produto_id_foreign');
                });

        Schema::table('item_pedido_servicos', function (Blueprint $table) {
                    $table->dropForeign('item_pedido_servicos_pedido_id_foreign');
                    $table->dropForeign('item_pedido_servicos_servico_id_foreign');
                });

        Schema::table('item_pedido_mercado_livres', function (Blueprint $table) {
                    $table->dropForeign('item_pedido_mercado_livres_pedido_id_foreign');
                    $table->dropForeign('item_pedido_mercado_livres_produto_id_foreign');
                });

        Schema::table('item_pedido_ecommerces', function (Blueprint $table) {
                    $table->dropForeign('item_pedido_ecommerces_pedido_id_foreign');
                    $table->dropForeign('item_pedido_ecommerces_produto_id_foreign');
                    $table->dropForeign('item_pedido_ecommerces_variacao_id_foreign');
                });

        Schema::table('item_pedido_deliveries', function (Blueprint $table) {
                    $table->dropForeign('item_pedido_deliveries_pedido_id_foreign');
                    $table->dropForeign('item_pedido_deliveries_produto_id_foreign');
                    $table->dropForeign('item_pedido_deliveries_tamanho_id_foreign');
                });

        Schema::table('item_ordem_producaos', function (Blueprint $table) {
                    $table->dropForeign('item_ordem_producaos_item_producao_id_foreign');
                    $table->dropForeign('item_ordem_producaos_ordem_producao_id_foreign');
                    $table->dropForeign('item_ordem_producaos_produto_id_foreign');
                });

        Schema::table('item_nves', function (Blueprint $table) {
                    $table->dropForeign('item_nves_nfe_id_foreign');
                    $table->dropForeign('item_nves_produto_id_foreign');
                    $table->dropForeign('item_nves_variacao_id_foreign');
                });

        Schema::table('item_nota_servicos', function (Blueprint $table) {
                    $table->dropForeign('item_nota_servicos_nota_servico_id_foreign');
                    $table->dropForeign('item_nota_servicos_servico_id_foreign');
                });

        Schema::table('item_nfces', function (Blueprint $table) {
                    $table->dropForeign('item_nfces_nfce_id_foreign');
                    $table->dropForeign('item_nfces_produto_id_foreign');
                    $table->dropForeign('item_nfces_variacao_id_foreign');
                });

        Schema::table('item_lista_precos', function (Blueprint $table) {
                    $table->dropForeign('item_lista_precos_lista_id_foreign');
                    $table->dropForeign('item_lista_precos_produto_id_foreign');
                });

        Schema::table('item_inventarios', function (Blueprint $table) {
                    $table->dropForeign('item_inventarios_inventario_id_foreign');
                    $table->dropForeign('item_inventarios_produto_id_foreign');
                    $table->dropForeign('item_inventarios_usuario_id_foreign');
                });

        Schema::table('item_inventario_impressaos', function (Blueprint $table) {
                    $table->dropForeign('item_inventario_impressaos_inventario_id_foreign');
                    $table->dropForeign('item_inventario_impressaos_produto_id_foreign');
                    $table->dropForeign('item_inventario_impressaos_usuario_id_foreign');
                });

        Schema::table('item_ibpts', function (Blueprint $table) {
                    $table->dropForeign('item_ibpts_ibpt_id_foreign');
                });

        Schema::table('item_dimensao_nves', function (Blueprint $table) {
                    $table->dropForeign('item_dimensao_nves_item_nfe_id_foreign');
                });

        Schema::table('item_cotacaos', function (Blueprint $table) {
                    $table->dropForeign('item_cotacaos_cotacao_id_foreign');
                    $table->dropForeign('item_cotacaos_produto_id_foreign');
                });

        Schema::table('item_conta_empresas', function (Blueprint $table) {
                    $table->dropForeign('item_conta_empresas_conta_id_foreign');
                });

        Schema::table('item_carrinhos', function (Blueprint $table) {
                    $table->dropForeign('item_carrinhos_carrinho_id_foreign');
                    $table->dropForeign('item_carrinhos_produto_id_foreign');
                    $table->dropForeign('item_carrinhos_variacao_id_foreign');
                });

        Schema::table('item_carrinho_deliveries', function (Blueprint $table) {
                    $table->dropForeign('item_carrinho_deliveries_carrinho_id_foreign');
                    $table->dropForeign('item_carrinho_deliveries_produto_id_foreign');
                    $table->dropForeign('item_carrinho_deliveries_tamanho_id_foreign');
                });

        Schema::table('item_carrinho_cardapios', function (Blueprint $table) {
                    $table->dropForeign('item_carrinho_cardapios_carrinho_id_foreign');
                    $table->dropForeign('item_carrinho_cardapios_produto_id_foreign');
                    $table->dropForeign('item_carrinho_cardapios_tamanho_id_foreign');
                });

        Schema::table('item_carrinho_adicional_deliveries', function (Blueprint $table) {
                    $table->dropForeign('item_carrinho_adicional_deliveries_adicional_id_foreign');
                    $table->dropForeign('item_carrinho_adicional_deliveries_item_carrinho_id_foreign');
                });

        Schema::table('item_carrinho_adicional_cardapios', function (Blueprint $table) {
                    $table->dropForeign('item_carrinho_adicional_cardapios_adicional_id_foreign');
                    $table->dropForeign('item_carrinho_adicional_cardapios_item_carrinho_id_foreign');
                });

        Schema::table('item_agendamentos', function (Blueprint $table) {
                    $table->dropForeign('item_agendamentos_agendamento_id_foreign');
                    $table->dropForeign('item_agendamentos_servico_id_foreign');
                });

        Schema::table('item_adicionals', function (Blueprint $table) {
                    $table->dropForeign('item_adicionals_adicional_id_foreign');
                    $table->dropForeign('item_adicionals_item_pedido_id_foreign');
                });

        Schema::table('item_adicional_nfces', function (Blueprint $table) {
                    $table->dropForeign('item_adicional_nfces_adicional_id_foreign');
                    $table->dropForeign('item_adicional_nfces_item_nfce_id_foreign');
                });

        Schema::table('item_adicional_deliveries', function (Blueprint $table) {
                    $table->dropForeign('item_adicional_deliveries_adicional_id_foreign');
                    $table->dropForeign('item_adicional_deliveries_item_pedido_id_foreign');
                });

        Schema::table('inventarios', function (Blueprint $table) {
                    $table->dropForeign('inventarios_empresa_id_foreign');
                    $table->dropForeign('inventarios_usuario_id_foreign');
                });

        Schema::table('inutilizacaos', function (Blueprint $table) {
                    $table->dropForeign('inutilizacaos_empresa_id_foreign');
                });

        Schema::table('interrupcoes', function (Blueprint $table) {
                    $table->dropForeign('interrupcoes_empresa_id_foreign');
                    $table->dropForeign('interrupcoes_funcionario_id_foreign');
                });

        Schema::table('informacao_bancaria_mdves', function (Blueprint $table) {
                    $table->dropForeign('informacao_bancaria_mdves_mdfe_id_foreign');
                });

        Schema::table('info_descargas', function (Blueprint $table) {
                    $table->dropForeign('info_descargas_cidade_id_foreign');
                    $table->dropForeign('info_descargas_mdfe_id_foreign');
                });

        Schema::table('impressora_pedidos', function (Blueprint $table) {
                    $table->dropForeign('impressora_pedidos_empresa_id_foreign');
                });

        Schema::table('impressora_pedido_produtos', function (Blueprint $table) {
                    $table->dropForeign('impressora_pedido_produtos_impressora_id_foreign');
                    $table->dropForeign('impressora_pedido_produtos_produto_id_foreign');
                });

        Schema::table('ifood_configs', function (Blueprint $table) {
                    $table->dropForeign('ifood_configs_empresa_id_foreign');
                });

        Schema::table('hospede_reservas', function (Blueprint $table) {
                    $table->dropForeign('hospede_reservas_cidade_id_foreign');
                    $table->dropForeign('hospede_reservas_reserva_id_foreign');
                });

        Schema::table('gestao_custo_producaos', function (Blueprint $table) {
                    $table->dropForeign('gestao_custo_producaos_cliente_id_foreign');
                    $table->dropForeign('gestao_custo_producaos_empresa_id_foreign');
                    $table->dropForeign('gestao_custo_producaos_produto_id_foreign');
                });

        Schema::table('gestao_custo_producao_servicos', function (Blueprint $table) {
                    $table->dropForeign('gestao_custo_producao_servicos_gestao_custo_id_foreign');
                    $table->dropForeign('gestao_custo_producao_servicos_servico_id_foreign');
                });

        Schema::table('gestao_custo_producao_produtos', function (Blueprint $table) {
                    $table->dropForeign('gestao_custo_producao_produtos_gestao_custo_id_foreign');
                    $table->dropForeign('gestao_custo_producao_produtos_produto_id_foreign');
                });

        Schema::table('gestao_custo_producao_outro_custos', function (Blueprint $table) {
                    $table->dropForeign('gestao_custo_producao_outro_custos_gestao_custo_id_foreign');
                });

        Schema::table('garantias', function (Blueprint $table) {
                    $table->dropForeign('garantias_cliente_id_foreign');
                    $table->dropForeign('garantias_empresa_id_foreign');
                    $table->dropForeign('garantias_produto_id_foreign');
                    $table->dropForeign('garantias_usuario_id_foreign');
                });

        Schema::table('galeria_produtos', function (Blueprint $table) {
                    $table->dropForeign('galeria_produtos_produto_id_foreign');
                });

        Schema::table('funcionarios', function (Blueprint $table) {
                    $table->dropForeign('funcionarios_cidade_id_foreign');
                    $table->dropForeign('funcionarios_empresa_id_foreign');
                    $table->dropForeign('funcionarios_usuario_id_foreign');
                });

        Schema::table('funcionario_servicos', function (Blueprint $table) {
                    $table->dropForeign('funcionario_servicos_empresa_id_foreign');
                    $table->dropForeign('funcionario_servicos_funcionario_id_foreign');
                    $table->dropForeign('funcionario_servicos_servico_id_foreign');
                });

        Schema::table('funcionario_os', function (Blueprint $table) {
                    $table->dropForeign('funcionario_os_funcionario_id_foreign');
                    $table->dropForeign('funcionario_os_ordem_servico_id_foreign');
                    $table->dropForeign('funcionario_os_usuario_id_foreign');
                });

        Schema::table('funcionario_eventos', function (Blueprint $table) {
                    $table->dropForeign('funcionario_eventos_evento_id_foreign');
                    $table->dropForeign('funcionario_eventos_funcionario_id_foreign');
                });

        Schema::table('funcionamentos', function (Blueprint $table) {
                    $table->dropForeign('funcionamentos_funcionario_id_foreign');
                });

        Schema::table('funcionamento_deliveries', function (Blueprint $table) {
                    $table->dropForeign('funcionamento_deliveries_empresa_id_foreign');
                });

        Schema::table('frigobars', function (Blueprint $table) {
                    $table->dropForeign('frigobars_acomodacao_id_foreign');
                    $table->dropForeign('frigobars_empresa_id_foreign');
                });

        Schema::table('fretes', function (Blueprint $table) {
                    $table->dropForeign('fretes_cidade_id_foreign');
                    $table->dropForeign('fretes_cliente_id_foreign');
                    $table->dropForeign('fretes_empresa_id_foreign');
                    $table->dropForeign('fretes_veiculo_id_foreign');
                });

        Schema::table('frete_anexos', function (Blueprint $table) {
                    $table->dropForeign('frete_anexos_frete_id_foreign');
                });

        Schema::table('fornecedors', function (Blueprint $table) {
                    $table->dropForeign('fornecedors_cidade_id_foreign');
                    $table->dropForeign('fornecedors_empresa_id_foreign');
                });

        Schema::table('formato_armacao_oticas', function (Blueprint $table) {
                    $table->dropForeign('formato_armacao_oticas_empresa_id_foreign');
                });

        Schema::table('financeiro_planos', function (Blueprint $table) {
                    $table->dropForeign('financeiro_planos_empresa_id_foreign');
                    $table->dropForeign('financeiro_planos_plano_id_foreign');
                });

        Schema::table('financeiro_contadors', function (Blueprint $table) {
                    $table->dropForeign('financeiro_contadors_contador_id_foreign');
                });

        Schema::table('financeiro_boletos', function (Blueprint $table) {
                    $table->dropForeign('financeiro_boletos_empresa_id_foreign');
                });

        Schema::table('fila_envio_crons', function (Blueprint $table) {
                    $table->dropForeign('fila_envio_crons_cliente_id_foreign');
                    $table->dropForeign('fila_envio_crons_empresa_id_foreign');
                });

        Schema::table('fatura_reservas', function (Blueprint $table) {
                    $table->dropForeign('fatura_reservas_reserva_id_foreign');
                });

        Schema::table('fatura_pre_vendas', function (Blueprint $table) {
                    $table->dropForeign('fatura_pre_vendas_pre_venda_id_foreign');
                });

        Schema::table('fatura_ordem_servicos', function (Blueprint $table) {
                    $table->dropForeign('fatura_ordem_servicos_ordem_servico_id_foreign');
                });

        Schema::table('fatura_nves', function (Blueprint $table) {
                    $table->dropForeign('fatura_nves_nfe_id_foreign');
                });

        Schema::table('fatura_nfces', function (Blueprint $table) {
                    $table->dropForeign('fatura_nfces_nfce_id_foreign');
                });

        Schema::table('fatura_cotacaos', function (Blueprint $table) {
                    $table->dropForeign('fatura_cotacaos_cotacao_id_foreign');
                });

        Schema::table('fatura_clientes', function (Blueprint $table) {
                    $table->dropForeign('fatura_clientes_cliente_id_foreign');
                });

        Schema::table('evento_salarios', function (Blueprint $table) {
                    $table->dropForeign('evento_salarios_empresa_id_foreign');
                });

        Schema::table('etiqueta_configuracaos', function (Blueprint $table) {
                    $table->dropForeign('etiqueta_configuracaos_empresa_id_foreign');
                });

        Schema::table('estoques', function (Blueprint $table) {
                    $table->dropForeign('estoques_produto_id_foreign');
                    $table->dropForeign('estoques_produto_variacao_id_foreign');
                });

        Schema::table('estoque_atual_produtos', function (Blueprint $table) {
                    $table->dropForeign('estoque_atual_produtos_produto_id_foreign');
                });

        Schema::table('escritorio_contabils', function (Blueprint $table) {
                    $table->dropForeign('escritorio_contabils_cidade_id_foreign');
                    $table->dropForeign('escritorio_contabils_empresa_id_foreign');
                });

        Schema::table('endereco_ecommerces', function (Blueprint $table) {
                    $table->dropForeign('endereco_ecommerces_cidade_id_foreign');
                    $table->dropForeign('endereco_ecommerces_cliente_id_foreign');
                });

        Schema::table('endereco_deliveries', function (Blueprint $table) {
                    $table->dropForeign('endereco_deliveries_bairro_id_foreign');
                    $table->dropForeign('endereco_deliveries_cidade_id_foreign');
                    $table->dropForeign('endereco_deliveries_cliente_id_foreign');
                });

        Schema::table('empresas', function (Blueprint $table) {
                    $table->dropForeign('empresas_cidade_id_foreign');
                });

        Schema::table('email_configs', function (Blueprint $table) {
                    $table->dropForeign('email_configs_empresa_id_foreign');
                });

        Schema::table('ecommerce_configs', function (Blueprint $table) {
                    $table->dropForeign('ecommerce_configs_cidade_id_foreign');
                    $table->dropForeign('ecommerce_configs_empresa_id_foreign');
                });

        Schema::table('difals', function (Blueprint $table) {
                    $table->dropForeign('difals_empresa_id_foreign');
                });

        Schema::table('dia_semanas', function (Blueprint $table) {
                    $table->dropForeign('dia_semanas_empresa_id_foreign');
                    $table->dropForeign('dia_semanas_funcionario_id_foreign');
                });

        Schema::table('destaque_market_places', function (Blueprint $table) {
                    $table->dropForeign('destaque_market_places_empresa_id_foreign');
                    $table->dropForeign('destaque_market_places_produto_id_foreign');
                    $table->dropForeign('destaque_market_places_servico_id_foreign');
                });

        Schema::table('despesa_fretes', function (Blueprint $table) {
                    $table->dropForeign('despesa_fretes_fornecedor_id_foreign');
                    $table->dropForeign('despesa_fretes_frete_id_foreign');
                    $table->dropForeign('despesa_fretes_tipo_despesa_id_foreign');
                });

        Schema::table('custo_adm_planejamento_custos', function (Blueprint $table) {
                    $table->dropForeign('custo_adm_planejamento_custos_planejamento_id_foreign');
                });

        Schema::table('cupom_descontos', function (Blueprint $table) {
                    $table->dropForeign('cupom_descontos_cliente_id_foreign');
                    $table->dropForeign('cupom_descontos_empresa_id_foreign');
                });

        Schema::table('cupom_desconto_clientes', function (Blueprint $table) {
                    $table->dropForeign('cupom_desconto_clientes_cliente_id_foreign');
                    $table->dropForeign('cupom_desconto_clientes_cupom_id_foreign');
                    $table->dropForeign('cupom_desconto_clientes_empresa_id_foreign');
                    $table->dropForeign('cupom_desconto_clientes_pedido_id_foreign');
                });

        Schema::table('ctes', function (Blueprint $table) {
                    $table->dropForeign('ctes_destinatario_id_foreign');
                    $table->dropForeign('ctes_empresa_id_foreign');
                    $table->dropForeign('ctes_expedidor_id_foreign');
                    $table->dropForeign('ctes_municipio_envio_foreign');
                    $table->dropForeign('ctes_municipio_fim_foreign');
                    $table->dropForeign('ctes_municipio_inicio_foreign');
                    $table->dropForeign('ctes_municipio_tomador_foreign');
                    $table->dropForeign('ctes_natureza_id_foreign');
                    $table->dropForeign('ctes_recebedor_id_foreign');
                    $table->dropForeign('ctes_remetente_id_foreign');
                    $table->dropForeign('ctes_veiculo_id_foreign');
                });

        Schema::table('cte_os', function (Blueprint $table) {
                    $table->dropForeign('cte_os_emitente_id_foreign');
                    $table->dropForeign('cte_os_empresa_id_foreign');
                    $table->dropForeign('cte_os_municipio_envio_foreign');
                    $table->dropForeign('cte_os_municipio_fim_foreign');
                    $table->dropForeign('cte_os_municipio_inicio_foreign');
                    $table->dropForeign('cte_os_natureza_id_foreign');
                    $table->dropForeign('cte_os_tomador_id_foreign');
                    $table->dropForeign('cte_os_usuario_id_foreign');
                    $table->dropForeign('cte_os_veiculo_id_foreign');
                });

        Schema::table('crm_anotacaos', function (Blueprint $table) {
                    $table->dropForeign('crm_anotacaos_cliente_id_foreign');
                    $table->dropForeign('crm_anotacaos_empresa_id_foreign');
                    $table->dropForeign('crm_anotacaos_fornecedor_id_foreign');
                });

        Schema::table('crm_anotacao_notas', function (Blueprint $table) {
                    $table->dropForeign('crm_anotacao_notas_crm_anotacao_id_foreign');
                });

        Schema::table('credito_clientes', function (Blueprint $table) {
                    $table->dropForeign('credito_clientes_cliente_id_foreign');
                });

        Schema::table('cotacaos', function (Blueprint $table) {
                    $table->dropForeign('cotacaos_empresa_id_foreign');
                    $table->dropForeign('cotacaos_fornecedor_id_foreign');
                });

        Schema::table('convenios', function (Blueprint $table) {
                    $table->dropForeign('convenios_empresa_id_foreign');
                });

        Schema::table('contrato_empresas', function (Blueprint $table) {
                    $table->dropForeign('contrato_empresas_empresa_id_foreign');
                });

        Schema::table('contigencias', function (Blueprint $table) {
                    $table->dropForeign('contigencias_empresa_id_foreign');
                });

        Schema::table('contador_empresas', function (Blueprint $table) {
                    $table->dropForeign('contador_empresas_contador_id_foreign');
                    $table->dropForeign('contador_empresas_empresa_id_foreign');
                });

        Schema::table('conta_recebers', function (Blueprint $table) {
                    $table->dropForeign('conta_recebers_caixa_id_foreign');
                    $table->dropForeign('conta_recebers_cliente_id_foreign');
                    $table->dropForeign('conta_recebers_empresa_id_foreign');
                    $table->dropForeign('conta_recebers_nfce_id_foreign');
                    $table->dropForeign('conta_recebers_nfe_id_foreign');
                });

        Schema::table('conta_pagars', function (Blueprint $table) {
                    $table->dropForeign('conta_pagars_caixa_id_foreign');
                    $table->dropForeign('conta_pagars_empresa_id_foreign');
                    $table->dropForeign('conta_pagars_fornecedor_id_foreign');
                    $table->dropForeign('conta_pagars_nfe_id_foreign');
                });

        Schema::table('conta_empresas', function (Blueprint $table) {
                    $table->dropForeign('conta_empresas_empresa_id_foreign');
                });

        Schema::table('conta_boletos', function (Blueprint $table) {
                    $table->dropForeign('conta_boletos_cidade_id_foreign');
                    $table->dropForeign('conta_boletos_empresa_id_foreign');
                });

        Schema::table('consumo_reservas', function (Blueprint $table) {
                    $table->dropForeign('consumo_reservas_produto_id_foreign');
                    $table->dropForeign('consumo_reservas_reserva_id_foreign');
                });

        Schema::table('configuracao_cardapios', function (Blueprint $table) {
                    $table->dropForeign('configuracao_cardapios_cidade_id_foreign');
                    $table->dropForeign('configuracao_cardapios_empresa_id_foreign');
                });

        Schema::table('configuracao_agendamentos', function (Blueprint $table) {
                    $table->dropForeign('configuracao_agendamentos_empresa_id_foreign');
                });

        Schema::table('config_gerals', function (Blueprint $table) {
                    $table->dropForeign('config_gerals_empresa_id_foreign');
                });

        Schema::table('componente_mdves', function (Blueprint $table) {
                    $table->dropForeign('componente_mdves_mdfe_id_foreign');
                });

        Schema::table('componente_ctes', function (Blueprint $table) {
                    $table->dropForeign('componente_ctes_cte_id_foreign');
                });

        Schema::table('comissao_vendas', function (Blueprint $table) {
                    $table->dropForeign('comissao_vendas_empresa_id_foreign');
                    $table->dropForeign('comissao_vendas_funcionario_id_foreign');
                });

        Schema::table('clientes', function (Blueprint $table) {
                    $table->dropForeign('clientes_cidade_id_foreign');
                    $table->dropForeign('clientes_empresa_id_foreign');
                });

        Schema::table('ciots', function (Blueprint $table) {
                    $table->dropForeign('ciots_mdfe_id_foreign');
                });

        Schema::table('chave_nfe_ctes', function (Blueprint $table) {
                    $table->dropForeign('chave_nfe_ctes_cte_id_foreign');
                });

        Schema::table('categoria_woocommerces', function (Blueprint $table) {
                    $table->dropForeign('categoria_woocommerces_empresa_id_foreign');
                });

        Schema::table('categoria_vendi_zaps', function (Blueprint $table) {
                    $table->dropForeign('categoria_vendi_zaps_empresa_id_foreign');
                });

        Schema::table('categoria_servicos', function (Blueprint $table) {
                    $table->dropForeign('categoria_servicos_empresa_id_foreign');
                });

        Schema::table('categoria_produtos', function (Blueprint $table) {
                    $table->dropForeign('categoria_produtos_categoria_id_foreign');
                    $table->dropForeign('categoria_produtos_empresa_id_foreign');
                });

        Schema::table('categoria_produto_ifoods', function (Blueprint $table) {
                    $table->dropForeign('categoria_produto_ifoods_empresa_id_foreign');
                });

        Schema::table('categoria_nuvem_shops', function (Blueprint $table) {
                    $table->dropForeign('categoria_nuvem_shops_empresa_id_foreign');
                });

        Schema::table('categoria_contas', function (Blueprint $table) {
                    $table->dropForeign('categoria_contas_empresa_id_foreign');
                });

        Schema::table('categoria_adicionals', function (Blueprint $table) {
                    $table->dropForeign('categoria_adicionals_empresa_id_foreign');
                });

        Schema::table('categoria_acomodacaos', function (Blueprint $table) {
                    $table->dropForeign('categoria_acomodacaos_empresa_id_foreign');
                });

        Schema::table('cash_back_configs', function (Blueprint $table) {
                    $table->dropForeign('cash_back_configs_empresa_id_foreign');
                });

        Schema::table('cash_back_clientes', function (Blueprint $table) {
                    $table->dropForeign('cash_back_clientes_cliente_id_foreign');
                    $table->dropForeign('cash_back_clientes_empresa_id_foreign');
                });

        Schema::table('carrossel_cardapios', function (Blueprint $table) {
                    $table->dropForeign('carrossel_cardapios_empresa_id_foreign');
                    $table->dropForeign('carrossel_cardapios_produto_id_foreign');
                });

        Schema::table('carrinhos', function (Blueprint $table) {
                    $table->dropForeign('carrinhos_cliente_id_foreign');
                    $table->dropForeign('carrinhos_empresa_id_foreign');
                    $table->dropForeign('carrinhos_endereco_id_foreign');
                });

        Schema::table('carrinho_deliveries', function (Blueprint $table) {
                    $table->dropForeign('carrinho_deliveries_cliente_id_foreign');
                    $table->dropForeign('carrinho_deliveries_empresa_id_foreign');
                    $table->dropForeign('carrinho_deliveries_endereco_id_foreign');
                });

        Schema::table('carrinho_cardapios', function (Blueprint $table) {
                    $table->dropForeign('carrinho_cardapios_empresa_id_foreign');
                });

        Schema::table('caixas', function (Blueprint $table) {
                    $table->dropForeign('caixas_empresa_id_foreign');
                    $table->dropForeign('caixas_usuario_id_foreign');
                });

        Schema::table('c_te_descargas', function (Blueprint $table) {
                    $table->dropForeign('c_te_descargas_info_id_foreign');
                });

        Schema::table('boletos', function (Blueprint $table) {
                    $table->dropForeign('boletos_conta_boleto_id_foreign');
                    $table->dropForeign('boletos_conta_receber_id_foreign');
                    $table->dropForeign('boletos_empresa_id_foreign');
                });

        Schema::table('bairro_delivery_masters', function (Blueprint $table) {
                    $table->dropForeign('bairro_delivery_masters_cidade_id_foreign');
                });

        Schema::table('bairro_deliveries', function (Blueprint $table) {
                    $table->dropForeign('bairro_deliveries_empresa_id_foreign');
                });

        Schema::table('apuracao_mensals', function (Blueprint $table) {
                    $table->dropForeign('apuracao_mensals_funcionario_id_foreign');
                });

        Schema::table('apuracao_mensal_eventos', function (Blueprint $table) {
                    $table->dropForeign('apuracao_mensal_eventos_apuracao_id_foreign');
                    $table->dropForeign('apuracao_mensal_eventos_evento_id_foreign');
                });

        Schema::table('apontamentos', function (Blueprint $table) {
                    $table->dropForeign('apontamentos_produto_id_foreign');
                });

        Schema::table('api_logs', function (Blueprint $table) {
                    $table->dropForeign('api_logs_empresa_id_foreign');
                });

        Schema::table('api_configs', function (Blueprint $table) {
                    $table->dropForeign('api_configs_empresa_id_foreign');
                });

        Schema::table('agendamentos', function (Blueprint $table) {
                    $table->dropForeign('agendamentos_cliente_id_foreign');
                    $table->dropForeign('agendamentos_empresa_id_foreign');
                    $table->dropForeign('agendamentos_funcionario_id_foreign');
                });

        Schema::table('adicionals', function (Blueprint $table) {
                    $table->dropForeign('adicionals_empresa_id_foreign');
                });

        Schema::table('acomodacaos', function (Blueprint $table) {
                    $table->dropForeign('acomodacaos_categoria_id_foreign');
                    $table->dropForeign('acomodacaos_empresa_id_foreign');
                });

        Schema::table('acesso_logs', function (Blueprint $table) {
                    $table->dropForeign('acesso_logs_usuario_id_foreign');
                });

        Schema::table('acao_logs', function (Blueprint $table) {
                    $table->dropForeign('acao_logs_empresa_id_foreign');
                });
    }
}
