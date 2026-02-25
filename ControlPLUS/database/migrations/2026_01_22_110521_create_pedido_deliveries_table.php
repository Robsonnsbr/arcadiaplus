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
        Schema::create('pedido_deliveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('pedido_deliveries_empresa_id_foreign');
            $table->unsignedBigInteger('cliente_id')->index('pedido_deliveries_cliente_id_foreign');
            $table->unsignedBigInteger('motoboy_id')->nullable()->index('pedido_deliveries_motoboy_id_foreign');
            $table->decimal('comissao_motoboy', 10)->nullable();
            $table->decimal('valor_total', 10);
            $table->decimal('troco_para', 10)->nullable();
            $table->string('tipo_pagamento', 20);
            $table->string('observacao', 50)->nullable();
            $table->string('telefone', 15);
            $table->enum('estado', ['novo', 'aprovado', 'cancelado', 'finalizado']);
            $table->string('motivo_estado', 50)->nullable();
            $table->unsignedBigInteger('endereco_id')->nullable()->index('pedido_deliveries_endereco_id_foreign');
            $table->unsignedBigInteger('cupom_id')->nullable()->index('pedido_deliveries_cupom_id_foreign');
            $table->decimal('desconto', 10)->nullable();
            $table->decimal('valor_entrega', 10);
            $table->boolean('app');
            $table->text('qr_code_base64')->nullable();
            $table->text('qr_code')->nullable();
            $table->string('transacao_id', 50)->nullable();
            $table->string('status_pagamento', 100)->nullable();
            $table->boolean('pedido_lido')->default(false);
            $table->boolean('finalizado')->nullable()->default(false);
            $table->string('horario_cricao', 5)->nullable();
            $table->string('horario_leitura', 5)->nullable();
            $table->string('horario_entrega', 5)->nullable();
            $table->integer('nfce_id')->nullable();
            $table->timestamps();
            $table->integer('funcionario_id_agendamento')->nullable();
            $table->string('inicio_agendamento', 5)->nullable();
            $table->string('fim_agendamento', 5)->nullable();
            $table->date('data_agendamento')->nullable();
            $table->integer('numero_sequencial')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pedido_deliveries');
    }
};
