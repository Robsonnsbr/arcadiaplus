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
        Schema::create('difals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('difals_empresa_id_foreign');
            $table->string('uf', 2);
            $table->string('cfop', 4);
            $table->decimal('pICMSUFDest', 6);
            $table->decimal('pICMSInter', 6);
            $table->decimal('pICMSInterPart', 6);
            $table->decimal('pFCPUFDest', 6);
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
        Schema::dropIfExists('difals');
    }
};
