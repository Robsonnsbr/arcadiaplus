<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\TradeinInventoryItem;
use Illuminate\Http\Request;

class TradeinInventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tradein_view', ['only' => ['index', 'transferRedirect']]);
    }

    public function index(Request $request)
    {
        $status = $request->get('status');

        $items = TradeinInventoryItem::where('empresa_id', $request->empresa_id)
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->orderBy('id', 'desc')
            ->paginate(__itensPagina());

        $clienteIds = $items->pluck('cliente_id')->filter()->unique()->values();
        $clientes = $clienteIds->isEmpty()
            ? collect()
            : Cliente::whereIn('id', $clienteIds)->pluck('razao_social', 'id');

        return view('tradein.inventory', compact('items', 'clientes', 'status'));
    }

    public function transferRedirect(Request $request, $id)
    {
        $item = TradeinInventoryItem::where('empresa_id', $request->empresa_id)->findOrFail($id);
        __validaObjetoEmpresa($item);

        return redirect()->route('estoque.create', [
            'empresa_id' => $request->empresa_id,
            'quantidade' => 1,
            'tradein_inventory_id' => $item->id,
            'tradein_id' => $item->tradein_id,
            'descricao_item' => $item->descricao_item,
            'serial' => $item->serial,
            'valor' => $item->valor,
        ]);
    }
}
