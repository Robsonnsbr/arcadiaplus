<?php

namespace App\Models;

class Permission extends \Spatie\Permission\Models\Permission
{
    public static function defaultPermissions()
    {
        return [
            // USUARIOS
            array('name' => 'usuarios_view', 'description' => 'Visualiza usuários'),
            array('name' => 'usuarios_create', 'description' => 'Cria usuário'),
            array('name' => 'usuarios_edit', 'description' => 'Edita usuário'),
            array('name' => 'usuarios_delete', 'description' => 'Deleta usuário'),

            // PRODUTOS E ESTOQUE
            array('name' => 'produtos_view', 'description' => 'Visualiza produtos'),
            array('name' => 'produtos_create', 'description' => 'Cria produto'),
            array('name' => 'produtos_edit', 'description' => 'Edita produtos'),
            array('name' => 'produtos_delete', 'description' => 'Deleta produtos'),

            array('name' => 'estoque_view', 'description' => 'Visualiza estoque'),
            array('name' => 'estoque_create', 'description' => 'Cria estoque'),
            array('name' => 'estoque_edit', 'description' => 'Edita estoque'),
            array('name' => 'estoque_delete', 'description' => 'Deleta estoque'),

            array('name' => 'variacao_view', 'description' => 'Visualiza variação'),
            array('name' => 'variacao_create', 'description' => 'Cria variação'),
            array('name' => 'variacao_edit', 'description' => 'Edita variação'),
            array('name' => 'variacao_delete', 'description' => 'Deleta variação'),

            array('name' => 'categoria_produtos_view', 'description' => 'Visualiza categoria de produtos'),
            array('name' => 'categoria_produtos_create', 'description' => 'Cria categoria de produtos'),
            array('name' => 'categoria_produtos_edit', 'description' => 'Edita categoria de produtos'),
            array('name' => 'categoria_produtos_delete', 'description' => 'Deleta categoria de produtos'),

            array('name' => 'marcas_view', 'description' => 'Visualiza marca'),
            array('name' => 'marcas_create', 'description' => 'Cria marca'),
            array('name' => 'marcas_edit', 'description' => 'Edita marca'),
            array('name' => 'marcas_delete', 'description' => 'Deleta marca'),

            array('name' => 'lista_preco_view', 'description' => 'Visualiza lista de preços'),
            array('name' => 'lista_preco_create', 'description' => 'Cria lista de preços'),
            array('name' => 'lista_preco_edit', 'description' => 'Edita lista de preços'),
            array('name' => 'lista_preco_delete', 'description' => 'Deleta lista de preços'),

            // FISCAL PRODUTO
            array('name' => 'config_produto_fiscal_view', 'description' => 'Visualiza configuração fiscal produto'),
            array('name' => 'config_produto_fiscal_create', 'description' => 'Cria configuração fiscal produto'),
            array('name' => 'config_produto_fiscal_edit', 'description' => 'Edita configuração fiscal produto'),
            array('name' => 'config_produto_fiscal_delete', 'description' => 'Deleta configuração fiscal produto'),

            // CADASTROS GERAIS
            array('name' => 'clientes_view', 'description' => 'Visualiza clientes'),
            array('name' => 'clientes_create', 'description' => 'Cria cliente'),
            array('name' => 'clientes_edit', 'description' => 'Edita cliente'),
            array('name' => 'clientes_delete', 'description' => 'Deleta cliente'),

            array('name' => 'fornecedores_view', 'description' => 'Visualiza fornecedores'),
            array('name' => 'fornecedores_create', 'description' => 'Cria fornecedor'),
            array('name' => 'fornecedores_edit', 'description' => 'Edita fornecedor'),
            array('name' => 'fornecedores_delete', 'description' => 'Deleta fornecedor'),

            array('name' => 'transportadoras_view', 'description' => 'Visualiza transportadora'),
            array('name' => 'transportadoras_create', 'description' => 'Cria transportadora'),
            array('name' => 'transportadoras_edit', 'description' => 'Edita transportadora'),
            array('name' => 'transportadoras_delete', 'description' => 'Deleta transportadora'),

            array('name' => 'funcionario_view', 'description' => 'Visualiza funcionário'),
            array('name' => 'funcionario_create', 'description' => 'Cria funcionário'),
            array('name' => 'funcionario_edit', 'description' => 'Edita funcionário'),
            array('name' => 'funcionario_delete', 'description' => 'Deleta funcionário'),

            // DOCUMENTOS FISCAIS
            array('name' => 'nfe_view', 'description' => 'Visualiza NFe'),
            array('name' => 'nfe_create', 'description' => 'Cria NFe'),
            array('name' => 'nfe_edit', 'description' => 'Edita NFe'),
            array('name' => 'nfe_delete', 'description' => 'Deleta NFe'),
            array('name' => 'nfe_inutiliza', 'description' => 'Inutiliza NFe'),
            array('name' => 'nfe_transmitir', 'description' => 'Transmitir NFe'),
            array('name' => 'nfe_correcao', 'description' => 'Cria carta de correção NFe'),

            array('name' => 'nfce_view', 'description' => 'Visualiza NFCe'),
            array('name' => 'nfce_create', 'description' => 'Cria NFCe'),
            array('name' => 'nfce_edit', 'description' => 'Edita NFCe'),
            array('name' => 'nfce_delete', 'description' => 'Deleta NFCe'),
            array('name' => 'nfce_transmitir', 'description' => 'Transmitir NFCe'),
            array('name' => 'nfce_inutiliza', 'description' => 'Inutiliza NFce'),

            array('name' => 'cte_view', 'description' => 'Visualiza CTe'),
            array('name' => 'cte_os_view', 'description' => 'Visualiza CTeOs'),
            array('name' => 'mdfe_view', 'description' => 'Visualiza MDFe'),
            array('name' => 'nfse_view', 'description' => 'Visualiza NFSe'),

            // VENDAS E PDV
            array('name' => 'pdv_view', 'description' => 'Visualiza PDV'),
            array('name' => 'pdv_create', 'description' => 'Cria PDV'),
            array('name' => 'orcamento_view', 'description' => 'Visualiza Orçamento'),
            array('name' => 'orcamento_create', 'description' => 'Cria Orçamento'),
            array('name' => 'pre_venda_view', 'description' => 'Visualiza pré venda'),
            array('name' => 'tradein_view', 'description' => 'Visualiza Trade-in'),
            array('name' => 'troca_view', 'description' => 'Visualiza troca'),

            // FINANCEIRO
            array('name' => 'conta_pagar_view', 'description' => 'Visualiza conta a pagar'),
            array('name' => 'conta_pagar_create', 'description' => 'Cria conta a pagar'),
            array('name' => 'conta_receber_view', 'description' => 'Visualiza conta a receber'),
            array('name' => 'conta_receber_create', 'description' => 'Cria conta a receber'),
            array('name' => 'fluxo_caixa_view', 'description' => 'Visualiza fluxo de caixa'),
            array('name' => 'caixa_view', 'description' => 'Visualiza caixa (PDV)'),
            array('name' => 'contas_empresa_view', 'description' => 'Visualiza contas da empresa'),
            array('name' => 'plano_contas_view', 'description' => 'Visualiza plano de contas'),
            array('name' => 'categoria_conta_view', 'description' => 'Visualiza categoria de conta'),

            // BOLETOS
            array('name' => 'contas_boleto_view', 'description' => 'Visualiza contas de boleto'),
            array('name' => 'boleto_view', 'description' => 'Visualiza boleto'),
            array('name' => 'boleto_create', 'description' => 'Cria boleto'),

            // SERVIÇOS E ORDENS
            array('name' => 'servico_view', 'description' => 'Visualiza serviço'),
            array('name' => 'servico_create', 'description' => 'Cria serviço'),
            array('name' => 'ordem_servico_view', 'description' => 'Visualiza ordem de serviço'),
            array('name' => 'ordem_servico_create', 'description' => 'Cria ordem de serviço'),
            array('name' => 'agendamento_view', 'description' => 'Visualiza agendamento'),

            // PRODUÇÃO E CUSTOS
            array('name' => 'ordem_producao_view', 'description' => 'Visualiza ordem de produção'),
            array('name' => 'gestao_producao_view', 'description' => 'Visualiza gestão de custos'),
            array('name' => 'planejamento_custo_view', 'description' => 'Visualiza planejamento de custos'),

            // CONFIGURAÇÕES E SISTEMA
            array('name' => 'config_empresa_view', 'description' => 'Visualiza configuração da empresa'),
            array('name' => 'config_empresa_edit', 'description' => 'Edita configuração da empresa'),
            array('name' => 'config_certificado_view', 'description' => 'Visualiza certificado digital'),
            array('name' => 'controle_acesso_view', 'description' => 'Visualiza controle de acesso/permissões'),
            array('name' => 'emitente_view', 'description' => 'Visualiza emitente'),
            array('name' => 'natureza_operacao_view', 'description' => 'Visualiza natureza de operação'),
            array('name' => 'logs_view', 'description' => 'Visualiza logs de atividade'),
            array('name' => 'dashboard_view', 'description' => 'Visualiza painel de indicadores'),

            // INTEGRAÇÕES
            array('name' => 'ecommerce_view', 'description' => 'Visualiza ecommerce'),
            array('name' => 'woocommerce_view', 'description' => 'Visualiza woocommerce'),
            array('name' => 'ifood_view', 'description' => 'Visualiza ifood'),
            array('name' => 'mercado_livre_view', 'description' => 'Visualiza mercado livre'),
            array('name' => 'nuvem_shop_view', 'description' => 'Visualiza nuvem shop'),

            // RELATÓRIOS E OUTROS
            array('name' => 'relatorio_view', 'description' => 'Visualiza relatórios'),
            array('name' => 'arquivos_xml_view', 'description' => 'Visualiza arquivos xml'),
            array('name' => 'garantias_view', 'description' => 'Visualiza garantias'),
        ];
    }
}
