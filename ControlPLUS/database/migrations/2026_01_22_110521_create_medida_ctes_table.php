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
        Schema::create('medida_ctes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cte_id')->index('medida_ctes_cte_id_foreign');
            $table->string('cod_unidade', 2);
            $table->string('tipo_medida', 20);
            $table->decimal('quantidade', 10, 4);
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
        Schema::dropIfExists('medida_ctes');
    }
};
