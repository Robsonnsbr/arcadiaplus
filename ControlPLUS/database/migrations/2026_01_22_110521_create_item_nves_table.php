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
        Schema::create('item_nves', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('nfe_id')->nullable()->index('item_nves_nfe_id_foreign');
            $table->unsignedBigInteger('produto_id')->nullable()->index('item_nves_produto_id_foreign');
            $table->unsignedBigInteger('variacao_id')->nullable()->index('item_nves_variacao_id_foreign');
            $table->decimal('quantidade', 12, 4)->nullable();
            $table->decimal('valor_unitario', 10);
            $table->decimal('sub_total', 10);
            $table->decimal('perc_icms', 10)->default(0);
            $table->decimal('perc_pis', 10)->default(0);
            $table->decimal('perc_cofins', 10)->default(0);
            $table->decimal('perc_ipi', 10)->default(0);
            $table->string('descricao', 200)->nullable();
            $table->string('cst_csosn', 3);
            $table->string('cst_pis', 3);
            $table->string('cst_cofins', 3);
            $table->string('cst_ipi', 3);
            $table->string('cest', 10)->nullable();
            $table->decimal('vbc_icms', 10)->nullable()->default(0);
            $table->decimal('vbc_pis', 10)->nullable()->default(0);
            $table->decimal('vbc_cofins', 10)->nullable()->default(0);
            $table->decimal('vbc_ipi', 10)->nullable()->default(0);
            $table->decimal('perc_red_bc', 10)->nullable();
            $table->string('cfop', 4);
            $table->string('ncm', 10);
            $table->string('cEnq', 3)->nullable();
            $table->decimal('pST', 10)->nullable();
            $table->decimal('vBCSTRet', 10)->nullable();
            $table->integer('origem')->default(0);
            $table->string('codigo_beneficio_fiscal', 10)->nullable();
            $table->string('lote', 30)->nullable();
            $table->date('data_vencimento')->nullable();
            $table->string('xPed', 30)->nullable();
            $table->string('nItemPed', 30)->nullable();
            $table->string('infAdProd', 200)->nullable();
            $table->decimal('pMVAST', 10, 4)->nullable();
            $table->decimal('vBCST', 10)->nullable();
            $table->decimal('pICMSST', 10)->nullable();
            $table->decimal('vICMSST', 10)->nullable();
            $table->decimal('vBCFCPST', 10)->nullable();
            $table->decimal('pFCPST', 10)->nullable();
            $table->decimal('vFCPST', 10)->nullable();
            $table->decimal('vICMSSubstituto', 10)->nullable();
            $table->integer('modBCST')->nullable();
            $table->string('nDI', 30)->nullable();
            $table->date('dDI')->nullable();
            $table->integer('cidade_desembarque_id')->nullable();
            $table->date('dDesemb')->nullable();
            $table->string('tpViaTransp', 2)->nullable();
            $table->decimal('vAFRMM', 12)->nullable();
            $table->string('tpIntermedio', 2)->nullable();
            $table->string('cpf_cnpj_di', 18)->nullable();
            $table->string('UFTerceiro', 2)->nullable();
            $table->string('cExportador', 30)->nullable();
            $table->string('nAdicao', 10)->nullable();
            $table->string('cFabricante', 20)->nullable();
            $table->decimal('vBCII', 10)->nullable();
            $table->decimal('vDespAdu', 10)->nullable();
            $table->decimal('vII', 10)->nullable();
            $table->decimal('vIOF', 10)->nullable();
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
        Schema::dropIfExists('item_nves');
    }
};
