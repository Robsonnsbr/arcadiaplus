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
        Schema::create('registro_tefs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('registro_tefs_empresa_id_foreign');
            $table->unsignedBigInteger('nfce_id')->nullable()->index('registro_tefs_nfce_id_foreign');
            $table->string('nome_rede', 20);
            $table->string('nsu', 20);
            $table->string('data_transacao', 20);
            $table->string('hora_transacao', 20);
            $table->string('valor_total', 20);
            $table->string('hash', 20);
            $table->enum('estado', ['aprovado', 'cancelado', 'pendente']);
            $table->timestamps();
            $table->integer('usuario_id')->nullable();
            $table->string('finalizacao', 30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registro_tefs');
    }
};
