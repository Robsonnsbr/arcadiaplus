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
        Schema::create('produtos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('produtos_empresa_id_foreign');
            $table->unsignedBigInteger('categoria_id')->nullable()->index('produtos_categoria_id_foreign');
            $table->unsignedBigInteger('sub_categoria_id')->nullable()->index('produtos_sub_categoria_id_foreign');
            $table->unsignedBigInteger('padrao_id')->nullable()->index('produtos_padrao_id_foreign');
            $table->unsignedBigInteger('marca_id')->nullable()->index('produtos_marca_id_foreign');
            $table->integer('variacao_modelo_id')->nullable();
            $table->integer('sub_variacao_modelo_id')->nullable();
            $table->string('nome', 200)->nullable();
            $table->string('codigo_barras', 20)->nullable();
            $table->string('codigo_barras2', 20)->nullable();
            $table->string('codigo_barras3', 20)->nullable();
            $table->string('referencia', 20)->nullable();
            $table->string('ncm', 10);
            $table->string('unidade', 20);
            $table->string('imagem', 25)->nullable();
            $table->decimal('perc_icms', 10)->default(0);
            $table->decimal('perc_pis', 10)->default(0);
            $table->decimal('perc_cofins', 10)->default(0);
            $table->decimal('perc_ipi', 10)->default(0);
            $table->string('cest', 10)->nullable();
            $table->integer('origem')->default(0);
            $table->string('cst_csosn', 3)->nullable();
            $table->string('cst_pis', 3)->nullable();
            $table->string('cst_cofins', 3)->nullable();
            $table->string('cst_ipi', 3)->nullable();
            $table->decimal('perc_red_bc', 5)->nullable();
            $table->decimal('pST', 5)->nullable();
            $table->decimal('valor_unitario', 12, 4);
            $table->decimal('valor_prazo', 12, 4)->nullable();
            $table->decimal('valor_minimo_venda', 12, 4)->nullable()->default(0);
            $table->decimal('valor_compra', 12, 4);
            $table->decimal('percentual_lucro', 10)->nullable()->default(0);
            $table->string('cfop_estadual', 4);
            $table->string('cfop_outro_estado', 4);
            $table->string('cfop_entrada_estadual', 4)->nullable();
            $table->string('cfop_entrada_outro_estado', 4)->nullable();
            $table->string('codigo_beneficio_fiscal', 15)->nullable();
            $table->string('cEnq', 3)->nullable();
            $table->boolean('gerenciar_estoque')->default(false);
            $table->decimal('adRemICMSRet', 10, 4)->nullable()->default(0);
            $table->decimal('pBio', 10, 4)->nullable()->default(0);
            $table->boolean('tipo_servico')->nullable()->default(false);
            $table->integer('indImport')->nullable()->default(0);
            $table->string('cUFOrig', 2)->nullable();
            $table->decimal('pOrig', 5)->nullable()->default(0);
            $table->string('codigo_anp', 10)->nullable();
            $table->decimal('perc_glp', 5)->nullable()->default(0);
            $table->decimal('perc_gnn', 5)->nullable()->default(0);
            $table->decimal('perc_gni', 5)->nullable()->default(0);
            $table->decimal('valor_partida', 10)->nullable()->default(0);
            $table->string('unidade_tributavel', 4)->nullable()->default('');
            $table->decimal('quantidade_tributavel', 10)->nullable()->default(0);
            $table->boolean('status')->default(true);
            $table->boolean('cardapio')->default(false);
            $table->boolean('delivery')->nullable()->default(false);
            $table->boolean('reserva')->nullable()->default(false);
            $table->boolean('ecommerce')->nullable()->default(false);
            $table->string('nome_en', 80)->nullable();
            $table->string('nome_es', 80)->nullable();
            $table->string('descricao')->nullable();
            $table->string('descricao_en')->nullable();
            $table->string('descricao_es')->nullable();
            $table->decimal('valor_cardapio', 12, 4)->nullable();
            $table->decimal('valor_delivery', 12, 4)->nullable();
            $table->boolean('destaque_delivery')->nullable()->default(false);
            $table->boolean('oferta_delivery')->nullable()->default(false);
            $table->boolean('destaque_cardapio')->nullable()->default(false);
            $table->boolean('oferta_cardapio')->nullable()->default(false);
            $table->integer('tempo_preparo')->nullable();
            $table->boolean('tipo_carne')->default(false);
            $table->boolean('tipo_unico')->nullable()->default(false);
            $table->boolean('composto')->nullable()->default(false);
            $table->boolean('combo')->nullable()->default(false);
            $table->decimal('margem_combo', 5)->nullable()->default(0);
            $table->decimal('estoque_minimo', 5)->nullable()->default(0);
            $table->string('alerta_validade')->nullable()->default('');
            $table->integer('referencia_balanca')->nullable();
            $table->boolean('balanca_pdv')->nullable()->default(false);
            $table->boolean('exportar_balanca')->nullable()->default(false);
            $table->decimal('valor_ecommerce', 12, 4)->nullable();
            $table->boolean('destaque_ecommerce')->nullable()->default(false);
            $table->integer('percentual_desconto')->nullable();
            $table->string('descricao_ecommerce')->nullable();
            $table->text('texto_ecommerce')->nullable();
            $table->decimal('largura')->nullable();
            $table->decimal('comprimento')->nullable();
            $table->decimal('altura')->nullable();
            $table->decimal('peso', 12, 3)->nullable();
            $table->decimal('peso_bruto', 12, 3)->nullable();
            $table->string('hash_ecommerce', 50)->nullable();
            $table->string('hash_delivery', 50)->nullable();
            $table->text('texto_delivery')->nullable();
            $table->string('mercado_livre_id', 20)->nullable();
            $table->string('mercado_livre_link')->nullable();
            $table->decimal('mercado_livre_valor', 12, 4)->nullable();
            $table->string('mercado_livre_categoria', 20)->nullable();
            $table->string('condicao_mercado_livre', 20)->nullable();
            $table->integer('quantidade_mercado_livre')->nullable();
            $table->string('mercado_livre_tipo_publicacao', 20)->nullable();
            $table->string('mercado_livre_youtube', 100)->nullable();
            $table->text('mercado_livre_descricao')->nullable();
            $table->string('mercado_livre_status', 20);
            $table->string('mercado_livre_modelo', 100)->nullable();
            $table->string('woocommerce_id', 20)->nullable();
            $table->string('woocommerce_slug', 80)->nullable();
            $table->string('woocommerce_link')->nullable();
            $table->decimal('woocommerce_valor', 12, 4)->nullable();
            $table->string('woocommerce_type', 30)->nullable();
            $table->string('woocommerce_status', 30)->nullable();
            $table->text('woocommerce_descricao')->nullable();
            $table->string('woocommerce_stock_status', 30)->nullable();
            $table->text('categorias_woocommerce')->nullable();
            $table->string('nuvem_shop_id', 20)->nullable();
            $table->decimal('nuvem_shop_valor', 12, 4)->nullable();
            $table->text('texto_nuvem_shop')->nullable();
            $table->integer('modBCST')->nullable();
            $table->decimal('pMVAST', 5)->nullable();
            $table->decimal('pICMSST', 5)->nullable();
            $table->decimal('redBCST', 5)->nullable();
            $table->decimal('pICMSEfet', 5)->nullable();
            $table->decimal('pRedBCEfet', 5)->nullable();
            $table->decimal('valor_atacado', 22, 7)->nullable()->default(0);
            $table->integer('quantidade_atacado')->nullable();
            $table->string('referencia_xml', 50)->nullable();
            $table->boolean('tipo_dimensao')->nullable()->default(false);
            $table->boolean('tipo_producao')->nullable()->default(false);
            $table->decimal('espessura')->nullable();
            $table->integer('_id_import')->nullable();
            $table->string('observacao', 100)->nullable();
            $table->string('observacao2', 100)->nullable();
            $table->string('observacao3', 100)->nullable();
            $table->string('observacao4', 100)->nullable();
            $table->text('avaliacao_observacao')->nullable();
            $table->integer('numero_sequencial')->nullable();
            $table->string('ifood_id', 50)->nullable();
            $table->string('vendizap_id', 50)->nullable();
            $table->decimal('vendizap_valor', 12, 4)->nullable();
            $table->string('tipo_item_sped', 2)->nullable();
            $table->integer('prazo_garantia')->nullable();
            $table->string('cst_ibscbs', 3)->nullable();
            $table->string('cclass_trib', 10)->nullable();
            $table->decimal('perc_ibs_uf', 10)->nullable()->default(0);
            $table->decimal('perc_ibs_mun', 10)->nullable()->default(0);
            $table->decimal('perc_cbs', 10)->nullable()->default(0);
            $table->decimal('perc_dif', 10)->nullable()->default(0);
            $table->timestamps();
            $table->string('ponto_carne', 30)->nullable();
            $table->string('tipo_produto', 20)->default('novo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produtos');
    }
};
