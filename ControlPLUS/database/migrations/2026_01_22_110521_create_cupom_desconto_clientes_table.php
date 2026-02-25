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
        Schema::create('cupom_desconto_clientes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cliente_id')->index('cupom_desconto_clientes_cliente_id_foreign');
            $table->unsignedBigInteger('empresa_id')->index('cupom_desconto_clientes_empresa_id_foreign');
            $table->unsignedBigInteger('cupom_id')->index('cupom_desconto_clientes_cupom_id_foreign');
            $table->unsignedBigInteger('pedido_id')->index('cupom_desconto_clientes_pedido_id_foreign');
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
        Schema::dropIfExists('cupom_desconto_clientes');
    }
};
