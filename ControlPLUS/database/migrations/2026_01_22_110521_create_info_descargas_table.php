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
        Schema::create('info_descargas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mdfe_id')->index('info_descargas_mdfe_id_foreign');
            $table->unsignedBigInteger('cidade_id')->index('info_descargas_cidade_id_foreign');
            $table->integer('tp_unid_transp');
            $table->string('id_unid_transp', 20);
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
        Schema::dropIfExists('info_descargas');
    }
};
