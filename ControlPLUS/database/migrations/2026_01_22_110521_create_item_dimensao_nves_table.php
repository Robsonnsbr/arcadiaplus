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
        Schema::create('item_dimensao_nves', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_nfe_id')->nullable()->index('item_dimensao_nves_item_nfe_id_foreign');
            $table->decimal('valor_unitario_m2', 12);
            $table->decimal('largura', 12);
            $table->decimal('altura', 12);
            $table->decimal('quantidade', 12);
            $table->decimal('m2_total', 12);
            $table->decimal('sub_total', 12);
            $table->decimal('espessura', 12);
            $table->string('observacao', 200);
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
        Schema::dropIfExists('item_dimensao_nves');
    }
};
