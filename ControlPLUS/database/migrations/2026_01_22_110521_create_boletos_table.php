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
        Schema::create('boletos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('boletos_empresa_id_foreign');
            $table->unsignedBigInteger('conta_boleto_id')->nullable()->index('boletos_conta_boleto_id_foreign');
            $table->unsignedBigInteger('conta_receber_id')->nullable()->index('boletos_conta_receber_id_foreign');
            $table->string('numero', 10);
            $table->string('numero_documento', 10);
            $table->string('carteira', 10);
            $table->string('convenio', 20);
            $table->date('vencimento');
            $table->decimal('valor', 12);
            $table->string('instrucoes')->nullable();
            $table->string('linha_digitavel', 50)->nullable();
            $table->string('nome_arquivo', 35)->nullable();
            $table->decimal('juros', 10)->nullable();
            $table->decimal('multa', 10)->nullable();
            $table->integer('juros_apos')->nullable();
            $table->enum('tipo', ['Cnab400', 'Cnab240']);
            $table->boolean('usar_logo')->default(false);
            $table->string('posto', 10)->nullable();
            $table->string('codigo_cliente', 10)->nullable();
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
        Schema::dropIfExists('boletos');
    }
};
