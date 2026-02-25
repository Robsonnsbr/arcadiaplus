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
        Schema::create('tradein_inventory_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index();
            $table->unsignedBigInteger('tradein_id')->unique();
            $table->unsignedBigInteger('cliente_id')->nullable()->index();
            $table->string('descricao_item', 255);
            $table->string('serial', 120)->nullable();
            $table->decimal('valor', 12, 2)->nullable();
            $table->string('status', 30)->default('pending_transfer');
            $table->text('observacao_tecnica')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable()->index();
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
        Schema::dropIfExists('tradein_inventory_items');
    }
};
