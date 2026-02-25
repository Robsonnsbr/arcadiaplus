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
        Schema::create('mercado_livre_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('mercado_livre_configs_empresa_id_foreign');
            $table->string('client_id', 30);
            $table->string('client_secret', 100);
            $table->string('access_token')->nullable();
            $table->string('user_id', 25)->nullable();
            $table->string('code', 100)->nullable();
            $table->string('url', 120);
            $table->timestamps();
            $table->string('refresh_token')->nullable();
            $table->bigInteger('token_expira')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mercado_livre_configs');
    }
};
