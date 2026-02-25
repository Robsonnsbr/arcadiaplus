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
        Schema::create('item_pedido_vendi_zaps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pedido_id')->index('item_pedido_vendi_zaps_pedido_id_foreign');
            $table->unsignedBigInteger('produto_id')->nullable()->index('item_pedido_vendi_zaps_produto_id_foreign');
            $table->string('vendizap_produto_id', 30);
            $table->string('descricao');
            $table->text('detalhes');
            $table->string('unidade', 30);
            $table->string('observacao')->nullable();
            $table->string('codigo', 30)->nullable();
            $table->decimal('valor', 12);
            $table->decimal('valor_promociconal', 12)->nullable();
            $table->decimal('quantidade', 12);
            $table->decimal('sub_total', 12);
            $table->decimal('valor_adicionais', 12)->nullable();
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
        Schema::dropIfExists('item_pedido_vendi_zaps');
    }
};
