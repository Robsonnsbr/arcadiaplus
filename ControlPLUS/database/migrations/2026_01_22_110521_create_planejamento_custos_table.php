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
        Schema::create('planejamento_custos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('planejamento_custos_empresa_id_foreign');
            $table->unsignedBigInteger('cliente_id')->nullable()->index('planejamento_custos_cliente_id_foreign');
            $table->integer('numero_sequencial')->nullable();
            $table->text('descricao')->nullable();
            $table->text('observacao')->nullable();
            $table->date('data_prevista_entrega')->nullable();
            $table->date('data_entrega')->nullable();
            $table->string('arquivo', 25)->nullable();
            $table->integer('usuario_id')->nullable();
            $table->enum('estado', ['novo', 'cotacao', 'proposta', 'producao', 'finalizado', 'cancelado'])->default('novo');
            $table->integer('compra_id')->nullable();
            $table->integer('venda_id')->nullable();
            $table->integer('local_id')->nullable();
            $table->decimal('total_custo', 14);
            $table->decimal('total_final', 14);
            $table->decimal('desconto', 14)->nullable();
            $table->decimal('frete', 14)->nullable();
            $table->timestamps();
            $table->string('codigo_material', 60)->nullable();
            $table->string('equipamento', 200)->nullable();
            $table->string('desenho', 200)->nullable();
            $table->string('material', 200)->nullable();
            $table->decimal('quantidade', 10)->nullable();
            $table->string('unidade', 20)->nullable();
            $table->integer('projeto_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('planejamento_custos');
    }
};
