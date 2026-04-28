<?php

namespace App\Http\Controllers;

use App\Models\FuncionarioCargo;
use Illuminate\Http\Request;

class FuncionarioCargoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:funcionario_create|funcionario_edit');
    }

    public function index(Request $request)
    {
        $data = FuncionarioCargo::withCount('funcionarios')
        ->where('status', 1)
        ->where(function ($q) use ($request) {
            $q->whereNull('empresa_id')
            ->orWhere('empresa_id', $request->empresa_id);
        })
        ->orderBy('empresa_id')
        ->orderBy('nome')
        ->get();

        $data->each(function ($cargo) use ($request) {
            $cargo->can_manage = (int) $cargo->empresa_id === (int) $request->empresa_id;
        });

        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:60',
            'empresa_id' => 'required',
        ]);

        $nome = trim($request->nome);

        $cargo = FuncionarioCargo::where('nome', $nome)
        ->where(function ($q) use ($request) {
            $q->whereNull('empresa_id')
            ->orWhere('empresa_id', $request->empresa_id);
        })
        ->first();

        if (!$cargo) {
            $cargo = FuncionarioCargo::create([
                'empresa_id' => $request->empresa_id,
                'nome' => $nome,
                'status' => true,
            ]);
        }

        return response()->json($cargo, 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:60',
            'empresa_id' => 'required',
        ]);

        $cargo = $this->findCargo($request, $id);

        if (!$this->canManage($request, $cargo)) {
            return response()->json([
                'message' => 'Cargos padrão não podem ser editados pela modal. Crie um novo cargo para personalizar a lista desta empresa.',
            ], 422);
        }

        $cargo->nome = trim($request->nome);
        $cargo->save();

        return response()->json($cargo, 200);
    }

    public function destroy(Request $request, $id)
    {
        $request->validate([
            'empresa_id' => 'required',
        ]);

        $cargo = $this->findCargo($request, $id);

        if (!$this->canManage($request, $cargo)) {
            return response()->json([
                'message' => 'Cargos padrão não podem ser excluídos.',
            ], 422);
        }

        if ($cargo->funcionarios()->exists()) {
            return response()->json([
                'message' => 'Este cargo não pode ser excluído porque já está vinculado a um ou mais funcionários.',
            ], 422);
        }

        $cargo->delete();

        return response()->json('', 200);
    }

    private function findCargo(Request $request, $id)
    {
        return FuncionarioCargo::where('id', $id)
        ->where(function ($q) use ($request) {
            $q->whereNull('empresa_id')
            ->orWhere('empresa_id', $request->empresa_id);
        })
        ->firstOrFail();
    }

    private function canManage(Request $request, FuncionarioCargo $cargo)
    {
        return (int) $cargo->empresa_id === (int) $request->empresa_id;
    }
}
