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
        Schema::create('funcionarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('funcionarios_empresa_id_foreign');
            $table->string('nome', 60);
            $table->string('cpf_cnpj', 20)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->unsignedBigInteger('cidade_id')->nullable()->index('funcionarios_cidade_id_foreign');
            $table->string('rua', 60)->nullable();
            $table->string('cep', 9)->nullable();
            $table->string('numero', 10)->nullable();
            $table->string('bairro', 40)->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable()->index('funcionarios_usuario_id_foreign');
            $table->decimal('comissao', 10)->nullable()->default(0);
            $table->decimal('salario', 10)->nullable()->default(0);
            $table->string('codigo', 30)->nullable();
            $table->boolean('status')->nullable()->default(true);
            $table->boolean('permite_alterar_valor_app')->nullable()->default(true);
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
        Schema::dropIfExists('funcionarios');
    }
};
