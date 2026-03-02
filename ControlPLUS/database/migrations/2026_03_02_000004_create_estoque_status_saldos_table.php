<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('estoque_status_saldos')) {
            return;
        }

        Schema::create('estoque_status_saldos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('produto_id');
            $table->unsignedBigInteger('produto_variacao_id')->nullable();
            $table->unsignedBigInteger('local_id');
            $table->string('status_key', 40);
            $table->decimal('quantidade', 14, 4)->default(0);
            $table->timestamps();

            $table->unique(
                ['empresa_id', 'produto_id', 'produto_variacao_id', 'local_id', 'status_key'],
                'estoque_status_saldos_unique'
            );

            $table->index(['produto_id', 'local_id'], 'estoque_status_saldos_produto_local_idx');
            $table->index(['empresa_id', 'local_id', 'status_key'], 'estoque_status_saldos_empresa_local_status_idx');

            $table->foreign('empresa_id')->references('id')->on('empresas')->cascadeOnDelete();
            $table->foreign('produto_id')->references('id')->on('produtos')->cascadeOnDelete();
            $table->foreign('local_id')->references('id')->on('localizacaos')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('estoque_status_saldos');
    }
};
