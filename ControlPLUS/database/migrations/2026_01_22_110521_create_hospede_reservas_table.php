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
        Schema::create('hospede_reservas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('reserva_id')->index('hospede_reservas_reserva_id_foreign');
            $table->string('descricao', 20);
            $table->string('nome_completo', 100)->nullable();
            $table->string('cpf', 14)->nullable();
            $table->string('rua', 60)->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('bairro', 10)->nullable();
            $table->unsignedBigInteger('cidade_id')->nullable()->index('hospede_reservas_cidade_id_foreign');
            $table->string('telefone', 15)->nullable();
            $table->string('cep', 10)->nullable();
            $table->string('email', 60)->nullable();
            $table->boolean('status')->default(false);
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
        Schema::dropIfExists('hospede_reservas');
    }
};
