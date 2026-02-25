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
        Schema::create('gestao_custo_producaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('numero_sequencial')->nullable();
            $table->unsignedBigInteger('empresa_id')->index('gestao_custo_producaos_empresa_id_foreign');
            $table->unsignedBigInteger('produto_id')->index('gestao_custo_producaos_produto_id_foreign');
            $table->unsignedBigInteger('cliente_id')->nullable()->index('gestao_custo_producaos_cliente_id_foreign');
            $table->date('data_finalizacao')->nullable();
            $table->boolean('status');
            $table->decimal('total_custo_produtos', 14);
            $table->decimal('total_custo_servicos', 14);
            $table->decimal('total_custo_outros', 14);
            $table->decimal('desconto', 14)->nullable();
            $table->decimal('total_final', 14);
            $table->decimal('frete', 14)->nullable();
            $table->decimal('quantidade', 12, 4);
            $table->integer('usuario_id')->nullable();
            $table->string('observacao')->nullable();
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
        Schema::dropIfExists('gestao_custo_producaos');
    }
};
