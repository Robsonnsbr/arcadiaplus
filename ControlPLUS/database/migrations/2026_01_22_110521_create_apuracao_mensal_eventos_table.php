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
        Schema::create('apuracao_mensal_eventos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('apuracao_id')->nullable()->index('apuracao_mensal_eventos_apuracao_id_foreign');
            $table->unsignedBigInteger('evento_id')->nullable()->index('apuracao_mensal_eventos_evento_id_foreign');
            $table->decimal('valor');
            $table->enum('metodo', ['informado', 'fixo']);
            $table->enum('condicao', ['soma', 'diminui']);
            $table->string('nome', 100);
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
        Schema::dropIfExists('apuracao_mensal_eventos');
    }
};
