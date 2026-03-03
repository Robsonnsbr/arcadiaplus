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
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

class TradeinController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tradein_view', ['only' => ['index', 'edit']]);
        $this->middleware('permission:tradein_view|pdv_view', ['only' => ['modalForm']]);
        $this->middleware('permission:tradein_edit|pdv_edit', ['only' => ['update']]);
        $this->middleware('permission:pdv_edit', ['only' => ['storeWeb']]);
        $this->middleware('permission:pdv_view', ['only' => ['status', 'creditBalance']]);
        $this->middleware('permission:pdv_edit', ['only' => ['accept', 'reject', 'cancel', 'creditDebit']]);
    }

    private function checklistTemplate(): array
    {
        return [
            'aparelho_liga_corretamente' => 'Aparelho liga corretamente',
            'avarias_travamentos_toque_fantasma' => 'Avarias, travamentos ou toque fantasma',
            'manchas_na_tela' => 'Manchas na tela',
            'botoes_funcionando' => 'Botões funcionando',
            'marcas_de_uso' => 'Marcas de uso',
            'wifi_funcionando' => 'Wi-Fi funcionando',
            'chip_funcionando' => 'Chip funcionando',
            'rede_4g_5g_funcionando' => '4G/5G funcionando',
            'sensores_nfc_funcionando' => 'Sensores funcionando / NFC',
            'face_touch_id_funcionando' => 'Face ID / Touch ID funcionando',
            'microfones_funcionando' => 'Microfones funcionando',
            'audio_auricular_funcionando' => 'Áudio auricular funcionando',
            'audio_alto_falante_funcionando' => 'Áudio alto-falante funcionando',
            'entrada_carregamento_funcionando' => 'Entrada de carregamento funcionando',
            'cameras_funcionando_manchas' => 'Câmeras funcionando / Manchas',
            'flash_funcionando' => 'Flash funcionando',
            'possui_carregador' => 'Possui carregador',
            'analise_3utools_ok' => 'Análise pelo 3uTools OK',
            'saude_bateria' => 'Saúde da bateria',
        ];
    }

    private function parseMoneyNullable($value): ?float
    {
        if ($value === null) {
            return null;
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        if (is_numeric($raw)) {
            return (float) $raw;
        }

        $normalized = preg_replace('/[^0-9,\.\-]/', '', $raw);
        if ($normalized === null || $normalized === '') {
            return null;
        }

        return (float) __convert_value_bd($normalized);
    }

    private function resultadoToBool(?string $resultado): ?int
    {
        $resultado = strtoupper(trim((string) $resultado));
        if ($resultado === 'SIM') {
            return 1;
        }
        if ($resultado === 'NAO') {
            return 0;
        }
        return null;
    }

    private function resolveTradeinSnapshot(Tradein $tradein, ?Cliente $cliente = null): array
    {
        $cliente = $cliente ?: ($tradein->cliente_id ? Cliente::find($tradein->cliente_id) : null);
        $checklistTemplate = $this->checklistTemplate();
        $snapshot = is_array($tradein->avaliacao_snapshot) ? $tradein->avaliacao_snapshot : [];

        $cabecalhoDefault = [
            'cliente' => $cliente->razao_social ?? '',
            'numero_venda' => '',
            'aparelho_entrada' => $tradein->nome_item ?? '',
            'data' => ($tradein->avaliado_em ?: $tradein->created_at ?: now())->format('Y-m-d'),
            'imei' => $tradein->serial_number ?? '',
            'consultor' => '',
            'valor_aparelho' => $tradein->valor_avaliado ?: $tradein->valor_pretendido,
        ];

        $cabecalho = array_merge($cabecalhoDefault, Arr::get($snapshot, 'cabecalho', []));

        $pecas = [];
        for ($i = 0; $i < 5; $i++) {
            $pecas[] = [
                'descricao' => trim((string) Arr::get($snapshot, "pecas.$i.descricao", '')),
                'valor' => $this->parseMoneyNullable(Arr::get($snapshot, "pecas.$i.valor")),
            ];
        }

        $legacyResultados = [
            'aparelho_liga_corretamente' => $tradein->check_tela_ok,
            'saude_bateria' => $tradein->check_bateria_ok,
            'entrada_carregamento_funcionando' => $tradein->check_carregamento_ok,
            'botoes_funcionando' => $tradein->check_botoes_ok,
            'cameras_funcionando_manchas' => $tradein->check_camera_ok,
        ];

        $checklist = [];
        foreach ($checklistTemplate as $key => $label) {
            $resultado = strtoupper(trim((string) Arr::get($snapshot, "checklist.$key.resultado", '')));
            if (!in_array($resultado, ['SIM', 'NAO'], true)) {
                $legacy = $legacyResultados[$key] ?? null;
                if ($legacy !== null) {
                    $resultado = (int) $legacy === 1 ? 'SIM' : 'NAO';
                } else {
                    $resultado = '';
                }
            }

            $checklist[$key] = [
                'label' => $label,
                'resultado' => $resultado,
                'observacao' => trim((string) Arr::get($snapshot, "checklist.$key.observacao", '')),
            ];
        }

        $declaracoes = [
            'removeu_dados_pessoais' => strtoupper(trim((string) Arr::get($snapshot, 'declaracoes.removeu_dados_pessoais', ''))),
            'transferencia_propriedade' => strtoupper(trim((string) Arr::get($snapshot, 'declaracoes.transferencia_propriedade', ''))),
        ];
        foreach ($declaracoes as $key => $value) {
            if (!in_array($value, ['SIM', 'NAO'], true)) {
                $declaracoes[$key] = '';
            }
        }

        return [
            'cabecalho' => $cabecalho,
            'pecas' => $pecas,
            'checklist' => $checklist,
            'declaracoes' => $declaracoes,
            'observacao_geral' => trim((string) Arr::get($snapshot, 'observacao_geral', $tradein->observacao_tecnico ?? '')),
            'versao' => Arr::get($snapshot, 'versao', 'v2'),
        ];
    }

    private function buildTradeinSnapshotFromRequest(Request $request, Tradein $tradein, ?Cliente $cliente = null): array
    {
        $snapshot = $this->resolveTradeinSnapshot($tradein, $cliente);

        $cabecalhoInput = $request->input('cabecalho', []);
        $snapshot['cabecalho']['numero_venda'] = trim((string) Arr::get($cabecalhoInput, 'numero_venda', $snapshot['cabecalho']['numero_venda']));
        $snapshot['cabecalho']['consultor'] = trim((string) Arr::get($cabecalhoInput, 'consultor', $snapshot['cabecalho']['consultor']));
        $snapshot['cabecalho']['data'] = trim((string) Arr::get($cabecalhoInput, 'data', $snapshot['cabecalho']['data']));

        $valorAparelho = $this->parseMoneyNullable(Arr::get($cabecalhoInput, 'valor_aparelho'));
        if ($valorAparelho !== null) {
            $snapshot['cabecalho']['valor_aparelho'] = $valorAparelho;
        }

        $pecasInput = $request->input('pecas', []);
        $pecas = [];
        for ($i = 0; $i < 5; $i++) {
            $pecas[$i] = [
                'descricao' => trim((string) Arr::get($pecasInput, "$i.descricao", '')),
                'valor' => $this->parseMoneyNullable(Arr::get($pecasInput, "$i.valor")),
            ];
        }
        $snapshot['pecas'] = $pecas;

        $checklistInput = $request->input('checklist', []);
        foreach ($this->checklistTemplate() as $key => $label) {
            $resultado = strtoupper(trim((string) Arr::get($checklistInput, "$key.resultado", '')));
            if (!in_array($resultado, ['SIM', 'NAO'], true)) {
                $resultado = '';
            }
            $snapshot['checklist'][$key] = [
                'label' => $label,
                'resultado' => $resultado,
                'observacao' => trim((string) Arr::get($checklistInput, "$key.observacao", '')),
            ];
        }

        $declaracoesInput = $request->input('declaracoes', []);
        foreach (['removeu_dados_pessoais', 'transferencia_propriedade'] as $key) {
            $value = strtoupper(trim((string) Arr::get($declaracoesInput, $key, '')));
            $snapshot['declaracoes'][$key] = in_array($value, ['SIM', 'NAO'], true) ? $value : '';
        }

        $snapshot['observacao_geral'] = trim((string) $request->input('observacao_tecnico', $snapshot['observacao_geral'] ?? ''));
        $snapshot['atualizado_em'] = now()->toDateTimeString();
        $snapshot['versao'] = 'v2';

        return $snapshot;
    }

    private function evaluateSaveValidationErrors(Request $request): array
    {
        $errors = [];
        $checklistInput = $request->input('checklist', []);
        foreach ($this->checklistTemplate() as $key => $label) {
            $resultado = strtoupper(trim((string) Arr::get($checklistInput, "$key.resultado", '')));
            if (!in_array($resultado, ['SIM', 'NAO'], true)) {
                $errors["checklist.$key.resultado"] = ["Informe Sim/Não para: {$label}"];
            }
        }

        $valorAparelho = $this->parseMoneyNullable(Arr::get($request->input('cabecalho', []), 'valor_aparelho'));
        if ($valorAparelho === null || $valorAparelho <= 0) {
            $errors['cabecalho.valor_aparelho'] = ['Informe um valor do aparelho válido (maior que zero).'];
        }

        $valorAvaliado = $this->parseMoneyNullable($request->valor_avaliado);
        if ($valorAvaliado === null || $valorAvaliado < 0) {
            $errors['valor_avaliado'] = ['Informe um valor avaliado válido (maior ou igual a zero).'];
        }

        return [$errors, $valorAparelho, $valorAvaliado];
    }

    private function hasSavedEvaluation(Tradein $tradein): bool
    {
        return !empty($tradein->avaliacao_snapshot);
    }

    private function resolveTradeinEditData(Request $request, $id): array
    {
        $tradein = Tradein::where('empresa_id', $request->empresa_id)->findOrFail($id);
        __validaObjetoEmpresa($tradein);

        if ($tradein->status === Tradein::STATUS_SUBMITTED) {
            $tradein->status = Tradein::STATUS_IN_REVIEW;
            $tradein->assigned_to_user_id = Auth::id();
            $tradein->save();
        }

        $cliente = $tradein->cliente_id ? Cliente::find($tradein->cliente_id) : null;
        $snapshot = $this->resolveTradeinSnapshot($tradein, $cliente);
        if (!Arr::get($snapshot, 'cabecalho.numero_venda') && $request->filled('numero_venda')) {
            Arr::set($snapshot, 'cabecalho.numero_venda', trim((string) $request->numero_venda));
        }
        if (!Arr::get($snapshot, 'cabecalho.consultor') && $request->filled('consultor')) {
            Arr::set($snapshot, 'cabecalho.consultor', trim((string) $request->consultor));
        }
        $checklistTemplate = $this->checklistTemplate();
        return [$tradein, $cliente, $snapshot, $checklistTemplate];
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
        [$tradein, $cliente, $snapshot, $checklistTemplate] = $this->resolveTradeinEditData($request, $id);
        $isModal = false;
        return view('tradein.form', compact('tradein', 'cliente', 'snapshot', 'checklistTemplate', 'isModal'));
    }

    public function modalForm(Request $request, $id)
    {
        [$tradein, $cliente, $snapshot, $checklistTemplate] = $this->resolveTradeinEditData($request, $id);
        $isModal = true;
        return view('tradein.partials._form_content', compact('tradein', 'cliente', 'snapshot', 'checklistTemplate', 'isModal'));
    }

    public function update(Request $request, $id)
    {
        $tradein = Tradein::where('empresa_id', $request->empresa_id)->findOrFail($id);
        __validaObjetoEmpresa($tradein);
        $cliente = $tradein->cliente_id ? Cliente::find($tradein->cliente_id) : null;

        $concluir = (bool) $request->boolean('concluir_avaliacao');

        $rules = [
            'cabecalho.numero_venda' => 'nullable|string|max:80',
            'cabecalho.consultor' => 'nullable|string|max:120',
            'cabecalho.data' => 'nullable|date',
            'cabecalho.valor_aparelho' => 'required|string|max:50',
            'valor_avaliado' => 'required|string|max:50',
            'observacao_tecnico' => 'nullable|string',
            'pecas' => 'nullable|array',
            'pecas.*.descricao' => 'nullable|string|max:255',
            'pecas.*.valor' => 'nullable|string|max:50',
            'checklist' => 'required|array',
            'checklist.*.resultado' => 'nullable|in:SIM,NAO',
            'checklist.*.observacao' => 'nullable|string|max:500',
            'declaracoes.removeu_dados_pessoais' => 'required|in:SIM,NAO',
            'declaracoes.transferencia_propriedade' => 'required|in:SIM,NAO',
            'concluir_avaliacao' => 'accepted',
        ];
        $messages = [
            'concluir_avaliacao.accepted' => 'Marque "Concluir avaliação" para salvar a avaliação.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Dados inválidos para avaliação.',
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        [$saveErrors, $valorAparelho, $valorAvaliado] = $this->evaluateSaveValidationErrors($request);
        if (!empty($saveErrors)) {
            $msg = 'Preencha todos os campos obrigatórios para salvar a avaliação.';
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => $msg,
                    'errors' => $saveErrors,
                ], 422);
            }
            return redirect()->back()->withErrors($saveErrors)->withInput();
        }

        if ($concluir && ($valorAvaliado === null || $valorAvaliado <= 0)) {
            $msg = 'Informe um valor avaliado válido (maior que zero) para concluir a avaliação.';
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'message' => $msg,
                    'errors' => ['valor_avaliado' => [$msg]],
                ], 422);
            }
            return redirect()->back()->withErrors(['valor_avaliado' => $msg])->withInput();
        }

        $snapshot = $this->buildTradeinSnapshotFromRequest($request, $tradein, $cliente);

        Arr::set($snapshot, 'cabecalho.valor_aparelho', $valorAparelho);
        $tradein->observacao_tecnico = $snapshot['observacao_geral'] ?? null;
        $tradein->valor_avaliado = $valorAvaliado;
        $tradein->avaliacao_snapshot = $snapshot;

        $tradein->check_tela_ok = $this->resultadoToBool(Arr::get($snapshot, 'checklist.aparelho_liga_corretamente.resultado'));
        $tradein->check_bateria_ok = $this->resultadoToBool(Arr::get($snapshot, 'checklist.saude_bateria.resultado'));
        $tradein->check_carregamento_ok = $this->resultadoToBool(Arr::get($snapshot, 'checklist.entrada_carregamento_funcionando.resultado'));
        $tradein->check_botoes_ok = $this->resultadoToBool(Arr::get($snapshot, 'checklist.botoes_funcionando.resultado'));
        $tradein->check_camera_ok = $this->resultadoToBool(Arr::get($snapshot, 'checklist.cameras_funcionando_manchas.resultado'));

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

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Avaliação atualizada.',
                'tradein_id' => $tradein->id,
                'status' => $tradein->status,
                'valor_avaliado' => $tradein->valor_avaliado,
            ], 200);
        }

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
            'evaluation_saved' => $this->hasSavedEvaluation($tradein),
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

                if (!$this->hasSavedEvaluation($tradein)) {
                    return response()->json('Salve a avaliação antes de gerar documentos/PDV.', 422);
                }

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
        if (!$this->hasSavedEvaluation($tradein)) {
            abort(403, 'Salve a avaliação do trade-in antes de gerar o termo.');
        }
        $cliente = $tradein->cliente_id ? Cliente::find($tradein->cliente_id) : null;
        $snapshot = $this->resolveTradeinSnapshot($tradein, $cliente);
        $checklistTemplate = $this->checklistTemplate();

        $html = view('tradein.termo', compact('tradein', 'cliente', 'snapshot', 'checklistTemplate'));

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
