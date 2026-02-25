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
        Schema::create('inutilizacaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('inutilizacaos_empresa_id_foreign');
            $table->integer('numero_inicial');
            $table->integer('numero_final');
            $table->string('numero_serie', 3);
            $table->enum('modelo', ['55', '65']);
            $table->string('justificativa', 200);
            $table->enum('estado', ['novo', 'aprovado', 'rejeitado']);
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
        Schema::dropIfExists('inutilizacaos');
    }
};
