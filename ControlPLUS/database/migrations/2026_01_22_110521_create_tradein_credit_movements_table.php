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
        Schema::create('tradein_credit_movements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id');
            $table->string('documento', 20);
            $table->unsignedBigInteger('cliente_id')->nullable()->index('tradein_credit_movements_cliente_id_foreign');
            $table->unsignedBigInteger('fornecedor_id')->nullable()->index('tradein_credit_movements_fornecedor_id_foreign');
            $table->enum('tipo', ['credit', 'debit']);
            $table->decimal('valor', 12);
            $table->string('origem_tipo', 60)->nullable();
            $table->unsignedBigInteger('origem_id')->nullable();
            $table->string('ref_texto')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index('tradein_credit_movements_user_id_foreign');
            $table->timestamps();

            $table->index(['empresa_id', 'documento']);
            $table->index(['origem_tipo', 'origem_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tradein_credit_movements');
    }
};
