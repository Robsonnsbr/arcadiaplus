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
        Schema::create('acomodacaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('acomodacaos_empresa_id_foreign');
            $table->string('nome', 50);
            $table->string('numero', 15);
            $table->unsignedBigInteger('categoria_id')->index('acomodacaos_categoria_id_foreign');
            $table->decimal('valor_diaria', 12);
            $table->integer('capacidade');
            $table->string('estacionamento', 15)->nullable();
            $table->text('descricao');
            $table->boolean('status');
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
        Schema::dropIfExists('acomodacaos');
    }
};
