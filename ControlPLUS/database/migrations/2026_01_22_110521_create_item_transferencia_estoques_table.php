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
        Schema::create('item_transferencia_estoques', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('produto_id')->nullable()->index('item_transferencia_estoques_produto_id_foreign');
            $table->unsignedBigInteger('transferencia_id')->nullable()->index('item_transferencia_estoques_transferencia_id_foreign');
            $table->decimal('quantidade', 14, 4);
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
        Schema::dropIfExists('item_transferencia_estoques');
    }
};
