<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Tradein;
use App\Models\TradeinCreditMovement;
use App\Models\TradeinInventoryItem;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TradeinController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tradein_view', ['only' => ['index', 'edit']]);
        $this->middleware('permission:tradein_edit', ['only' => ['update']]);
        $this->middleware('permission:pdv_edit', ['only' => ['storeWeb']]);
        $this->middleware('permission:pdv_view', ['only' => ['status', 'creditBalance']]);
        $this->middleware('permission:pdv_edit', ['only' => ['accept', 'reject', 'cancel', 'creditDebit']]);
    }

    public function index(Request $request)
    {
        $tradeins = Tradein::where('empresa_id', $request->empresa_id)
            ->whereIn('status', [Tradein::STATUS_SUBMITTED, Tradein::STATUS_IN_REVIEW, Tradein::STATUS_COMPLETED])
            ->orderBy('created_at', 'desc')
            ->paginate(__itensPagina());

        $clienteIds = $tradeins->pluck('cliente_id')->filter()->unique()->values();
        $clientes = $clienteIds->isEmpty()
            ? collect()
            : Cliente::whereIn('id', $clienteIds)->pluck('razao_social', 'id');

        return view('tradein.index', compact('tradeins', 'clientes'));
    }

    public function edit(Request $request, $id)
    {
        $tradein = Tradein::where('empresa_id', $request->empresa_id)->findOrFail($id);
        __validaObjetoEmpresa($tradein);

        if ($tradein->status === Tradein::STATUS_SUBMITTED) {
            $tradein->status = Tradein::STATUS_IN_REVIEW;
            $tradein->assigned_to_user_id = Auth::id();
            $tradein->save();
        }

        $cliente = $tradein->cliente_id ? Cliente::find($tradein->cliente_id) : null;

        return view('tradein.form', compact('tradein', 'cliente'));
    }

    public function update(Request $request, $id)
    {
        $tradein = Tradein::where('empresa_id', $request->empresa_id)->findOrFail($id);
        __validaObjetoEmpresa($tradein);

        $concluir = $request->has('concluir_avaliacao');

        $rules = [
            'check_tela_ok' => 'nullable|boolean',
            'check_bateria_ok' => 'nullable|boolean',
            'check_carregamento_ok' => 'nullable|boolean',
            'check_botoes_ok' => 'nullable|boolean',
            'check_camera_ok' => 'nullable|boolean',
            'observacao_tecnico' => 'nullable|string',
            'valor_avaliado' => $concluir ? 'required|numeric|min:0.01' : 'nullable|numeric|min:0',
        ];
        $request->validate($rules);

        $tradein->check_tela_ok = $request->has('check_tela_ok') ? 1 : null;
        $tradein->check_bateria_ok = $request->has('check_bateria_ok') ? 1 : null;
        $tradein->check_carregamento_ok = $request->has('check_carregamento_ok') ? 1 : null;
        $tradein->check_botoes_ok = $request->has('check_botoes_ok') ? 1 : null;
        $tradein->check_camera_ok = $request->has('check_camera_ok') ? 1 : null;
        $tradein->observacao_tecnico = $request->observacao_tecnico;
        $tradein->valor_avaliado = $request->valor_avaliado ? __convert_value_bd($request->valor_avaliado) : null;

        if ($concluir) {
            $tradein->status = Tradein::STATUS_COMPLETED;
            $tradein->avaliado_em = now();
        } elseif ($tradein->status !== Tradein::STATUS_COMPLETED) {
            $tradein->status = Tradein::STATUS_IN_REVIEW;
        }

        if (!$tradein->assigned_to_user_id) {
            $tradein->assigned_to_user_id = Auth::id();
        }

        $tradein->save();

        session()->flash('flash_success', 'Avaliacao atualizada.');
        return redirect()->route('tradein.edit', ['id' => $tradein->id, 'empresa_id' => $request->empresa_id]);
    }

    public function status(Request $request, $id)
    {
        $tradein = Tradein::findOrFail($id);
        if ($request->empresa_id && (int) $request->empresa_id !== (int) $tradein->empresa_id) {
            abort(403);
        }
        __validaObjetoEmpresa($tradein);

        return response()->json([
            'id' => $tradein->id,
            'status' => $tradein->status,
            'client_decision_status' => $tradein->status_aceite_cliente,
            'status_aceite_cliente' => $tradein->status_aceite_cliente,
            'valor_avaliado' => $tradein->valor_avaliado,
            'valor_pretendido' => $tradein->valor_pretendido,
            'term_generated_at' => $tradein->term_generated_at,
            'term_generated' => (bool) $tradein->term_generated_at,
            'updated_at' => $tradein->updated_at,
        ], 200);
    }

    public function accept(Request $request, $id)
    {
        $creditCreated = false;
        $creditValue = 0.0;
        $inventoryCreated = false;

        try {
            $result = DB::transaction(function () use ($request, $id, &$creditCreated, &$creditValue, &$inventoryCreated) {
                $tradein = Tradein::where('id', $id)->lockForUpdate()->firstOrFail();
                if ($request->empresa_id && (int) $request->empresa_id !== (int) $tradein->empresa_id) {
                    abort(403);
                }
                __validaObjetoEmpresa($tradein);

                if ($tradein->status !== Tradein::STATUS_COMPLETED || !$tradein->valor_avaliado) {
                    return response()->json('Trade-in ainda não concluído.', 422);
                }

                if ($tradein->status_aceite_cliente !== Tradein::ACEITE_ACCEPTED) {
                    $tradein->status_aceite_cliente = Tradein::ACEITE_ACCEPTED;
                    $tradein->aceite_em = $tradein->aceite_em ?? now();
                    $tradein->save();
                }

                $creditValue = (float) $tradein->valor_avaliado;
                $alreadyCredited = TradeinCreditMovement::where('empresa_id', $tradein->empresa_id)
                    ->where('origem_tipo', 'tradein_accept')
                    ->where('origem_id', $tradein->id)
                    ->where('tipo', TradeinCreditMovement::TYPE_CREDIT)
                    ->exists();

                if (!$alreadyCredited) {
                    try {
                        TradeinCreditMovement::create([
                            'empresa_id' => $tradein->empresa_id,
                            'cliente_id' => $tradein->cliente_id,
                            'tipo' => TradeinCreditMovement::TYPE_CREDIT,
                            'valor' => $creditValue,
                            'origem_tipo' => 'tradein_accept',
                            'origem_id' => $tradein->id,
                            'ref_texto' => 'Crédito Trade-in #' . $tradein->id,
                            'user_id' => Auth::id(),
                        ]);
                        $creditCreated = true;
                    } catch (QueryException $e) {
                        if (!$this->isDuplicateKey($e)) {
                            throw $e;
                        }
                    }
                }

                $alreadyInInventory = TradeinInventoryItem::where('tradein_id', $tradein->id)->exists();
                if (!$alreadyInInventory) {
                    try {
                        TradeinInventoryItem::create([
                            'empresa_id' => $tradein->empresa_id,
                            'tradein_id' => $tradein->id,
                            'cliente_id' => $tradein->cliente_id,
                            'descricao_item' => $tradein->nome_item,
                            'serial' => $tradein->serial_number,
                            'valor' => $tradein->valor_avaliado,
                            'status' => TradeinInventoryItem::STATUS_PENDING_TRANSFER,
                            'observacao_tecnica' => $tradein->observacao_tecnico,
                            'created_by_user_id' => Auth::id(),
                        ]);
                        $inventoryCreated = true;
                    } catch (QueryException $e) {
                        if (!$this->isDuplicateKey($e)) {
                            throw $e;
                        }
                    }
                }

                return $tradein;
            });
        } catch (\RuntimeException $e) {
            return response()->json($e->getMessage(), 422);
        }

        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }

        return response()->json([
            'tradein_id' => $result->id,
            'status_aceite_cliente' => $result->status_aceite_cliente,
            'client_decision_status' => $result->status_aceite_cliente,
            'credit_created' => $creditCreated,
            'credit_value' => $creditValue,
            'inventory_created' => $inventoryCreated,
        ], 200);
    }

    public function creditBalance(Request $request, $clienteId)
    {
        $empresaId = $request->empresa_id;
        if (!$empresaId) {
            return response()->json('Empresa inválida.', 422);
        }

        $credit = (float) TradeinCreditMovement::where('empresa_id', $empresaId)
            ->where('cliente_id', $clienteId)
            ->where('tipo', TradeinCreditMovement::TYPE_CREDIT)
            ->sum('valor');

        $debit = (float) TradeinCreditMovement::where('empresa_id', $empresaId)
            ->where('cliente_id', $clienteId)
            ->where('tipo', TradeinCreditMovement::TYPE_DEBIT)
            ->sum('valor');

        return response()->json([
            'cliente_id' => (int) $clienteId,
            'empresa_id' => (int) $empresaId,
            'saldo' => $credit - $debit,
        ], 200);
    }

    public function creditDebit(Request $request)
    {
        $request->validate([
            'empresa_id' => 'required|integer',
            'cliente_id' => 'required|integer',
            'valor' => 'required',
            'origem_id' => 'required|integer',
            'origem_tipo' => 'required|string',
        ]);

        $empresaId = (int) $request->empresa_id;
        $clienteId = (int) $request->cliente_id;
        $valor = (float) __convert_value_bd($request->valor);

        if ($valor <= 0) {
            return response()->json('Valor inválido para uso de crédito.', 422);
        }

        $cliente = Cliente::find($clienteId);
        if (!$cliente || (int) $cliente->empresa_id !== $empresaId) {
            abort(403);
        }

        return DB::transaction(function () use ($empresaId, $clienteId, $valor, $request, $cliente) {
            $movements = TradeinCreditMovement::where('empresa_id', $empresaId)
                ->where('cliente_id', $clienteId)
                ->lockForUpdate()
                ->get();

            $saldo = $movements->reduce(function ($carry, TradeinCreditMovement $movement) {
                return $carry + ($movement->tipo === TradeinCreditMovement::TYPE_CREDIT ? $movement->valor : -$movement->valor);
            }, 0.0);

            if ($saldo < $valor - 0.0001) {
                return response()->json('Saldo trade-in insuficiente.', 422);
            }

            $documento = TradeinCreditMovement::sanitizeDocumento($cliente->cpf_cnpj) ?? '';

            TradeinCreditMovement::create([
                'empresa_id' => $empresaId,
                'documento' => $documento,
                'cliente_id' => $clienteId,
                'tipo' => TradeinCreditMovement::TYPE_DEBIT,
                'valor' => $valor,
                'origem_tipo' => 'pdv_payment',
                'origem_id' => (int) $request->origem_id,
                'ref_texto' => 'Uso de crédito trade-in no PDV',
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'cliente_id' => $clienteId,
                'empresa_id' => $empresaId,
                'saldo_restante' => $saldo - $valor,
            ], 200);
        });
    }

    public function reject(Request $request, $id)
    {
        $tradein = Tradein::findOrFail($id);
        if ($request->empresa_id && (int) $request->empresa_id !== (int) $tradein->empresa_id) {
            abort(403);
        }
        __validaObjetoEmpresa($tradein);

        if ($tradein->status !== Tradein::STATUS_COMPLETED || !$tradein->valor_avaliado) {
            return response()->json('Trade-in ainda não concluído.', 422);
        }

        if ($tradein->status_aceite_cliente !== Tradein::ACEITE_REJECTED) {
            $tradein->status_aceite_cliente = Tradein::ACEITE_REJECTED;
            $tradein->aceite_em = $tradein->aceite_em ?? now();
            $tradein->save();
        }

        return response()->json([
            'client_decision_status' => $tradein->status_aceite_cliente,
            'status_aceite_cliente' => $tradein->status_aceite_cliente,
            'aceite_em' => $tradein->aceite_em,
        ], 200);
    }

    public function cancel(Request $request, $id)
    {
        $tradein = Tradein::findOrFail($id);
        if ($request->empresa_id && (int) $request->empresa_id !== (int) $tradein->empresa_id) {
            abort(403);
        }
        __validaObjetoEmpresa($tradein);

        if (in_array($tradein->status_aceite_cliente, [Tradein::ACEITE_ACCEPTED, Tradein::ACEITE_REJECTED], true)) {
            return response()->json('Trade-in com aceite/recusa não pode ser cancelado.', 422);
        }

        $tradein->delete();

        return response()->json(['ok' => true], 200);
    }

    public function start(Request $request, $id)
    {
        $tradein = Tradein::findOrFail($id);
        __validaObjetoEmpresa($tradein);

        if ($tradein->status === Tradein::STATUS_COMPLETED || $tradein->status === Tradein::STATUS_CANCELLED) {
            session()->flash('flash_warning', 'Trade-in não pode ser iniciado.');
            return redirect()->route('tradein.index');
        }

        $tradein->status = Tradein::STATUS_IN_REVIEW;
        $tradein->assigned_to_user_id = Auth::id();
        $tradein->save();

        session()->flash('flash_success', 'Trade-in iniciado.');
        return redirect()->route('tradein.edit', $tradein->id);
    }

    public function complete(Request $request, $id)
    {
        $tradein = Tradein::findOrFail($id);
        __validaObjetoEmpresa($tradein);

        if ($tradein->status !== Tradein::STATUS_IN_REVIEW) {
            session()->flash('flash_warning', 'Trade-in deve estar em análise para concluir.');
            return redirect()->route('tradein.edit', $tradein->id);
        }

        $request->validate([
            'valor_avaliado' => 'required',
        ]);

        $tradein->check_tela_ok = $request->check_tela_ok;
        $tradein->check_bateria_ok = $request->check_bateria_ok;
        $tradein->check_carregamento_ok = $request->check_carregamento_ok;
        $tradein->check_botoes_ok = $request->check_botoes_ok;
        $tradein->check_camera_ok = $request->check_camera_ok;
        $tradein->observacao_tecnico = $request->observacao_tecnico;
        $tradein->valor_avaliado = __convert_value_bd($request->valor_avaliado);
        $tradein->avaliado_em = now();
        $tradein->status = Tradein::STATUS_COMPLETED;
        $tradein->save();

        session()->flash('flash_success', 'Trade-in concluído.');
        return redirect()->route('tradein.index');
    }

    public function termoPdf(Request $request, $id)
    {
        $tradein = Tradein::findOrFail($id);
        __validaObjetoEmpresa($tradein);

        $html = view('tradein.termo', compact('tradein'));

        $domPdf = new Dompdf(['enable_remote' => true]);
        $domPdf->loadHtml($html);
        $domPdf->render();

        if (!$tradein->term_generated_at) {
            $tradein->term_generated_at = now();
            $tradein->save();
        }

        return $domPdf->stream("Termo Trade-in #{$tradein->id}.pdf", ['Attachment' => false]);
    }

    public function storeWeb(Request $request)
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

    private function isDuplicateKey(QueryException $e): bool
    {
        return isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062;
    }
}
