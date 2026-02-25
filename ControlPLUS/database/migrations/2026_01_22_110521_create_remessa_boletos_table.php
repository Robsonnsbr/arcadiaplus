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
        Schema::create('remessa_boletos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome_arquivo', 40);
            $table->integer('conta_boleto_id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('remessa_boletos_empresa_id_foreign');
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
        Schema::dropIfExists('remessa_boletos');
    }
};
