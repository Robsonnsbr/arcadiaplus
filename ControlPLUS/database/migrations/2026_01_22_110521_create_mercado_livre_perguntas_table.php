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
        Schema::create('mercado_livre_perguntas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('mercado_livre_perguntas_empresa_id_foreign');
            $table->string('_id', 20);
            $table->string('item_id', 20);
            $table->string('status', 20);
            $table->text('texto');
            $table->timestamp('data');
            $table->timestamps();
            $table->text('resposta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mercado_livre_perguntas');
    }
};
