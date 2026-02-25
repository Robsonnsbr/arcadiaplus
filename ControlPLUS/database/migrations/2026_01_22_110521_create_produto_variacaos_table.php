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
        Schema::create('produto_variacaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('produto_id')->nullable()->index('produto_variacaos_produto_id_foreign');
            $table->string('descricao', 100);
            $table->decimal('valor', 12, 4);
            $table->string('codigo_barras', 20)->nullable();
            $table->string('referencia', 20)->nullable();
            $table->string('imagem', 25)->nullable();
            $table->timestamps();
            $table->integer('variacao_modelo_item_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produto_variacaos');
    }
};
