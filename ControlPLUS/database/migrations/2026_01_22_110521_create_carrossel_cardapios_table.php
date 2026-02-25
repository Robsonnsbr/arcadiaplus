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
        Schema::create('carrossel_cardapios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('carrossel_cardapios_empresa_id_foreign');
            $table->unsignedBigInteger('produto_id')->nullable()->index('carrossel_cardapios_produto_id_foreign');
            $table->string('descricao')->nullable();
            $table->string('descricao_en')->nullable();
            $table->string('descricao_es')->nullable();
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
        Schema::dropIfExists('carrossel_cardapios');
    }
};
