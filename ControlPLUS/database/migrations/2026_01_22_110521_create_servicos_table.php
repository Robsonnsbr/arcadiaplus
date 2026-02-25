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
        Schema::create('servicos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->index('servicos_empresa_id_foreign');
            $table->integer('numero_sequencial')->nullable();
            $table->string('nome', 60);
            $table->decimal('valor', 10);
            $table->string('unidade_cobranca', 5);
            $table->integer('tempo_servico');
            $table->integer('tempo_adicional')->default(0);
            $table->integer('tempo_tolerancia')->default(0);
            $table->decimal('valor_adicional', 10)->default(0);
            $table->decimal('comissao', 6)->default(0);
            $table->unsignedBigInteger('categoria_id')->index('servicos_categoria_id_foreign');
            $table->string('codigo_servico', 10)->nullable();
            $table->decimal('aliquota_iss', 6)->nullable();
            $table->decimal('aliquota_pis', 6)->nullable();
            $table->decimal('aliquota_cofins', 6)->nullable();
            $table->decimal('aliquota_inss', 6)->nullable();
            $table->string('imagem', 25)->nullable();
            $table->boolean('status')->nullable()->default(true);
            $table->boolean('reserva')->nullable()->default(false);
            $table->boolean('padrao_reserva_nfse')->nullable()->default(false);
            $table->boolean('marketplace')->nullable()->default(false);
            $table->string('codigo_tributacao_municipio', 30)->nullable();
            $table->string('hash_delivery', 50)->nullable();
            $table->text('descricao')->nullable();
            $table->boolean('destaque_marketplace')->nullable();
            $table->decimal('aliquota_ir', 7)->nullable();
            $table->decimal('aliquota_csll', 7)->nullable();
            $table->decimal('valor_deducoes', 16, 7)->nullable();
            $table->decimal('desconto_incondicional', 16, 7)->nullable();
            $table->decimal('desconto_condicional', 16, 7)->nullable();
            $table->decimal('outras_retencoes', 16, 7)->nullable();
            $table->string('estado_local_prestacao_servico', 2)->nullable();
            $table->string('natureza_operacao', 100)->nullable();
            $table->string('codigo_cnae', 30)->nullable();
            $table->integer('prazo_garantia')->nullable();
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
        Schema::dropIfExists('servicos');
    }
};
