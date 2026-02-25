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
        Schema::create('caixas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('caixas_empresa_id_foreign');
            $table->unsignedBigInteger('usuario_id')->nullable()->index('caixas_usuario_id_foreign');
            $table->decimal('valor_abertura', 10);
            $table->integer('conta_empresa_id')->nullable();
            $table->string('observacao', 200);
            $table->boolean('status')->default(false);
            $table->timestamp('data_fechamento')->nullable();
            $table->decimal('valor_fechamento', 10)->nullable()->default(0);
            $table->decimal('valor_dinheiro', 10)->nullable()->default(0);
            $table->decimal('valor_cheque', 10)->nullable()->default(0);
            $table->decimal('valor_outros', 10)->nullable()->default(0);
            $table->integer('local_id')->nullable();
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
        Schema::dropIfExists('caixas');
    }
};
