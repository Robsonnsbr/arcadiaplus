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
        Schema::create('gestao_custo_producao_produtos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('gestao_custo_id')->index('gestao_custo_producao_produtos_gestao_custo_id_foreign');
            $table->unsignedBigInteger('produto_id')->index('gestao_custo_producao_produtos_produto_id_foreign');
            $table->decimal('quantidade', 12, 4);
            $table->decimal('valor_unitario', 10);
            $table->decimal('sub_total', 10);
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
        Schema::dropIfExists('gestao_custo_producao_produtos');
    }
};
