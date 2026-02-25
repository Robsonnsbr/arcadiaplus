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
        Schema::create('item_ibpts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ibpt_id')->nullable()->index('item_ibpts_ibpt_id_foreign');
            $table->string('codigo', 8);
            $table->string('descricao', 80);
            $table->decimal('nacional_federal', 5);
            $table->decimal('importado_federal', 5);
            $table->decimal('estadual', 5);
            $table->decimal('municipal', 5);
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
        Schema::dropIfExists('item_ibpts');
    }
};
