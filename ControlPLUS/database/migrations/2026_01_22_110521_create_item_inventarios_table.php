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
        Schema::create('item_inventarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('inventario_id')->nullable()->index('item_inventarios_inventario_id_foreign');
            $table->unsignedBigInteger('produto_id')->nullable()->index('item_inventarios_produto_id_foreign');
            $table->unsignedBigInteger('usuario_id')->nullable()->index('item_inventarios_usuario_id_foreign');
            $table->decimal('quantidade', 10);
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
        Schema::dropIfExists('item_inventarios');
    }
};
