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
        Schema::create('tamanho_pizzas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('tamanho_pizzas_empresa_id_foreign');
            $table->string('nome', 50);
            $table->string('nome_en', 50)->nullable();
            $table->string('nome_es', 50)->nullable();
            $table->integer('maximo_sabores');
            $table->integer('quantidade_pedacos');
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('tamanho_pizzas');
    }
};
