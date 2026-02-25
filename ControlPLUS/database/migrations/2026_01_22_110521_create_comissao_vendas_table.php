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
        Schema::create('comissao_vendas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('comissao_vendas_empresa_id_foreign');
            $table->unsignedBigInteger('funcionario_id')->nullable()->index('comissao_vendas_funcionario_id_foreign');
            $table->integer('nfe_id')->nullable();
            $table->integer('nfce_id')->nullable();
            $table->string('tabela', 14);
            $table->decimal('valor', 10);
            $table->decimal('valor_venda', 10)->nullable()->default(0);
            $table->boolean('status')->default(false);
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
        Schema::dropIfExists('comissao_vendas');
    }
};
