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
        Schema::create('item_inventario_impressaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('inventario_id')->nullable()->index('item_inventario_impressaos_inventario_id_foreign');
            $table->unsignedBigInteger('produto_id')->nullable()->index('item_inventario_impressaos_produto_id_foreign');
            $table->unsignedBigInteger('usuario_id')->nullable()->index('item_inventario_impressaos_usuario_id_foreign');
            $table->decimal('quantidade', 10)->nullable();
            $table->string('observacao', 100)->nullable();
            $table->string('estado', 15)->nullable();
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
        Schema::dropIfExists('item_inventario_impressaos');
    }
};
