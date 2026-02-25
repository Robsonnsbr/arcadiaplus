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
        Schema::create('padrao_frigobars', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('frigobar_id')->index('padrao_frigobars_frigobar_id_foreign');
            $table->unsignedBigInteger('produto_id')->index('padrao_frigobars_produto_id_foreign');
            $table->decimal('quantidade');
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
        Schema::dropIfExists('padrao_frigobars');
    }
};
