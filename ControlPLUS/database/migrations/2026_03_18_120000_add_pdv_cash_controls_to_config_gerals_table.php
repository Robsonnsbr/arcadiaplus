<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('config_gerals', function (Blueprint $table) {
            $table->boolean('pdv_habilitar_sangria')->default(true)->after('documento_pdv');
            $table->boolean('pdv_habilitar_suprimentos')->default(true)->after('pdv_habilitar_sangria');
        });
    }

    public function down()
    {
        Schema::table('config_gerals', function (Blueprint $table) {
            $table->dropColumn(['pdv_habilitar_sangria', 'pdv_habilitar_suprimentos']);
        });
    }
};
