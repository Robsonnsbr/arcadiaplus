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
        Schema::create('retirada_estoques', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('motivo', 100);
            $table->string('observacao')->nullable();
            $table->unsignedBigInteger('produto_id')->nullable()->index('retirada_estoques_produto_id_foreign');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('retirada_estoques_empresa_id_foreign');
            $table->decimal('quantidade', 10);
            $table->integer('local_id')->nullable();
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
        Schema::dropIfExists('retirada_estoques');
    }
};
