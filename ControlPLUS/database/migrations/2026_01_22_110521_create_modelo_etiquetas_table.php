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
        Schema::create('modelo_etiquetas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('modelo_etiquetas_empresa_id_foreign');
            $table->string('nome', 40);
            $table->string('observacao')->nullable();
            $table->decimal('altura', 7);
            $table->decimal('largura', 7);
            $table->integer('etiquestas_por_linha');
            $table->decimal('distancia_etiquetas_lateral', 7);
            $table->decimal('distancia_etiquetas_topo', 7);
            $table->integer('quantidade_etiquetas');
            $table->decimal('tamanho_fonte', 7);
            $table->decimal('tamanho_codigo_barras', 7);
            $table->boolean('nome_empresa');
            $table->boolean('nome_produto');
            $table->boolean('valor_produto');
            $table->boolean('codigo_produto');
            $table->boolean('codigo_barras_numerico');
            $table->enum('tipo', ['simples', 'gondola']);
            $table->timestamps();
            $table->boolean('importado_super')->nullable()->default(false);
            $table->decimal('distancia_entre_linhas', 7)->nullable()->default(0);
            $table->boolean('referencia')->nullable()->default(false);
            $table->boolean('valor_atacado')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modelo_etiquetas');
    }
};
