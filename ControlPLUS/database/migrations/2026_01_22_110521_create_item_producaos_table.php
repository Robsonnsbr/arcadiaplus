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
        Schema::create('item_producaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('produto_id')->index('item_producaos_produto_id_foreign');
            $table->decimal('quantidade', 12, 3);
            $table->boolean('status')->default(false);
            $table->integer('item_id')->default(0);
            $table->timestamps();
            $table->string('observacao', 100)->nullable();
            $table->string('dimensao', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_producaos');
    }
};
