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
        Schema::create('evento_salarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome', 50);
            $table->enum('tipo', ['semanal', 'mensal', 'anual']);
            $table->enum('metodo', ['informado', 'fixo']);
            $table->enum('condicao', ['soma', 'diminui']);
            $table->enum('tipo_valor', ['fixo', 'percentual']);
            $table->boolean('ativo')->default(true);
            $table->unsignedBigInteger('empresa_id')->index('evento_salarios_empresa_id_foreign');
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
        Schema::dropIfExists('evento_salarios');
    }
};
