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
        Schema::create('configuracao_cardapios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('configuracao_cardapios_empresa_id_foreign');
            $table->string('nome_restaurante', 100);
            $table->text('descricao_restaurante_pt')->nullable();
            $table->text('descricao_restaurante_en')->nullable();
            $table->text('descricao_restaurante_es')->nullable();
            $table->string('logo', 25);
            $table->string('fav_icon', 25);
            $table->string('telefone', 25);
            $table->string('rua', 80);
            $table->string('numero', 25);
            $table->string('bairro', 25);
            $table->unsignedBigInteger('cidade_id')->index('configuracao_cardapios_cidade_id_foreign');
            $table->string('api_token', 25);
            $table->string('link_instagran', 150)->nullable();
            $table->string('link_facebook', 150)->nullable();
            $table->string('link_whatsapp', 150)->nullable();
            $table->boolean('intercionalizar')->default(false);
            $table->boolean('incluir_servico')->nullable()->default(false);
            $table->boolean('qr_code_mesa')->nullable()->default(false);
            $table->boolean('confirma_mesa')->nullable()->default(false);
            $table->enum('valor_pizza', ['divide', 'valor_maior'])->nullable()->default('divide');
            $table->decimal('percentual_taxa_servico', 5)->nullable()->default(0);
            $table->string('cor_principal', 10)->nullable();
            $table->integer('limite_pessoas_qr_code')->nullable();
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
        Schema::dropIfExists('configuracao_cardapios');
    }
};
