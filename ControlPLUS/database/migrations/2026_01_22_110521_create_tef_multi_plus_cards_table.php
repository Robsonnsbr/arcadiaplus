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
        Schema::create('tef_multi_plus_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('tef_multi_plus_cards_empresa_id_foreign');
            $table->unsignedBigInteger('usuario_id')->index('tef_multi_plus_cards_usuario_id_foreign');
            $table->string('cnpj', 20);
            $table->string('pdv', 20);
            $table->string('token', 60);
            $table->boolean('status');
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
        Schema::dropIfExists('tef_multi_plus_cards');
    }
};
