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
        Schema::create('notificacaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('notificacaos_empresa_id_foreign');
            $table->string('tabela', 60)->nullable();
            $table->text('descricao');
            $table->string('descricao_curta', 60);
            $table->string('titulo', 30);
            $table->integer('referencia')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('visualizada')->default(false);
            $table->boolean('por_sistema')->default(false);
            $table->enum('prioridade', ['baixa', 'media', 'alta']);
            $table->boolean('super')->default(false);
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
        Schema::dropIfExists('notificacaos');
    }
};
