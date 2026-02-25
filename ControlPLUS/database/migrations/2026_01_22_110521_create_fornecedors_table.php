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
        Schema::create('fornecedors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('fornecedors_empresa_id_foreign');
            $table->string('razao_social', 60);
            $table->string('nome_fantasia', 60);
            $table->string('cpf_cnpj', 20);
            $table->string('ie', 20)->nullable();
            $table->integer('numero_sequencial')->nullable();
            $table->boolean('contribuinte')->default(false);
            $table->boolean('consumidor_final')->default(false);
            $table->string('email', 60)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->unsignedBigInteger('cidade_id')->nullable()->index('fornecedors_cidade_id_foreign');
            $table->string('rua', 60);
            $table->string('cep', 9);
            $table->string('numero', 10);
            $table->string('bairro', 40);
            $table->string('complemento', 60)->nullable();
            $table->integer('_id_import')->nullable();
            $table->string('codigo_pais', 4)->nullable();
            $table->string('id_estrangeiro', 30)->nullable();
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
        Schema::dropIfExists('fornecedors');
    }
};
