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
        Schema::create('crm_anotacaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('crm_anotacaos_empresa_id_foreign');
            $table->unsignedBigInteger('cliente_id')->nullable()->index('crm_anotacaos_cliente_id_foreign');
            $table->unsignedBigInteger('fornecedor_id')->nullable()->index('crm_anotacaos_fornecedor_id_foreign');
            $table->integer('funcionario_id')->nullable();
            $table->integer('registro_id')->nullable();
            $table->string('tipo_registro')->nullable();
            $table->enum('status', ['positivo', 'bom', 'negativo'])->nullable();
            $table->string('conclusao', 100)->nullable();
            $table->string('assunto');
            $table->boolean('alerta');
            $table->date('data_retorno')->nullable();
            $table->date('data_entrega')->nullable();
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
        Schema::dropIfExists('crm_anotacaos');
    }
};
