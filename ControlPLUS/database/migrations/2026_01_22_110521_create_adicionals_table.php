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
        Schema::create('adicionals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('adicionals_empresa_id_foreign');
            $table->string('nome', 60);
            $table->string('nome_en', 60)->nullable();
            $table->string('nome_es', 60)->nullable();
            $table->boolean('status')->default(true);
            $table->decimal('valor', 12, 4);
            $table->unsignedBigInteger('categoria_id')->nullable();
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
        Schema::dropIfExists('adicionals');
    }
};
