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
        Schema::create('variacao_modelos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descricao', 200);
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('empresa_id')->index('variacao_modelos_empresa_id_foreign');
            $table->timestamps();
            $table->string('vendizap_id', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variacao_modelos');
    }
};
