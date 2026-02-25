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
        Schema::create('etiqueta_configuracaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('etiqueta_configuracaos_empresa_id_foreign');
            $table->decimal('margem_topo', 7);
            $table->decimal('margem_lateral', 7);
            $table->decimal('distancia_entre_etiquetas', 7);
            $table->decimal('distancia_entre_linhas', 7);
            $table->decimal('largura_imagem', 7);
            $table->decimal('altura_imagem', 7);
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
        Schema::dropIfExists('etiqueta_configuracaos');
    }
};
