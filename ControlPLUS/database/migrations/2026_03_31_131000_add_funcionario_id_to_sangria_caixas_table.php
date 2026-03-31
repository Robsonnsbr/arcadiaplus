<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sangria_caixas', function (Blueprint $table) {
            $table->unsignedBigInteger('funcionario_id')->nullable()->after('conta_empresa_id');
            $table->foreign('funcionario_id')->references('id')->on('funcionarios')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('sangria_caixas', function (Blueprint $table) {
            $table->dropForeign(['funcionario_id']);
            $table->dropColumn('funcionario_id');
        });
    }
};
