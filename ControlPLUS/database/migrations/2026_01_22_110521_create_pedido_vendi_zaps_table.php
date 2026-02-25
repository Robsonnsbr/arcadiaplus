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
        Schema::create('pedido_vendi_zaps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('pedido_vendi_zaps_empresa_id_foreign');
            $table->unsignedBigInteger('cliente_id')->nullable()->index('pedido_vendi_zaps_cliente_id_foreign');
            $table->integer('numero_pedido');
            $table->string('data', 30);
            $table->string('nome', 60);
            $table->string('documento', 20)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('email', 20)->nullable();
            $table->string('cep', 9)->nullable();
            $table->string('rua', 100)->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('bairro', 50)->nullable();
            $table->string('cidade', 50)->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('complemento', 100)->nullable();
            $table->decimal('total', 12);
            $table->string('observacao')->nullable();
            $table->boolean('entrega');
            $table->decimal('taxa_entrega', 12)->nullable();
            $table->decimal('taxa_retirada', 12)->nullable();
            $table->string('_id', 30);
            $table->string('hash', 30);
            $table->string('codigo_link_rastreio')->nullable();
            $table->string('tipo_pagamento', 50)->nullable();
            $table->integer('nfe_id')->nullable();
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
        Schema::dropIfExists('pedido_vendi_zaps');
    }
};
