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
        Schema::create('produto_unicos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('nfe_id')->nullable()->index('produto_unicos_nfe_id_foreign');
            $table->unsignedBigInteger('nfce_id')->nullable()->index('produto_unicos_nfce_id_foreign');
            $table->unsignedBigInteger('produto_id')->index('produto_unicos_produto_id_foreign');
            $table->string('codigo', 60);
            $table->string('observacao', 250)->nullable();
            $table->enum('tipo', ['entrada', 'saida']);
            $table->boolean('em_estoque');
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
        Schema::dropIfExists('produto_unicos');
    }
};
