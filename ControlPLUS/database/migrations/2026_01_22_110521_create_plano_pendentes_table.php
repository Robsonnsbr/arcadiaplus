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
        Schema::create('plano_pendentes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('plano_pendentes_empresa_id_foreign');
            $table->unsignedBigInteger('contador_id')->index('plano_pendentes_contador_id_foreign');
            $table->decimal('valor', 10);
            $table->unsignedBigInteger('plano_id')->index('plano_pendentes_plano_id_foreign');
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
        Schema::dropIfExists('plano_pendentes');
    }
};
