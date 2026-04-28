<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('funcionario_cargos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('empresa_id')->nullable()->index('funcionario_cargos_empresa_id_foreign');
            $table->string('nome', 60);
            $table->boolean('status')->nullable()->default(true);
            $table->timestamps();
            $table->foreign(['empresa_id'])->references(['id'])->on('empresas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });

        $now = now();
        DB::table('funcionario_cargos')->insert([
            ['empresa_id' => null, 'nome' => 'vendedor', 'status' => true, 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => null, 'nome' => 'consultor', 'status' => true, 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => null, 'nome' => 'gerente', 'status' => true, 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => null, 'nome' => 'assistente', 'status' => true, 'created_at' => $now, 'updated_at' => $now],
            ['empresa_id' => null, 'nome' => 'outros', 'status' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        Schema::table('funcionarios', function (Blueprint $table) {
            $table->unsignedBigInteger('funcionario_cargo_id')->nullable()->after('usuario_id')->index('funcionarios_funcionario_cargo_id_foreign');
            $table->foreign(['funcionario_cargo_id'])->references(['id'])->on('funcionario_cargos')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->dropForeign(['funcionario_cargo_id']);
            $table->dropColumn('funcionario_cargo_id');
        });

        Schema::dropIfExists('funcionario_cargos');
    }
};
