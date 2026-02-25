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
        Schema::create('informacao_bancaria_mdves', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mdfe_id')->index('informacao_bancaria_mdves_mdfe_id_foreign');
            $table->string('codigo_banco', 50);
            $table->string('codigo_agencia', 50);
            $table->string('cnpj_ipef', 20);
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
        Schema::dropIfExists('informacao_bancaria_mdves');
    }
};
