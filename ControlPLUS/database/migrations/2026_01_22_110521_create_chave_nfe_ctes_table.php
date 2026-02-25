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
        Schema::create('chave_nfe_ctes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cte_id')->index('chave_nfe_ctes_cte_id_foreign');
            $table->string('chave', 44);
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
        Schema::dropIfExists('chave_nfe_ctes');
    }
};
