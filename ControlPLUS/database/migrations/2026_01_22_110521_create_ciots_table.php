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
        Schema::create('ciots', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mdfe_id')->index('ciots_mdfe_id_foreign');
            $table->string('cpf_cnpj', 18);
            $table->string('codigo', 20);
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
        Schema::dropIfExists('ciots');
    }
};
