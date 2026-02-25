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
        Schema::create('produto_combos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('produto_id')->index('produto_combos_produto_id_foreign');
            $table->unsignedBigInteger('item_id')->index('produto_combos_item_id_foreign');
            $table->decimal('quantidade', 8, 3);
            $table->decimal('valor_compra', 12, 4);
            $table->decimal('sub_total', 12, 4);
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
        Schema::dropIfExists('produto_combos');
    }
};
