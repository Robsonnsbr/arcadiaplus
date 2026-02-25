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
        Schema::create('endereco_deliveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cidade_id')->index('endereco_deliveries_cidade_id_foreign');
            $table->unsignedBigInteger('cliente_id')->index('endereco_deliveries_cliente_id_foreign');
            $table->unsignedBigInteger('bairro_id')->index('endereco_deliveries_bairro_id_foreign');
            $table->string('rua', 50);
            $table->string('numero', 10);
            $table->string('referencia', 30)->nullable();
            $table->string('latitude', 10)->nullable();
            $table->string('longitude', 10)->nullable();
            $table->string('cep', 10);
            $table->enum('tipo', ['casa', 'trabalho']);
            $table->boolean('padrao');
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
        Schema::dropIfExists('endereco_deliveries');
    }
};
