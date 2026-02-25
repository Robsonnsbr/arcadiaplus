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
        Schema::create('produto_os', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('produto_id')->nullable()->index('produto_os_produto_id_foreign');
            $table->unsignedBigInteger('ordem_servico_id')->nullable()->index('produto_os_ordem_servico_id_foreign');
            $table->integer('quantidade');
            $table->decimal('valor', 10)->nullable()->default(0);
            $table->decimal('subtotal', 10)->nullable()->default(0);
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
        Schema::dropIfExists('produto_os');
    }
};
