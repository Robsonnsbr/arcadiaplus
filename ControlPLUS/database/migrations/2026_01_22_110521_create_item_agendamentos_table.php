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
        Schema::create('item_agendamentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('servico_id')->nullable()->index('item_agendamentos_servico_id_foreign');
            $table->unsignedBigInteger('agendamento_id')->nullable()->index('item_agendamentos_agendamento_id_foreign');
            $table->integer('quantidade');
            $table->decimal('valor', 10)->default(0);
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
        Schema::dropIfExists('item_agendamentos');
    }
};
