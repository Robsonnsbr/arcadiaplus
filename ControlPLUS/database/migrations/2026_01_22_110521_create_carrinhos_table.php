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
        Schema::create('carrinhos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cliente_id')->nullable()->index('carrinhos_cliente_id_foreign');
            $table->unsignedBigInteger('empresa_id')->index('carrinhos_empresa_id_foreign');
            $table->unsignedBigInteger('endereco_id')->nullable()->index('carrinhos_endereco_id_foreign');
            $table->enum('estado', ['pendente', 'finalizado']);
            $table->decimal('valor_total', 10);
            $table->string('tipo_frete', 20)->nullable();
            $table->decimal('valor_frete', 10);
            $table->decimal('cep', 9);
            $table->string('session_cart', 30);
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
        Schema::dropIfExists('carrinhos');
    }
};
