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
        Schema::create('inventarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('inventarios_empresa_id_foreign');
            $table->unsignedBigInteger('usuario_id')->nullable()->index('inventarios_usuario_id_foreign');
            $table->date('inicio');
            $table->date('fim');
            $table->boolean('status');
            $table->string('referencia', 30);
            $table->string('observacao')->nullable();
            $table->string('tipo', 15);
            $table->integer('numero_sequencial')->nullable();
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
        Schema::dropIfExists('inventarios');
    }
};
