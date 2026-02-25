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
        Schema::create('cte_os', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('cte_os_empresa_id_foreign');
            $table->unsignedBigInteger('emitente_id')->nullable()->index('cte_os_emitente_id_foreign');
            $table->unsignedBigInteger('tomador_id')->nullable()->index('cte_os_tomador_id_foreign');
            $table->unsignedBigInteger('municipio_envio')->nullable()->index('cte_os_municipio_envio_foreign');
            $table->unsignedBigInteger('municipio_inicio')->nullable()->index('cte_os_municipio_inicio_foreign');
            $table->unsignedBigInteger('municipio_fim')->nullable()->index('cte_os_municipio_fim_foreign');
            $table->unsignedBigInteger('veiculo_id')->nullable()->index('cte_os_veiculo_id_foreign');
            $table->unsignedBigInteger('usuario_id')->nullable()->index('cte_os_usuario_id_foreign');
            $table->string('modal', 2);
            $table->string('cst', 3)->default('00');
            $table->decimal('perc_icms', 5)->default(0);
            $table->decimal('valor_transporte', 10);
            $table->decimal('valor_receber', 10);
            $table->string('descricao_servico', 100)->default('');
            $table->decimal('quantidade_carga', 12, 4);
            $table->unsignedBigInteger('natureza_id')->nullable()->index('cte_os_natureza_id_foreign');
            $table->integer('tomador');
            $table->integer('sequencia_cce');
            $table->string('observacao', 200);
            $table->integer('numero_emissao')->default(0);
            $table->string('chave', 48);
            $table->enum('estado_emissao', ['novo', 'aprovado', 'cancelado', 'rejeitado']);
            $table->timestamp('data_emissao')->nullable();
            $table->string('data_viagem', 10)->nullable()->default('');
            $table->string('horario_viagem', 5)->nullable()->default('');
            $table->string('cfop', 4)->nullable();
            $table->string('recibo', 30)->nullable();
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
        Schema::dropIfExists('cte_os');
    }
};
