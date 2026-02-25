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
        Schema::create('movimentacao_produtos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('produto_id')->nullable()->index('movimentacao_produtos_produto_id_foreign');
            $table->decimal('quantidade', 14, 4);
            $table->enum('tipo', ['incremento', 'reducao']);
            $table->integer('codigo_transacao');
            $table->integer('user_id')->nullable();
            $table->enum('tipo_transacao', ['venda_nfe', 'venda_nfce', 'compra', 'alteracao_estoque']);
            $table->unsignedBigInteger('produto_variacao_id')->nullable()->index('movimentacao_produtos_produto_variacao_id_foreign');
            $table->decimal('estoque_atual', 14, 4)->nullable()->default(0);
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
        Schema::dropIfExists('movimentacao_produtos');
    }
};
