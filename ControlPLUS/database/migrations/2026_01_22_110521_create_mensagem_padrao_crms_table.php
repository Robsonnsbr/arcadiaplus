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
        Schema::create('mensagem_padrao_crms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('mensagem_padrao_crms_empresa_id_foreign');
            $table->string('titulo', 100);
            $table->text('mensagem');
            $table->boolean('status');
            $table->string('tipo', 30);
            $table->boolean('enviar_whatsapp');
            $table->boolean('enviar_email');
            $table->time('horario_envio')->nullable();
            $table->integer('dias_apos_venda')->nullable();
            $table->integer('dias_ultima_venda')->nullable();
            $table->timestamps();
            $table->boolean('mensagem_para_agendamento')->nullable()->default(false);
            $table->integer('dias_apos_agendamento')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mensagem_padrao_crms');
    }
};
