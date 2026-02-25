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
        Schema::create('log_boletos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('tipo', ['confirmacao', 'geracao']);
            $table->unsignedBigInteger('empresa_id')->nullable()->index('log_boletos_empresa_id_foreign');
            $table->boolean('status');
            $table->string('descricao')->nullable();
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
        Schema::dropIfExists('log_boletos');
    }
};
