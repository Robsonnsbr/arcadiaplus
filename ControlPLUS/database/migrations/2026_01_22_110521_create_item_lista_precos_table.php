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
        Schema::create('item_lista_precos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('lista_id')->nullable()->index('item_lista_precos_lista_id_foreign');
            $table->unsignedBigInteger('produto_id')->nullable()->index('item_lista_precos_produto_id_foreign');
            $table->decimal('valor', 10);
            $table->decimal('percentual_lucro', 10);
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
        Schema::dropIfExists('item_lista_precos');
    }
};
