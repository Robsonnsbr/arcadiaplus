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
        Schema::create('bairro_delivery_masters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome', 60);
            $table->unsignedBigInteger('cidade_id')->nullable()->index('bairro_delivery_masters_cidade_id_foreign');
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
        Schema::dropIfExists('bairro_delivery_masters');
    }
};
