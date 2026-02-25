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
        Schema::create('nota_servico_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome', 100);
            $table->string('razao_social', 100);
            $table->string('documento', 18);
            $table->enum('regime', ['simples', 'normal']);
            $table->string('ie', 20)->nullable();
            $table->string('im', 20)->nullable();
            $table->string('cnae', 20)->nullable();
            $table->string('login_prefeitura', 50)->nullable();
            $table->string('senha_prefeitura', 50)->nullable();
            $table->string('telefone', 20);
            $table->string('email', 80);
            $table->string('rua', 80);
            $table->string('numero', 10);
            $table->string('bairro', 30);
            $table->string('complemento', 50)->nullable();
            $table->string('cep', 9);
            $table->string('token')->nullable();
            $table->string('logo', 30)->nullable();
            $table->unsignedBigInteger('empresa_id')->nullable()->index('nota_servico_configs_empresa_id_foreign');
            $table->unsignedBigInteger('cidade_id')->nullable()->index('nota_servico_configs_cidade_id_foreign');
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
        Schema::dropIfExists('nota_servico_configs');
    }
};
