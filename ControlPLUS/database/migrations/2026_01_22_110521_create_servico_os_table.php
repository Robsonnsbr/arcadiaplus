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
        Schema::create('servico_os', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('servico_id')->nullable()->index('servico_os_servico_id_foreign');
            $table->unsignedBigInteger('ordem_servico_id')->nullable()->index('servico_os_ordem_servico_id_foreign');
            $table->integer('quantidade');
            $table->boolean('status')->default(false);
            $table->decimal('valor', 10)->nullable()->default(0);
            $table->decimal('subtotal', 10)->nullable()->default(0);
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
        Schema::dropIfExists('servico_os');
    }
};
