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
        Schema::create('manutencao_veiculo_produtos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('manutencao_id')->index('manutencao_veiculo_produtos_manutencao_id_foreign');
            $table->unsignedBigInteger('produto_id')->nullable()->index('manutencao_veiculo_produtos_produto_id_foreign');
            $table->decimal('quantidade', 6);
            $table->decimal('valor_unitario', 10);
            $table->decimal('sub_total', 10);
            $table->string('observacao', 200)->nullable();
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
        Schema::dropIfExists('manutencao_veiculo_produtos');
    }
};
