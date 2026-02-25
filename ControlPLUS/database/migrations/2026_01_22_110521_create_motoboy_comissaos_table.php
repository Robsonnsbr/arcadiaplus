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
        Schema::create('motoboy_comissaos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('motoboy_comissaos_empresa_id_foreign');
            $table->unsignedBigInteger('pedido_id')->index('motoboy_comissaos_pedido_id_foreign');
            $table->unsignedBigInteger('motoboy_id')->index('motoboy_comissaos_motoboy_id_foreign');
            $table->decimal('valor', 10);
            $table->decimal('valor_total_pedido', 10);
            $table->boolean('status')->default(false);
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
        Schema::dropIfExists('motoboy_comissaos');
    }
};
