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
        Schema::create('interrupcoes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('funcionario_id')->nullable()->index('interrupcoes_funcionario_id_foreign');
            $table->time('inicio');
            $table->time('fim');
            $table->enum('dia_id', ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo']);
            $table->string('motivo', 50)->nullable();
            $table->unsignedBigInteger('empresa_id')->nullable()->index('interrupcoes_empresa_id_foreign');
            $table->boolean('status')->nullable()->default(true);
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
        Schema::dropIfExists('interrupcoes');
    }
};
