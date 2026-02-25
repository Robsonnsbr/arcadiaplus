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
        Schema::create('meta_resultados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('meta_resultados_empresa_id_foreign');
            $table->unsignedBigInteger('funcionario_id')->index('meta_resultados_funcionario_id_foreign');
            $table->decimal('valor', 12);
            $table->integer('local_id')->nullable();
            $table->string('tabela', 20);
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
        Schema::dropIfExists('meta_resultados');
    }
};
