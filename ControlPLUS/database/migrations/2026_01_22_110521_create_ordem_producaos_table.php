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
        Schema::create('ordem_producaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('ordem_producaos_empresa_id_foreign');
            $table->unsignedBigInteger('funcionario_id')->nullable()->index('ordem_producaos_funcionario_id_foreign');
            $table->unsignedBigInteger('usuario_id')->index('ordem_producaos_usuario_id_foreign');
            $table->text('observacao');
            $table->enum('estado', ['novo', 'producao', 'expedicao', 'entregue']);
            $table->date('data_prevista_entrega')->nullable();
            $table->integer('codigo_sequencial')->nullable();
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
        Schema::dropIfExists('ordem_producaos');
    }
};
