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
        Schema::create('item_servico_nfces', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('nfce_id')->index('item_servico_nfces_nfce_id_foreign');
            $table->unsignedBigInteger('servico_id')->index('item_servico_nfces_servico_id_foreign');
            $table->decimal('quantidade', 6);
            $table->decimal('valor_unitario', 10);
            $table->decimal('sub_total', 10);
            $table->string('observacao', 50)->nullable();
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
        Schema::dropIfExists('item_servico_nfces');
    }
};
