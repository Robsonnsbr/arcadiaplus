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
        Schema::create('reserva_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('reserva_configs_empresa_id_foreign');
            $table->string('cpf_cnpj', 18);
            $table->string('razao_social', 80);
            $table->string('rua', 80);
            $table->string('numero', 10);
            $table->string('bairro', 30);
            $table->string('cep', 10);
            $table->string('complemento', 200)->nullable();
            $table->unsignedBigInteger('cidade_id')->index('reserva_configs_cidade_id_foreign');
            $table->string('telefone', 15);
            $table->string('horario_checkin', 5);
            $table->string('horario_checkout', 5);
            $table->string('email', 60)->nullable();
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
        Schema::dropIfExists('reserva_configs');
    }
};
