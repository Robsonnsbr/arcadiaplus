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
        Schema::create('despesa_fretes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('frete_id')->nullable()->index('despesa_fretes_frete_id_foreign');
            $table->unsignedBigInteger('tipo_despesa_id')->nullable()->index('despesa_fretes_tipo_despesa_id_foreign');
            $table->unsignedBigInteger('fornecedor_id')->nullable()->index('despesa_fretes_fornecedor_id_foreign');
            $table->decimal('valor', 10);
            $table->string('observacao', 200)->nullable();
            $table->timestamps();
            $table->integer('conta_pagar_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('despesa_fretes');
    }
};
