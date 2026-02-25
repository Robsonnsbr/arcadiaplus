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
        Schema::create('item_adicionals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_pedido_id')->index('item_adicionals_item_pedido_id_foreign');
            $table->unsignedBigInteger('adicional_id')->index('item_adicionals_adicional_id_foreign');
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
        Schema::dropIfExists('item_adicionals');
    }
};
