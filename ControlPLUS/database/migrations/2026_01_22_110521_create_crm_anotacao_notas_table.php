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
        Schema::create('crm_anotacao_notas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('crm_anotacao_id')->index('crm_anotacao_notas_crm_anotacao_id_foreign');
            $table->text('nota');
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
        Schema::dropIfExists('crm_anotacao_notas');
    }
};
