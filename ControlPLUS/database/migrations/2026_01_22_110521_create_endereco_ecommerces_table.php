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
        Schema::create('endereco_ecommerces', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cidade_id')->index('endereco_ecommerces_cidade_id_foreign');
            $table->unsignedBigInteger('cliente_id')->index('endereco_ecommerces_cliente_id_foreign');
            $table->string('rua', 50);
            $table->string('bairro', 30);
            $table->string('numero', 10);
            $table->string('referencia', 50)->nullable();
            $table->string('cep', 10);
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
        Schema::dropIfExists('endereco_ecommerces');
    }
};
