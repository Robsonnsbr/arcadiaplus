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
        Schema::create('item_pedido_deliveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('produto_id')->nullable()->index('item_pedido_deliveries_produto_id_foreign');
            $table->unsignedBigInteger('pedido_id')->index('item_pedido_deliveries_pedido_id_foreign');
            $table->unsignedBigInteger('tamanho_id')->nullable()->index('item_pedido_deliveries_tamanho_id_foreign');
            $table->boolean('status');
            $table->enum('estado', ['novo', 'pendente', 'preparando', 'finalizado'])->default('novo');
            $table->decimal('quantidade');
            $table->decimal('valor_unitario', 12);
            $table->decimal('sub_total', 12);
            $table->string('observacao', 50)->nullable();
            $table->timestamps();
            $table->integer('servico_id')->nullable();
            $table->boolean('impresso')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_pedido_deliveries');
    }
};
