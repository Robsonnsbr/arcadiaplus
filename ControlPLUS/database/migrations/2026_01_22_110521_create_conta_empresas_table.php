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
        Schema::create('conta_empresas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('conta_empresas_empresa_id_foreign');
            $table->string('nome', 50);
            $table->string('banco', 50)->nullable();
            $table->string('agencia', 10)->nullable();
            $table->string('conta', 10)->nullable();
            $table->integer('plano_conta_id')->nullable();
            $table->decimal('saldo', 16)->nullable();
            $table->decimal('saldo_inicial', 16)->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
            $table->integer('local_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conta_empresas');
    }
};
