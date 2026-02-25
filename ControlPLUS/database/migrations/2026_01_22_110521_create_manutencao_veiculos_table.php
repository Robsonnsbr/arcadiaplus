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
        Schema::create('manutencao_veiculos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('manutencao_veiculos_empresa_id_foreign');
            $table->unsignedBigInteger('veiculo_id')->index('manutencao_veiculos_veiculo_id_foreign');
            $table->unsignedBigInteger('fornecedor_id')->index('manutencao_veiculos_fornecedor_id_foreign');
            $table->integer('numero_sequencial')->nullable();
            $table->string('observacao', 200)->nullable();
            $table->decimal('total', 12);
            $table->decimal('desconto', 10)->nullable();
            $table->decimal('acrescimo', 10)->nullable();
            $table->boolean('conta_pagar_id')->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->enum('estado', ['aguardando', 'em_manutencao', 'finalizado']);
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
        Schema::dropIfExists('manutencao_veiculos');
    }
};
