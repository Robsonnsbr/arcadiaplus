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
        Schema::create('laboratorios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('laboratorios_empresa_id_foreign');
            $table->string('nome', 60);
            $table->string('cnpj', 14)->nullable();
            $table->string('email', 60)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->unsignedBigInteger('cidade_id')->nullable()->index('laboratorios_cidade_id_foreign');
            $table->string('rua', 60)->nullable();
            $table->string('cep', 9)->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('bairro', 40)->nullable();
            $table->boolean('status');
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
        Schema::dropIfExists('laboratorios');
    }
};
