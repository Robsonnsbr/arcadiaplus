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
        Schema::create('email_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('email_configs_empresa_id_foreign');
            $table->string('host', 50);
            $table->string('email', 50);
            $table->string('senha', 50);
            $table->string('nome', 50);
            $table->string('porta', 10);
            $table->enum('cripitografia', ['ssl', 'tls']);
            $table->boolean('smtp_auth');
            $table->boolean('smtp_debug');
            $table->boolean('status');
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
        Schema::dropIfExists('email_configs');
    }
};
