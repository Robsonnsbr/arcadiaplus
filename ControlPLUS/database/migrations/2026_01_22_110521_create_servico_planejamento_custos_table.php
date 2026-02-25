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
        Schema::create('servico_planejamento_custos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('planejamento_id')->nullable()->index('servico_planejamento_custos_planejamento_id_foreign');
            $table->unsignedBigInteger('servico_id')->nullable()->index('servico_planejamento_custos_servico_id_foreign');
            $table->decimal('quantidade', 12, 4);
            $table->decimal('valor_unitario', 10);
            $table->decimal('sub_total', 10);
            $table->boolean('status')->default(false);
            $table->boolean('terceiro')->default(false);
            $table->string('observacao')->nullable();
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
        Schema::dropIfExists('servico_planejamento_custos');
    }
};
