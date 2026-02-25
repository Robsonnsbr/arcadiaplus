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
        Schema::create('financeiro_boletos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('financeiro_boletos_empresa_id_foreign');
            $table->decimal('valor', 10);
            $table->decimal('valor_recebido', 10);
            $table->decimal('juros', 10);
            $table->decimal('multa', 10);
            $table->date('vencimento');
            $table->date('data_recebimento')->nullable();
            $table->string('pdf_boleto')->nullable();
            $table->boolean('status');
            $table->integer('plano_id')->nullable();
            $table->date('data_liquidacao')->nullable();
            $table->string('_id', 30);
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
        Schema::dropIfExists('financeiro_boletos');
    }
};
