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
        Schema::create('cotacaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('cotacaos_empresa_id_foreign');
            $table->unsignedBigInteger('fornecedor_id')->index('cotacaos_fornecedor_id_foreign');
            $table->string('responsavel', 50)->nullable();
            $table->string('hash_link', 30);
            $table->string('referencia', 15);
            $table->string('observacao_resposta', 200)->nullable();
            $table->string('observacao', 200)->nullable();
            $table->boolean('status')->default(true);
            $table->decimal('valor_total', 10)->nullable();
            $table->decimal('desconto', 10)->nullable();
            $table->enum('estado', ['nova', 'respondida', 'aprovada', 'rejeitada']);
            $table->boolean('escolhida')->default(false);
            $table->timestamp('data_resposta')->nullable();
            $table->integer('nfe_id')->nullable();
            $table->decimal('valor_frete', 10)->nullable();
            $table->string('observacao_frete', 200)->nullable();
            $table->date('previsao_entrega')->nullable();
            $table->timestamps();
            $table->integer('planejamento_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cotacaos');
    }
};
