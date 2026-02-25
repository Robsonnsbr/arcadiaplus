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
        Schema::create('variacao_modelo_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('variacao_modelo_id')->index('variacao_modelo_items_variacao_modelo_id_foreign');
            $table->string('nome', 100);
            $table->timestamps();
            $table->string('vendizap_id', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variacao_modelo_items');
    }
};
