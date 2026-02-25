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
        Schema::create('parcelamento_mdves', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mdfe_id')->index('parcelamento_mdves_mdfe_id_foreign');
            $table->decimal('valor', 10);
            $table->date('vencimento');
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
        Schema::dropIfExists('parcelamento_mdves');
    }
};
