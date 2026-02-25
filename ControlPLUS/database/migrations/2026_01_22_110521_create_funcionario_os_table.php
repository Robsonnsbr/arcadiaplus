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
        Schema::create('funcionario_os', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('usuario_id')->nullable()->index('funcionario_os_usuario_id_foreign');
            $table->unsignedBigInteger('ordem_servico_id')->nullable()->index('funcionario_os_ordem_servico_id_foreign');
            $table->unsignedBigInteger('funcionario_id')->nullable()->index('funcionario_os_funcionario_id_foreign');
            $table->string('funcao');
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
        Schema::dropIfExists('funcionario_os');
    }
};
