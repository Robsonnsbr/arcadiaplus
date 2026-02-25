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
        Schema::create('natureza_operacaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('natureza_operacaos_empresa_id_foreign');
            $table->string('descricao', 100);
            $table->string('cst_csosn', 3)->nullable();
            $table->string('cst_pis', 3)->nullable();
            $table->string('cst_cofins', 3)->nullable();
            $table->string('cst_ipi', 3)->nullable();
            $table->string('cfop_estadual', 4)->nullable();
            $table->string('cfop_outro_estado', 4)->nullable();
            $table->string('cfop_entrada_estadual', 4)->nullable();
            $table->string('cfop_entrada_outro_estado', 4)->nullable();
            $table->decimal('perc_icms', 5)->nullable();
            $table->decimal('perc_pis', 5)->nullable();
            $table->decimal('perc_cofins', 5)->nullable();
            $table->decimal('perc_ipi', 5)->nullable();
            $table->boolean('padrao')->nullable()->default(false);
            $table->boolean('sobrescrever_cfop')->nullable()->default(false);
            $table->boolean('movimentar_estoque')->nullable()->default(true);
            $table->integer('_id_import')->nullable();
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
        Schema::dropIfExists('natureza_operacaos');
    }
};
