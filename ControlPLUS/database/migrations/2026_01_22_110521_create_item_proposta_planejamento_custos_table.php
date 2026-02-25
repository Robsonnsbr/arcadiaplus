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
        Schema::create('item_proposta_planejamento_custos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('planejamento_id')->index('item_proposta_planejamento_custos_planejamento_id_foreign');
            $table->string('descricao', 200);
            $table->decimal('quantidade', 12, 4);
            $table->decimal('valor_unitario_custo', 10);
            $table->decimal('valor_unitario_final', 10);
            $table->decimal('sub_total_custo', 10);
            $table->decimal('sub_total_final', 10);
            $table->string('tipo', 20);
            $table->string('observacao')->nullable();
            $table->integer('servico_id')->nullable();
            $table->integer('produto_id')->nullable();
            $table->boolean('terceiro')->default(false);
            $table->decimal('largura', 10)->nullable();
            $table->decimal('espessura', 10)->nullable();
            $table->decimal('comprimento', 10)->nullable();
            $table->decimal('peso_especifico', 10)->nullable();
            $table->decimal('calculo', 14, 4)->nullable();
            $table->timestamps();
            $table->decimal('peso_bruto', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_proposta_planejamento_custos');
    }
};
