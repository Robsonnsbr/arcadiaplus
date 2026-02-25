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
        Schema::create('remessa_boleto_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('remessa_id')->nullable()->index('remessa_boleto_items_remessa_id_foreign');
            $table->unsignedBigInteger('boleto_id')->nullable()->index('remessa_boleto_items_boleto_id_foreign');
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
        Schema::dropIfExists('remessa_boleto_items');
    }
};
