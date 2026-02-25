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
        Schema::create('pedido_ecommerces', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cliente_id')->index('pedido_ecommerces_cliente_id_foreign');
            $table->unsignedBigInteger('empresa_id')->index('pedido_ecommerces_empresa_id_foreign');
            $table->unsignedBigInteger('endereco_id')->nullable()->index('pedido_ecommerces_endereco_id_foreign');
            $table->enum('estado', ['novo', 'preparando', 'em_trasporte', 'finalizado', 'recusado']);
            $table->enum('tipo_pagamento', ['cartao', 'pix', 'boleto', 'deposito'])->nullable();
            $table->decimal('valor_total', 10);
            $table->decimal('valor_frete', 10);
            $table->decimal('desconto', 10);
            $table->string('tipo_frete', 20)->nullable();
            $table->string('rua_entrega', 50)->nullable();
            $table->string('numero_entrega', 10)->nullable();
            $table->string('referencia_entrega', 50)->nullable();
            $table->string('bairro_entrega', 30)->nullable();
            $table->string('cep_entrega', 10)->nullable();
            $table->string('cidade_entrega', 60)->nullable();
            $table->text('link_boleto');
            $table->text('qr_code_base64');
            $table->text('qr_code');
            $table->string('observacao', 100)->nullable();
            $table->string('hash_pedido', 30);
            $table->string('status_pagamento', 15)->default('');
            $table->string('transacao_id', 100)->default('');
            $table->integer('nfe_id')->nullable();
            $table->string('cupom_desconto', 6)->nullable();
            $table->string('data_entrega', 10)->nullable();
            $table->string('codigo_rastreamento', 20)->nullable();
            $table->boolean('pedido_lido')->default(false);
            $table->string('nome', 40)->nullable();
            $table->string('sobre_nome', 40)->nullable();
            $table->string('email', 60)->nullable();
            $table->enum('tipo_documento', ['cpf', 'cnpj'])->nullable();
            $table->string('numero_documento', 20)->nullable();
            $table->timestamps();
            $table->string('comprovante', 25)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedido_ecommerces');
    }
};
