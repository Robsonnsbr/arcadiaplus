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
        Schema::create('consumo_reservas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('reserva_id')->index('consumo_reservas_reserva_id_foreign');
            $table->unsignedBigInteger('produto_id')->nullable()->index('consumo_reservas_produto_id_foreign');
            $table->decimal('quantidade');
            $table->decimal('valor_unitario', 12);
            $table->decimal('sub_total', 12);
            $table->string('observacao', 200)->nullable();
            $table->timestamps();
            $table->boolean('frigobar')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consumo_reservas');
    }
};
