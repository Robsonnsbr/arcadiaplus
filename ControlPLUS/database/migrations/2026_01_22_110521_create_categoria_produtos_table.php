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
        Schema::create('categoria_produtos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('categoria_produtos_empresa_id_foreign');
            $table->string('nome', 60);
            $table->string('nome_en', 60)->nullable();
            $table->string('nome_es', 60)->nullable();
            $table->boolean('status')->nullable()->default(true);
            $table->boolean('cardapio')->default(false);
            $table->boolean('delivery')->nullable()->default(false);
            $table->boolean('ecommerce')->nullable()->default(false);
            $table->boolean('reserva')->nullable()->default(false);
            $table->boolean('tipo_pizza')->nullable()->default(false);
            $table->string('hash_ecommerce', 50)->nullable();
            $table->string('hash_delivery', 50)->nullable();
            $table->unsignedBigInteger('categoria_id')->nullable()->index('categoria_produtos_categoria_id_foreign');
            $table->integer('_id_import')->nullable();
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
        Schema::dropIfExists('categoria_produtos');
    }
};
