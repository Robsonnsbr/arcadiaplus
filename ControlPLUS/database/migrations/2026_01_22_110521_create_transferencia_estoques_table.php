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
        Schema::create('transferencia_estoques', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('transferencia_estoques_empresa_id_foreign');
            $table->unsignedBigInteger('local_saida_id')->nullable()->index('transferencia_estoques_local_saida_id_foreign');
            $table->unsignedBigInteger('local_entrada_id')->nullable()->index('transferencia_estoques_local_entrada_id_foreign');
            $table->unsignedBigInteger('usuario_id')->nullable()->index('transferencia_estoques_usuario_id_foreign');
            $table->string('observacao')->nullable();
            $table->string('codigo_transacao', 10);
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
        Schema::dropIfExists('transferencia_estoques');
    }
};
