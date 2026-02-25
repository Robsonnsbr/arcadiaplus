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
        Schema::create('otica_os', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ordem_servico_id')->index('otica_os_ordem_servico_id_foreign');
            $table->integer('convenio_id')->nullable();
            $table->integer('medico_id')->nullable();
            $table->integer('tipo_armacao_id')->nullable();
            $table->integer('laboratorio_id')->nullable();
            $table->integer('formato_armacao_id')->nullable();
            $table->date('validade')->nullable();
            $table->string('arquivo_receita', 25)->nullable();
            $table->text('observacao_receita');
            $table->enum('tipo_lente', ['Pronta', 'SurfaÃ§ada'])->nullable();
            $table->enum('material_lente', ['Policarbonato', 'Resina', 'Trivex'])->nullable();
            $table->string('descricao_lente', 100)->nullable();
            $table->string('coloracao_lente', 100)->nullable();
            $table->boolean('armacao_propria');
            $table->boolean('armacao_segue');
            $table->string('armacao_aro', 20)->nullable();
            $table->string('armacao_ponte', 20)->nullable();
            $table->string('armacao_maior_diagonal', 20)->nullable();
            $table->string('armacao_altura_vertical', 20)->nullable();
            $table->string('armacao_distancia_pupilar', 20)->nullable();
            $table->string('armacao_altura_centro_longe_od', 20)->nullable();
            $table->string('armacao_altura_centro_longe_oe', 20)->nullable();
            $table->string('armacao_altura_centro_perto_od', 20)->nullable();
            $table->string('armacao_altura_centro_perto_oe', 20)->nullable();
            $table->text('tratamentos');
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
        Schema::dropIfExists('otica_os');
    }
};
