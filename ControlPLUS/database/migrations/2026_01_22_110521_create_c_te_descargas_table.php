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
        Schema::create('c_te_descargas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('info_id')->index('c_te_descargas_info_id_foreign');
            $table->string('chave', 44);
            $table->string('seg_cod_barras', 44);
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
        Schema::dropIfExists('c_te_descargas');
    }
};
