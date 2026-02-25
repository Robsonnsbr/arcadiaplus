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
        Schema::create('mensagem_agendamento_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('mensagem');
            $table->unsignedBigInteger('empresa_id')->index('mensagem_agendamento_logs_empresa_id_foreign');
            $table->unsignedBigInteger('cliente_id')->index('mensagem_agendamento_logs_cliente_id_foreign');
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
        Schema::dropIfExists('mensagem_agendamento_logs');
    }
};
