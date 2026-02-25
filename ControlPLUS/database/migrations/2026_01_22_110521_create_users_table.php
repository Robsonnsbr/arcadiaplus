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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('imagem', 25)->nullable();
            $table->boolean('admin')->default(true);
            $table->boolean('sidebar_active')->nullable()->default(true);
            $table->boolean('notificacao_cardapio')->nullable()->default(false);
            $table->boolean('notificacao_marketplace')->nullable()->default(false);
            $table->boolean('notificacao_ecommerce')->nullable()->default(false);
            $table->boolean('tipo_contador')->nullable()->default(false);
            $table->boolean('escolher_localidade_venda')->nullable()->default(false);
            $table->boolean('suporte')->nullable()->default(false);
            $table->boolean('status')->nullable()->default(true);
            $table->string('tema_padrao', 10)->nullable();
            $table->integer('plano_auto_cadastro')->nullable();
            $table->string('finalizacao_pdv', 25)->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
