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
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('funcionario_id')->nullable()->index('agendamentos_funcionario_id_foreign');
            $table->unsignedBigInteger('cliente_id')->nullable()->index('agendamentos_cliente_id_foreign');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('agendamentos_empresa_id_foreign');
            $table->date('data');
            $table->string('observacao', 150)->nullable();
            $table->time('inicio');
            $table->time('termino');
            $table->decimal('total', 10);
            $table->decimal('desconto', 10)->nullable();
            $table->decimal('acrescimo', 10)->nullable();
            $table->decimal('valor_comissao', 10)->default(0);
            $table->boolean('status')->default(false);
            $table->enum('prioridade', ['baixa', 'media', 'alta'])->nullable()->default('baixa');
            $table->integer('nfce_id')->nullable();
            $table->integer('pedido_delivery_id')->nullable();
            $table->boolean('msg_wpp_manha_horario')->nullable()->default(false);
            $table->boolean('msg_wpp_alerta_horario')->nullable()->default(false);
            $table->integer('numero_sequencial')->nullable();
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
        Schema::dropIfExists('agendamentos');
    }
};
