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
        Schema::create('ecommerce_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('ecommerce_configs_empresa_id_foreign');
            $table->string('nome', 50);
            $table->string('loja_id', 30);
            $table->string('logo', 30);
            $table->string('descricao_breve', 200)->nullable();
            $table->string('rua', 80);
            $table->string('numero', 10);
            $table->string('bairro', 30);
            $table->string('cep', 10);
            $table->unsignedBigInteger('cidade_id')->index('ecommerce_configs_cidade_id_foreign');
            $table->string('telefone', 15);
            $table->string('email', 60)->nullable();
            $table->string('link_facebook', 120)->nullable();
            $table->string('link_whatsapp', 120)->nullable();
            $table->string('link_instagram', 120)->nullable();
            $table->decimal('frete_gratis_valor', 10)->nullable();
            $table->string('mercadopago_public_key', 120);
            $table->string('mercadopago_access_token', 120);
            $table->boolean('habilitar_retirada')->default(false);
            $table->boolean('notificacao_novo_pedido')->default(true);
            $table->decimal('desconto_padrao_boleto', 4)->nullable();
            $table->decimal('desconto_padrao_pix', 4)->nullable();
            $table->decimal('desconto_padrao_cartao', 4)->nullable();
            $table->string('tipos_pagamento')->default('[]');
            $table->boolean('status')->default(true);
            $table->text('politica_privacidade');
            $table->text('termos_condicoes');
            $table->timestamps();
            $table->text('dados_deposito')->nullable();
            $table->string('cor_principal', 10)->nullable()->default('#000');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ecommerce_configs');
    }
};
