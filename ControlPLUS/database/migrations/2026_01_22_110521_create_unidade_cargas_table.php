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
        Schema::create('unidade_cargas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('info_id')->index('unidade_cargas_info_id_foreign');
            $table->string('id_unidade_carga', 20);
            $table->decimal('quantidade_rateio', 5);
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
        Schema::dropIfExists('unidade_cargas');
    }
};
