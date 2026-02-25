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
        Schema::create('item_pizza_nfces', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_nfce_id')->index('item_pizza_nfces_item_nfce_id_foreign');
            $table->unsignedBigInteger('produto_id')->index('item_pizza_nfces_produto_id_foreign');
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
        Schema::dropIfExists('item_pizza_nfces');
    }
};
