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
        Schema::create('fila_envio_crons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('fila_envio_crons_empresa_id_foreign');
            $table->unsignedBigInteger('cliente_id')->nullable()->index('fila_envio_crons_cliente_id_foreign');
            $table->text('mensagem');
            $table->timestamp('enviado_em')->nullable();
            $table->date('agendar_para')->nullable();
            $table->enum('status', ['pendente', 'enviado', 'erro'])->default('pendente');
            $table->text('erro')->nullable();
            $table->boolean('enviar_whatsapp');
            $table->boolean('enviar_email');
            $table->string('whatsapp', 20)->nullable();
            $table->string('email', 60)->nullable();
            $table->string('tipo', 30);
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
        Schema::dropIfExists('fila_envio_crons');
    }
};
