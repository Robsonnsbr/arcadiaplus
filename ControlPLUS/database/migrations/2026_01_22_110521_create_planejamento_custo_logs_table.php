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
        Schema::create('planejamento_custo_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('planejamento_id')->nullable()->index('planejamento_custo_logs_planejamento_id_foreign');
            $table->integer('usuario_id')->nullable();
            $table->string('estado_anterior', 20);
            $table->string('estado_alterado', 20);
            $table->string('observacao');
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
        Schema::dropIfExists('planejamento_custo_logs');
    }
};
