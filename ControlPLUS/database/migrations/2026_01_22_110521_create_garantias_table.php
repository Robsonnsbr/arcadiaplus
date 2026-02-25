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
        Schema::create('garantias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('garantias_empresa_id_foreign');
            $table->unsignedBigInteger('usuario_id')->index('garantias_usuario_id_foreign');
            $table->unsignedBigInteger('produto_id')->nullable()->index('garantias_produto_id_foreign');
            $table->unsignedBigInteger('cliente_id')->index('garantias_cliente_id_foreign');
            $table->integer('nfe_id')->nullable();
            $table->integer('nfce_id')->nullable();
            $table->date('data_venda')->nullable();
            $table->date('data_solicitacao')->nullable();
            $table->integer('prazo_garantia')->default(0);
            $table->text('descricao_problema');
            $table->text('observacao');
            $table->decimal('valor_reparo', 10)->nullable();
            $table->enum('status', ['registrada', 'em anÃ¡lise', 'aprovada', 'recusada', 'concluÃ­da', 'expirada'])->default('registrada');
            $table->timestamps();
            $table->integer('servico_id')->nullable();
            $table->integer('ordem_servico_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('garantias');
    }
};
