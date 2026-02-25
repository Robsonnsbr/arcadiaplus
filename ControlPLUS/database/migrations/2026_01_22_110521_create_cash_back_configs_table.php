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
        Schema::create('cash_back_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('cash_back_configs_empresa_id_foreign');
            $table->decimal('valor_percentual', 5);
            $table->integer('dias_expiracao');
            $table->decimal('valor_minimo_venda', 10);
            $table->decimal('percentual_maximo_venda', 10);
            $table->string('mensagem_padrao_whatsapp');
            $table->timestamps();
            $table->text('mensagem_automatica_5_dias')->nullable();
            $table->text('mensagem_automatica_1_dia')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_back_configs');
    }
};
