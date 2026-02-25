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
        Schema::create('cupom_descontos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('cupom_descontos_empresa_id_foreign');
            $table->unsignedBigInteger('cliente_id')->nullable()->index('cupom_descontos_cliente_id_foreign');
            $table->enum('tipo_desconto', ['valor', 'percentual']);
            $table->string('codigo', 6);
            $table->string('descricao', 50)->nullable();
            $table->decimal('valor', 10, 4);
            $table->decimal('valor_minimo_pedido', 12, 4);
            $table->boolean('status')->default(true);
            $table->date('expiracao')->nullable();
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
        Schema::dropIfExists('cupom_descontos');
    }
};
