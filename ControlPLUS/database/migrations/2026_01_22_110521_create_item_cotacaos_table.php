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
        Schema::create('item_cotacaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cotacao_id')->nullable()->index('item_cotacaos_cotacao_id_foreign');
            $table->unsignedBigInteger('produto_id')->nullable()->index('item_cotacaos_produto_id_foreign');
            $table->decimal('valor_unitario', 12, 3)->nullable();
            $table->decimal('quantidade', 12, 3);
            $table->decimal('sub_total', 12, 3)->nullable();
            $table->string('observacao', 120)->nullable();
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
        Schema::dropIfExists('item_cotacaos');
    }
};
