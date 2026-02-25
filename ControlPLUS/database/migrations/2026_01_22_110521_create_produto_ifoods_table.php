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
        Schema::create('produto_ifoods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('produto_ifoods_empresa_id_foreign');
            $table->unsignedBigInteger('produto_id')->index('produto_ifoods_produto_id_foreign');
            $table->string('ifood_id', 50)->nullable();
            $table->string('ifood_id_aux', 50)->nullable();
            $table->string('categoria_produto_ifood_id', 50)->nullable();
            $table->text('descricao')->nullable();
            $table->string('imagem', 200)->nullable();
            $table->string('serving', 20)->nullable();
            $table->string('nome', 150);
            $table->string('status', 20)->nullable();
            $table->decimal('estoque', 10)->nullable();
            $table->decimal('valor', 10)->nullable();
            $table->integer('sellingOption_minimum')->nullable();
            $table->integer('sellingOption_incremental')->nullable();
            $table->integer('sellingOption_averageUnit')->nullable();
            $table->string('sellingOption_availableUnits', 100)->nullable();
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
        Schema::dropIfExists('produto_ifoods');
    }
};
