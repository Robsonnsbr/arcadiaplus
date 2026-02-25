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
        Schema::create('tributacao_clientes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cliente_id')->index('tributacao_clientes_cliente_id_foreign');
            $table->decimal('perc_icms', 10)->nullable();
            $table->decimal('perc_pis', 10)->nullable();
            $table->decimal('perc_cofins', 10)->nullable();
            $table->decimal('perc_ipi', 10)->nullable();
            $table->string('cst_csosn', 3)->nullable();
            $table->string('cst_pis', 3)->nullable();
            $table->string('cst_cofins', 3)->nullable();
            $table->string('cst_ipi', 3)->nullable();
            $table->string('cfop_estadual', 4)->nullable();
            $table->string('cfop_outro_estado', 4)->nullable();
            $table->decimal('perc_red_bc', 5)->nullable();
            $table->string('cest', 10)->nullable();
            $table->string('ncm', 10)->nullable();
            $table->string('codigo_beneficio_fiscal', 15)->nullable();
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
        Schema::dropIfExists('tributacao_clientes');
    }
};
