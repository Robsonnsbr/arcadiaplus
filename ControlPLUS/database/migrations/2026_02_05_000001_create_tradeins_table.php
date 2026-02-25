<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tradeins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('created_by_user_id');
            $table->unsignedBigInteger('assigned_to_user_id')->nullable();
            $table->string('status', 20);

            $table->string('nome_item');
            $table->string('serial_number', 120)->nullable();
            $table->decimal('valor_pretendido', 12, 2)->nullable();
            $table->text('observacao_vendedor')->nullable();

            $table->boolean('check_tela_ok')->nullable();
            $table->boolean('check_bateria_ok')->nullable();
            $table->boolean('check_carregamento_ok')->nullable();
            $table->boolean('check_botoes_ok')->nullable();
            $table->boolean('check_camera_ok')->nullable();
            $table->text('observacao_tecnico')->nullable();
            $table->decimal('valor_avaliado', 12, 2)->nullable();
            $table->dateTime('avaliado_em')->nullable();

            $table->string('status_aceite_cliente', 20)->default('pending');
            $table->dateTime('aceite_em')->nullable();

            $table->timestamps();

            $table->index(['empresa_id', 'status']);
            $table->index('cliente_id');
            $table->index('created_by_user_id');
            $table->index('assigned_to_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tradeins');
    }
};
