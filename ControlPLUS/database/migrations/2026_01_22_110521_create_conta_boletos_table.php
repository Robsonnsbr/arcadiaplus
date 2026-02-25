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
        Schema::create('conta_boletos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('conta_boletos_empresa_id_foreign');
            $table->string('banco', 30);
            $table->string('agencia', 10);
            $table->string('conta', 15);
            $table->string('titular', 45);
            $table->boolean('padrao')->default(false);
            $table->boolean('usar_logo')->default(false);
            $table->string('documento', 18);
            $table->string('rua', 60);
            $table->string('numero', 10);
            $table->string('cep', 9);
            $table->string('bairro', 30);
            $table->unsignedBigInteger('cidade_id')->nullable()->index('conta_boletos_cidade_id_foreign');
            $table->string('carteira', 10)->nullable();
            $table->string('convenio', 20)->nullable();
            $table->decimal('juros', 10)->nullable();
            $table->decimal('multa', 10)->nullable();
            $table->integer('juros_apos')->nullable();
            $table->enum('tipo', ['Cnab400', 'Cnab240']);
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
        Schema::dropIfExists('conta_boletos');
    }
};
