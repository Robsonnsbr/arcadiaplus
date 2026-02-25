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
        Schema::create('market_place_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('market_place_configs_empresa_id_foreign');
            $table->unsignedBigInteger('cidade_id')->nullable()->index('market_place_configs_cidade_id_foreign');
            $table->string('link_facebook')->nullable();
            $table->string('link_instagram')->nullable();
            $table->string('link_whatsapp')->nullable();
            $table->string('telefone', 20);
            $table->string('rua', 80);
            $table->string('numero', 15);
            $table->string('bairro', 30);
            $table->string('cep', 9);
            $table->string('tempo_medio_entrega', 10)->nullable();
            $table->decimal('valor_entrega', 10)->nullable();
            $table->string('nome', 50);
            $table->string('descricao', 200)->nullable();
            $table->string('latitude', 15)->nullable();
            $table->string('longitude', 15)->nullable();
            $table->integer('valor_entrega_gratis')->nullable();
            $table->boolean('usar_bairros');
            $table->boolean('status')->default(false);
            $table->boolean('notificacao_novo_pedido')->default(true);
            $table->string('mercadopago_public_key', 120)->nullable();
            $table->string('mercadopago_access_token', 120)->nullable();
            $table->enum('tipo_divisao_pizza', ['divide', 'valor_maior'])->default('divide');
            $table->string('logo', 25);
            $table->string('fav_icon', 25);
            $table->string('tipos_pagamento')->default('[]');
            $table->string('segmento', 100)->default('[]');
            $table->decimal('pedido_minimo', 10)->nullable();
            $table->decimal('avaliacao_media', 10);
            $table->string('api_token', 50)->nullable();
            $table->boolean('autenticacao_sms')->default(false);
            $table->boolean('confirmacao_pedido_cliente')->default(false);
            $table->timestamps();
            $table->string('tipo_entrega', 30)->nullable()->default('');
            $table->string('loja_id', 15)->nullable();
            $table->string('email', 80)->nullable();
            $table->string('cor_principal', 10)->nullable();
            $table->string('funcionamento_descricao', 100)->nullable();
            $table->string('token_whatsapp', 120)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('market_place_configs');
    }
};
