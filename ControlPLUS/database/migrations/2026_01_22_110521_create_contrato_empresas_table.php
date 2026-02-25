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
        Schema::create('contrato_empresas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('contrato_empresas_empresa_id_foreign');
            $table->boolean('assinado')->default(false);
            $table->timestamp('data_assinatura')->nullable();
            $table->string('cpf_cnpj', 18);
            $table->timestamps();
            $table->text('texto')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contrato_empresas');
    }
};
