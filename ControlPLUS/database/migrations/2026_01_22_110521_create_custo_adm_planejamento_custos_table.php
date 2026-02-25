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
        Schema::create('custo_adm_planejamento_custos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('planejamento_id')->nullable()->index('custo_adm_planejamento_custos_planejamento_id_foreign');
            $table->string('descricao', 100);
            $table->decimal('quantidade', 12, 4);
            $table->decimal('valor_unitario', 10);
            $table->decimal('sub_total', 10);
            $table->string('observacao')->nullable();
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
        Schema::dropIfExists('custo_adm_planejamento_custos');
    }
};
