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
        Schema::create('item_pre_vendas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pre_venda_id')->index('item_pre_vendas_pre_venda_id_foreign');
            $table->unsignedBigInteger('produto_id')->index('item_pre_vendas_produto_id_foreign');
            $table->unsignedBigInteger('variacao_id')->nullable()->index('item_pre_vendas_variacao_id_foreign');
            $table->decimal('quantidade', 10, 3);
            $table->decimal('valor', 16, 7);
            $table->string('observacao', 80);
            $table->integer('cfop')->default(0);
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
        Schema::dropIfExists('item_pre_vendas');
    }
};
