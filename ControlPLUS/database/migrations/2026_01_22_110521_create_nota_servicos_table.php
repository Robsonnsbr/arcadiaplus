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
        Schema::create('nota_servicos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('nota_servicos_empresa_id_foreign');
            $table->unsignedBigInteger('cliente_id')->nullable()->index('nota_servicos_cliente_id_foreign');
            $table->unsignedBigInteger('cidade_id')->nullable()->index('nota_servicos_cidade_id_foreign');
            $table->decimal('valor_total', 16, 7);
            $table->enum('estado', ['novo', 'rejeitado', 'aprovado', 'cancelado', 'processando']);
            $table->string('serie', 3);
            $table->string('codigo_verificacao', 20);
            $table->integer('numero_nfse');
            $table->string('url_xml');
            $table->string('url_pdf_nfse');
            $table->string('url_pdf_rps');
            $table->string('documento', 18);
            $table->string('razao_social', 60);
            $table->string('im', 20)->nullable();
            $table->string('ie', 20)->nullable();
            $table->string('cep', 9);
            $table->string('rua', 80);
            $table->string('numero', 20);
            $table->string('bairro', 40);
            $table->string('complemento', 80)->nullable();
            $table->string('email', 80)->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('natureza_operacao', 100)->nullable();
            $table->string('uuid', 100)->nullable();
            $table->string('chave', 50)->nullable();
            $table->timestamps();
            $table->integer('ambiente')->nullable();
            $table->boolean('gerar_conta_receber')->nullable();
            $table->date('data_vencimento')->nullable();
            $table->integer('conta_receber_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nota_servicos');
    }
};
