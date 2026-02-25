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
        Schema::create('veiculos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('veiculos_empresa_id_foreign');
            $table->string('placa', 8);
            $table->string('uf', 2);
            $table->string('cor', 10);
            $table->string('marca', 20);
            $table->string('modelo', 20);
            $table->string('rntrc', 12)->nullable();
            $table->string('taf', 15)->nullable();
            $table->string('renavam', 12)->nullable();
            $table->string('numero_registro_estadual', 30)->nullable();
            $table->string('tipo', 2);
            $table->string('tipo_carroceria', 2);
            $table->string('tipo_rodado', 2);
            $table->string('tara', 10);
            $table->string('capacidade', 10);
            $table->string('proprietario_documento', 20);
            $table->string('proprietario_nome', 40);
            $table->string('proprietario_ie', 13);
            $table->string('proprietario_uf', 2);
            $table->integer('proprietario_tp');
            $table->unsignedBigInteger('funcionario_id')->nullable();
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
        Schema::dropIfExists('veiculos');
    }
};
