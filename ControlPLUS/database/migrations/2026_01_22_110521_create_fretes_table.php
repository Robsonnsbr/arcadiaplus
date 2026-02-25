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
        Schema::create('fretes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('fretes_empresa_id_foreign');
            $table->unsignedBigInteger('veiculo_id')->index('fretes_veiculo_id_foreign');
            $table->unsignedBigInteger('cliente_id')->index('fretes_cliente_id_foreign');
            $table->enum('estado', ['em_carregamento', 'em_viagem', 'finalizado']);
            $table->string('observacao', 200)->nullable();
            $table->decimal('total', 12);
            $table->decimal('desconto', 10)->nullable();
            $table->decimal('acrescimo', 10)->nullable();
            $table->integer('local_id')->nullable();
            $table->unsignedBigInteger('cidade_id')->nullable()->index('fretes_cidade_id_foreign');
            $table->decimal('distancia_km', 10)->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->time('horario_inicio')->nullable();
            $table->time('horario_fim')->nullable();
            $table->timestamps();
            $table->integer('numero_sequencial')->nullable();
            $table->integer('cidade_carregamento')->nullable();
            $table->integer('cidade_descarregamento')->nullable();
            $table->decimal('total_despesa', 12)->nullable();
            $table->integer('conta_receber_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fretes');
    }
};
