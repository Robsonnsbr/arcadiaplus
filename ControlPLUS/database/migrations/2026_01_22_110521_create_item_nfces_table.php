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
        Schema::create('item_nfces', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('nfce_id')->nullable()->index('item_nfces_nfce_id_foreign');
            $table->unsignedBigInteger('produto_id')->nullable()->index('item_nfces_produto_id_foreign');
            $table->unsignedBigInteger('variacao_id')->nullable()->index('item_nfces_variacao_id_foreign');
            $table->decimal('quantidade', 12, 4)->nullable();
            $table->decimal('valor_unitario', 10);
            $table->decimal('sub_total', 10);
            $table->decimal('perc_icms', 5)->default(0);
            $table->decimal('perc_pis', 5)->default(0);
            $table->decimal('perc_cofins', 5)->default(0);
            $table->decimal('perc_ipi', 5)->default(0);
            $table->string('cest', 10)->nullable();
            $table->string('cst_csosn', 3)->default('102');
            $table->string('cst_pis', 3)->default('49');
            $table->string('cst_cofins', 3)->default('49');
            $table->string('cst_ipi', 3)->default('99');
            $table->decimal('perc_red_bc', 5)->default(0);
            $table->string('cfop', 4);
            $table->string('ncm', 10);
            $table->string('cEnq', 3)->nullable();
            $table->decimal('pST', 10)->nullable();
            $table->decimal('vBCSTRet', 10)->nullable();
            $table->integer('origem')->default(0);
            $table->string('codigo_beneficio_fiscal', 10)->nullable();
            $table->unsignedBigInteger('tamanho_id')->nullable();
            $table->string('observacao')->nullable();
            $table->string('infAdProd', 200)->nullable();
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
        Schema::dropIfExists('item_nfces');
    }
};
