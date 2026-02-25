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
        Schema::create('servico_reservas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('reserva_id')->index('servico_reservas_reserva_id_foreign');
            $table->unsignedBigInteger('servico_id')->nullable()->index('servico_reservas_servico_id_foreign');
            $table->decimal('quantidade');
            $table->decimal('valor_unitario', 12);
            $table->decimal('sub_total', 12);
            $table->string('observacao', 200)->nullable();
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
        Schema::dropIfExists('servico_reservas');
    }
};
