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
        Schema::create('funcionario_servicos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('funcionario_servicos_empresa_id_foreign');
            $table->unsignedBigInteger('funcionario_id')->nullable()->index('funcionario_servicos_funcionario_id_foreign');
            $table->unsignedBigInteger('servico_id')->nullable()->index('funcionario_servicos_servico_id_foreign');
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
        Schema::dropIfExists('funcionario_servicos');
    }
};
