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
        Schema::create('item_pedidos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pedido_id')->index('item_pedidos_pedido_id_foreign');
            $table->unsignedBigInteger('produto_id')->index('item_pedidos_produto_id_foreign');
            $table->unsignedBigInteger('funcionario_id')->nullable()->index('item_pedidos_funcionario_id_foreign');
            $table->string('observacao')->nullable();
            $table->enum('estado', ['novo', 'pendente', 'preparando', 'finalizado'])->default('novo');
            $table->decimal('quantidade', 8, 3);
            $table->decimal('valor_unitario', 10);
            $table->decimal('sub_total', 10);
            $table->integer('tempo_preparo')->nullable();
            $table->string('ponto_carne', 30)->nullable();
            $table->unsignedBigInteger('tamanho_id')->nullable()->index('item_pedidos_tamanho_id_foreign');
            $table->boolean('impresso')->nullable()->default(false);
            $table->string('nome_cardapio', 60)->nullable();
            $table->string('telefone_cardapio', 20)->nullable();
            $table->boolean('finalizado_pdv')->nullable()->default(false);
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
        Schema::dropIfExists('item_pedidos');
    }
};
