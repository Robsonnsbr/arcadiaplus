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
        Schema::create('produto_seriais', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('produto_id')->nullable()->index('idx_produto_seriais_produto');
            $table->unsignedBigInteger('produto_variacao_id')->nullable();
            $table->string('numero_serie', 100)->index('idx_produto_seriais_numero');
            $table->string('deposito_oid', 50)->nullable();
            $table->string('status', 20)->default('disponivel');
            $table->string('origem', 30)->nullable();
            $table->string('migration_run_id', 50)->nullable();
            $table->string('source_file')->nullable();
            $table->integer('source_row')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable();

            $table->unique(['produto_id', 'numero_serie'], 'uq_produto_seriais_produto_numero');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produto_seriais');
    }
};
