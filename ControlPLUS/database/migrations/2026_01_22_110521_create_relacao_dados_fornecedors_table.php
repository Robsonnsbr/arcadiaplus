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
        Schema::create('relacao_dados_fornecedors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('relacao_dados_fornecedors_empresa_id_foreign');
            $table->string('cst_csosn_entrada', 3)->nullable();
            $table->string('cfop_entrada', 4)->nullable();
            $table->string('cst_csosn_saida', 3)->nullable();
            $table->string('cfop_saida', 4)->nullable();
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
        Schema::dropIfExists('relacao_dados_fornecedors');
    }
};
