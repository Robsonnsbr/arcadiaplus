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
        Schema::create('escritorio_contabils', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('escritorio_contabils_empresa_id_foreign');
            $table->unsignedBigInteger('cidade_id')->nullable()->index('escritorio_contabils_cidade_id_foreign');
            $table->string('razao_social', 100);
            $table->string('nome_fantasia', 80);
            $table->string('cnpj', 19)->nullable();
            $table->string('ie', 20)->nullable();
            $table->string('email', 80)->nullable();
            $table->string('rua', 80);
            $table->string('numero', 10);
            $table->string('bairro', 50);
            $table->string('telefone', 20);
            $table->string('cep', 10);
            $table->string('crc', 20)->nullable();
            $table->string('cpf', 15)->nullable();
            $table->boolean('envio_xml_automatico')->default(false);
            $table->timestamps();
            $table->string('token_sieg')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('escritorio_contabils');
    }
};
