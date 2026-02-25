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
        Schema::create('planos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome', 40);
            $table->text('descricao');
            $table->string('descricao_curta', 200)->nullable();
            $table->integer('maximo_nfes');
            $table->integer('maximo_nfces');
            $table->integer('maximo_ctes');
            $table->integer('maximo_mdfes');
            $table->integer('maximo_usuarios')->nullable();
            $table->integer('maximo_locais')->nullable();
            $table->string('imagem', 25);
            $table->boolean('visivel_clientes')->default(true);
            $table->boolean('visivel_contadores')->nullable()->default(false);
            $table->boolean('status')->default(true);
            $table->decimal('valor', 10);
            $table->decimal('valor_implantacao', 10)->nullable()->default(0);
            $table->integer('intervalo_dias');
            $table->integer('dias_teste')->nullable();
            $table->text('modulos')->nullable();
            $table->boolean('auto_cadastro')->nullable()->default(false);
            $table->boolean('fiscal')->nullable()->default(true);
            $table->integer('segmento_id')->nullable();
            $table->integer('contador_id')->nullable();
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
        Schema::dropIfExists('planos');
    }
};
