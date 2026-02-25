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
        Schema::create('padrao_tributacao_produtos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('padrao_tributacao_produtos_empresa_id_foreign');
            $table->string('descricao', 60);
            $table->decimal('perc_icms', 10)->default(0);
            $table->decimal('perc_pis', 10)->default(0);
            $table->decimal('perc_cofins', 10)->default(0);
            $table->decimal('perc_ipi', 10)->default(0);
            $table->string('cst_csosn', 3)->nullable();
            $table->string('cst_pis', 3)->nullable();
            $table->string('cst_cofins', 3)->nullable();
            $table->string('cst_ipi', 3)->nullable();
            $table->string('cfop_estadual', 4);
            $table->string('cfop_outro_estado', 4);
            $table->string('cfop_entrada_estadual', 4)->nullable();
            $table->string('cfop_entrada_outro_estado', 4)->nullable();
            $table->string('cEnq', 3)->nullable();
            $table->decimal('perc_red_bc', 5)->nullable();
            $table->decimal('pST', 5)->nullable();
            $table->string('cest', 10)->nullable();
            $table->string('ncm', 10)->nullable();
            $table->string('codigo_beneficio_fiscal', 10)->nullable();
            $table->boolean('padrao')->nullable()->default(false);
            $table->integer('modBCST')->nullable();
            $table->decimal('pMVAST', 5)->nullable();
            $table->decimal('pICMSST', 5)->nullable();
            $table->decimal('redBCST', 5)->nullable();
            $table->string('cst_ibscbs', 3)->nullable();
            $table->string('cclass_trib', 10)->nullable();
            $table->decimal('perc_ibs_uf', 10)->nullable();
            $table->decimal('perc_ibs_mun', 10)->nullable();
            $table->decimal('perc_cbs', 10)->nullable();
            $table->decimal('perc_dif', 10)->nullable();
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
        Schema::dropIfExists('padrao_tributacao_produtos');
    }
};
