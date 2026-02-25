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
        Schema::create('item_ordem_producaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ordem_producao_id')->index('item_ordem_producaos_ordem_producao_id_foreign');
            $table->unsignedBigInteger('item_producao_id')->nullable()->index('item_ordem_producaos_item_producao_id_foreign');
            $table->unsignedBigInteger('produto_id')->index('item_ordem_producaos_produto_id_foreign');
            $table->decimal('quantidade', 12, 3);
            $table->boolean('status')->default(false);
            $table->string('observacao', 100)->nullable();
            $table->timestamps();
            $table->integer('cliente_id')->nullable();
            $table->string('numero_pedido', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_ordem_producaos');
    }
};
