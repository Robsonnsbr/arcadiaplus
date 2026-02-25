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
        Schema::create('reservas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('reservas_empresa_id_foreign');
            $table->unsignedBigInteger('cliente_id')->index('reservas_cliente_id_foreign');
            $table->unsignedBigInteger('acomodacao_id')->index('reservas_acomodacao_id_foreign');
            $table->date('data_checkin');
            $table->date('data_checkout');
            $table->decimal('valor_estadia', 12);
            $table->decimal('valor_consumo_frigobar', 12)->nullable();
            $table->decimal('valor_consumo_adicional', 12)->nullable();
            $table->decimal('desconto', 12)->nullable();
            $table->decimal('valor_outros', 12)->nullable();
            $table->decimal('valor_total', 12)->nullable();
            $table->enum('estado', ['pendente', 'iniciado', 'finalizado', 'cancelado']);
            $table->text('observacao');
            $table->boolean('conferencia_frigobar')->default(false);
            $table->integer('total_hospedes')->nullable();
            $table->timestamps();
            $table->string('codigo_reseva', 25)->nullable();
            $table->string('link_externo')->nullable();
            $table->integer('numero_sequencial')->nullable();
            $table->timestamp('data_checkin_realizado')->nullable()->useCurrent();
            $table->text('motivo_cancelamento')->nullable();
            $table->integer('nfe_id')->nullable();
            $table->integer('nfse_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservas');
    }
};
