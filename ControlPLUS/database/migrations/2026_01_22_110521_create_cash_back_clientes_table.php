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
        Schema::create('cash_back_clientes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('cash_back_clientes_empresa_id_foreign');
            $table->unsignedBigInteger('cliente_id')->index('cash_back_clientes_cliente_id_foreign');
            $table->enum('tipo', ['venda', 'pdv']);
            $table->integer('venda_id');
            $table->decimal('valor_venda', 16, 7);
            $table->decimal('valor_credito', 16, 7);
            $table->decimal('valor_percentual', 5);
            $table->date('data_expiracao');
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('cash_back_clientes');
    }
};
