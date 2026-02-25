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
        Schema::create('contigencias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('contigencias_empresa_id_foreign');
            $table->boolean('status');
            $table->enum('tipo', ['SVCAN', 'SVCRS', 'OFFLINE']);
            $table->string('motivo');
            $table->text('status_retorno');
            $table->enum('documento', ['NFe', 'NFCe', 'CTe', 'MDFe']);
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
        Schema::dropIfExists('contigencias');
    }
};
