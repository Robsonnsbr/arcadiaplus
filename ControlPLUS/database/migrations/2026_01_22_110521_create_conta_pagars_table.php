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
        Schema::create('conta_pagars', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('conta_pagars_empresa_id_foreign');
            $table->unsignedBigInteger('nfe_id')->nullable()->index('conta_pagars_nfe_id_foreign');
            $table->unsignedBigInteger('fornecedor_id')->nullable()->index('conta_pagars_fornecedor_id_foreign');
            $table->string('descricao', 200)->nullable();
            $table->string('referencia', 60)->nullable();
            $table->string('arquivo', 25)->nullable();
            $table->decimal('valor_integral', 16, 7);
            $table->decimal('valor_pago', 16, 7)->nullable();
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->boolean('status')->default(false);
            $table->string('observacao', 100)->nullable();
            $table->string('observacao2', 100)->nullable();
            $table->string('observacao3', 100)->nullable();
            $table->string('tipo_pagamento', 2)->nullable();
            $table->unsignedBigInteger('caixa_id')->nullable()->index('conta_pagars_caixa_id_foreign');
            $table->integer('local_id')->nullable();
            $table->string('motivo_estorno')->nullable();
            $table->integer('categoria_conta_id')->nullable();
            $table->decimal('desconto', 16, 7)->nullable();
            $table->decimal('acrescimo', 16, 7)->nullable();
            $table->integer('conta_empresa_id')->nullable();
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
        Schema::dropIfExists('conta_pagars');
    }
};
