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
        Schema::create('produto_planejamento_custos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('planejamento_id')->nullable()->index('produto_planejamento_custos_planejamento_id_foreign');
            $table->unsignedBigInteger('produto_id')->nullable()->index('produto_planejamento_custos_produto_id_foreign');
            $table->decimal('quantidade', 12, 4);
            $table->decimal('valor_unitario', 10);
            $table->decimal('sub_total', 10);
            $table->boolean('status')->default(false);
            $table->string('observacao')->nullable();
            $table->decimal('largura', 10)->nullable();
            $table->decimal('espessura', 10)->nullable();
            $table->decimal('comprimento', 10)->nullable();
            $table->timestamps();
            $table->decimal('peso_especifico', 10)->nullable();
            $table->decimal('calculo', 14, 4)->nullable();
            $table->decimal('peso_bruto', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produto_planejamento_custos');
    }
};
