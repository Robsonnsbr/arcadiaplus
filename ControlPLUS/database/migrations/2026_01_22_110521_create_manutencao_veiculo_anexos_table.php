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
        Schema::create('manutencao_veiculo_anexos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('manutencao_id')->index('manutencao_veiculo_anexos_manutencao_id_foreign');
            $table->string('arquivo', 25)->nullable();
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
        Schema::dropIfExists('manutencao_veiculo_anexos');
    }
};
