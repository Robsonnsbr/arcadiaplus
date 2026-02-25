<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tradein;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TradeinController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:pdv_view', ['only' => ['show']]);
        $this->middleware('permission:pdv_edit', ['only' => ['accept', 'reject', 'store']]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|integer',
            'cliente_id' => 'required|integer',
            'nome_item' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:120',
            'valor_pretendido' => 'nullable',
            'observacao' => 'nullable|string',
        ]);

        $tradein = Tradein::create([
            'empresa_id' => $request->empresa_id,
            'cliente_id' => $request->cliente_id,
            'created_by_user_id' => Auth::id(),
            'status' => Tradein::STATUS_SUBMITTED,
            'nome_item' => $request->nome_item,
            'serial_number' => $request->serial_number,
            'valor_pretendido' => $request->valor_pretendido ? __convert_value_bd($request->valor_pretendido) : null,
            'observacao_vendedor' => $request->observacao,
        ]);

        return response()->json([
            'id' => $tradein->id,
            'status' => $tradein->status,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $tradein = Tradein::findOrFail($id);
        __validaObjetoEmpresa($tradein);

        return response()->json([
            'id' => $tradein->id,
            'status' => $tradein->status,
            'valor_avaliado' => $tradein->valor_avaliado,
            'avaliado_em' => $tradein->avaliado_em,
            'status_aceite_cliente' => $tradein->status_aceite_cliente,
            'aceite_em' => $tradein->aceite_em,
        ], 200);
    }

    public function accept(Request $request, $id)
    {
        $tradein = Tradein::findOrFail($id);
        __validaObjetoEmpresa($tradein);

        if ($tradein->status !== Tradein::STATUS_COMPLETED) {
            return response()->json('Trade-in ainda não concluído.', 422);
        }

        if ($tradein->status_aceite_cliente !== Tradein::ACEITE_ACCEPTED) {
            $tradein->status_aceite_cliente = Tradein::ACEITE_ACCEPTED;
            $tradein->aceite_em = $tradein->aceite_em ?? now();
            $tradein->save();
        }

        return response()->json([
            'status_aceite_cliente' => $tradein->status_aceite_cliente,
            'aceite_em' => $tradein->aceite_em,
        ], 200);
    }

    public function reject(Request $request, $id)
    {
        $tradein = Tradein::findOrFail($id);
        __validaObjetoEmpresa($tradein);

        if ($tradein->status !== Tradein::STATUS_COMPLETED) {
            return response()->json('Trade-in ainda não concluído.', 422);
        }

        if ($tradein->status_aceite_cliente !== Tradein::ACEITE_REJECTED) {
            $tradein->status_aceite_cliente = Tradein::ACEITE_REJECTED;
            $tradein->aceite_em = $tradein->aceite_em ?? now();
            $tradein->save();
        }

        return response()->json([
            'status_aceite_cliente' => $tradein->status_aceite_cliente,
            'aceite_em' => $tradein->aceite_em,
        ], 200);
    }
}
