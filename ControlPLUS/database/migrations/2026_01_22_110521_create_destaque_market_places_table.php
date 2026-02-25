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
        Schema::create('destaque_market_places', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('destaque_market_places_empresa_id_foreign');
            $table->unsignedBigInteger('produto_id')->nullable()->index('destaque_market_places_produto_id_foreign');
            $table->unsignedBigInteger('servico_id')->nullable()->index('destaque_market_places_servico_id_foreign');
            $table->string('descricao')->nullable();
            $table->decimal('valor', 12, 4)->nullable();
            $table->boolean('status')->default(true);
            $table->string('imagem', 25)->nullable();
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
        Schema::dropIfExists('destaque_market_places');
    }
};
