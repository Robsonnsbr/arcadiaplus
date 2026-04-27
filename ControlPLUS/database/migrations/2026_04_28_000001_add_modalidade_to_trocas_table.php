<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trocas', function (Blueprint $table) {
            $table->string('modalidade', 32)->default('troca')->after('empresa_id');
        });
    }

    public function down(): void
    {
        Schema::table('trocas', function (Blueprint $table) {
            $table->dropColumn('modalidade');
        });
    }
};
