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
        Schema::create('pdv_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('pdv_logs_empresa_id_foreign');
            $table->unsignedBigInteger('usuario_id')->index('pdv_logs_usuario_id_foreign');
            $table->unsignedBigInteger('produto_id')->nullable()->index('pdv_logs_produto_id_foreign');
            $table->string('acao', 30);
            $table->decimal('valor_desconto', 10)->nullable();
            $table->decimal('valor_acrescimo', 10)->nullable();
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
        Schema::dropIfExists('pdv_logs');
    }
};
