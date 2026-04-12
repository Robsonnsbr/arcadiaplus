<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Produto;
use App\Models\TradeinInventoryItem;
use Illuminate\Http\Request;

class TradeinInventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tradein_view', ['only' => ['index', 'transferRedirect', 'edit', 'update']]);
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
            'empresa_id'           => $request->empresa_id,
            'quantidade'           => 1,
            'tradein_inventory_id' => $item->id,
            'tradein_id'           => $item->tradein_id,
            'descricao_item'       => $item->descricao_item,
            'produto_id'           => $item->produto_id,
            'serial'               => $item->serial,
            'valor'                => $item->valor,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $item = TradeinInventoryItem::where('empresa_id', $request->empresa_id)->findOrFail($id);
        __validaObjetoEmpresa($item);

        $produto = $item->produto_id ? Produto::find($item->produto_id) : null;

        return view('tradein.inventory_edit', compact('item', 'produto'));
    }

    public function update(Request $request, $id)
    {
        $item = TradeinInventoryItem::where('empresa_id', $request->empresa_id)->findOrFail($id);
        __validaObjetoEmpresa($item);

        $request->validate([
            'produto_id'         => 'nullable|integer|exists:produtos,id',
            'serial'             => 'nullable|string|max:120',
            'descricao_item'     => 'nullable|string|max:255',
            'observacao_tecnica' => 'nullable|string|max:1000',
            'status'             => 'nullable|in:' . TradeinInventoryItem::STATUS_PENDING_TRANSFER . ',' . TradeinInventoryItem::STATUS_TRANSFERRED,
        ], [
            'produto_id.exists'  => 'Produto não encontrado no catálogo.',
        ]);

        $item->update([
            'produto_id'         => $request->filled('produto_id') ? (int)$request->produto_id : $item->produto_id,
            'serial'             => $request->filled('serial') ? trim($request->serial) : $item->serial,
            'descricao_item'     => $request->filled('descricao_item') ? trim($request->descricao_item) : $item->descricao_item,
            'observacao_tecnica' => $request->filled('observacao_tecnica') ? trim($request->observacao_tecnica) : $item->observacao_tecnica,
            'status'             => $request->filled('status') ? $request->status : $item->status,
        ]);

        session()->flash('flash_success', 'Item de inventário atualizado com sucesso!');
        return redirect()->route('tradein.inventory.index', ['empresa_id' => $request->empresa_id]);
    }
}
