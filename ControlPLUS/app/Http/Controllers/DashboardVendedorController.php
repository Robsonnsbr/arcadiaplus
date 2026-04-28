<?php

namespace App\Http\Controllers;

use App\Models\CrmAnotacao;
use App\Models\Funcionario;
use App\Models\MetaResultado;
use App\Models\Nfe;
use App\Models\Nfce;
use App\Models\PreVenda;
use App\Support\ReportPeriodFilter;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardVendedorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:dashboard_view', ['only' => ['index']]);
    }

    public function index(Request $request)
    {
        $empresaId = $request->empresa_id;
        [$startDate, $endDate] = $this->resolvePeriodo($request);

        $funcionariosComerciais = Funcionario::where('empresa_id', $empresaId)
            ->where('status', 1)
            ->cargosComerciais()
            ->orderBy('nome')
            ->get(['id', 'nome']);

        $funcionarioId = $request->filled('funcionario_id') ? (int)$request->funcionario_id : null;
        $localId = $request->filled('local_id') ? (int)$request->local_id : null;
        $funcionariosComerciaisIds = $funcionariosComerciais->pluck('id')->all();
        $funcionarioFiltro = $funcionarioId ?: null;

        $nfeQuery = $this->baseNfeQuery($empresaId, $startDate, $endDate, $funcionarioFiltro, $funcionariosComerciaisIds, $localId);
        $nfceQuery = $this->baseNfceQuery($empresaId, $startDate, $endDate, $funcionarioFiltro, $funcionariosComerciaisIds, $localId);
        $preVendaQuery = $this->basePreVendaQuery($empresaId, $startDate, $endDate, $funcionarioFiltro, $funcionariosComerciaisIds, $localId);
        $crmQuery = $this->baseCrmQuery($empresaId, $startDate, $endDate, $funcionarioFiltro, $funcionariosComerciaisIds);
        $metaQuery = $this->baseMetaQuery($empresaId, $funcionarioFiltro, $funcionariosComerciaisIds, $localId);

        $totalNfe = (clone $nfeQuery)->sum('total');
        $totalNfce = (clone $nfceQuery)->sum('total');
        $qtdNfe = (clone $nfeQuery)->count();
        $qtdNfce = (clone $nfceQuery)->count();

        $totalVendas = (float) $totalNfe + (float) $totalNfce;
        $qtdVendas = (int) $qtdNfe + (int) $qtdNfce;
        $ticketMedio = $qtdVendas > 0 ? $totalVendas / $qtdVendas : 0;

        $totalMeta = (clone $metaQuery)->sum('valor');
        $metaAtingida = $totalMeta > 0 ? ($totalVendas / $totalMeta) * 100 : 0;

        $preVendasCriadas = (clone $preVendaQuery)->count();
        $preVendasConvertidas = (clone $preVendaQuery)
            ->whereNotNull('venda_id')
            ->where('status', 0)
            ->count();

        $crmTotal = (clone $crmQuery)->count();
        $crmConcluido = (clone $crmQuery)
            ->where('conclusao', 'Venda concluida')
            ->count();

        $funil = [
            'pre_vendas' => [
                'label' => 'Pré-vendas',
                'valor' => $preVendasCriadas,
            ],
            'vendas' => [
                'label' => 'Vendas',
                'valor' => $qtdVendas,
            ],
            'crm' => [
                'label' => 'CRM concluído',
                'valor' => $crmConcluido,
            ],
            'taxa_pre_venda' => $preVendasCriadas > 0 ? ($preVendasConvertidas / $preVendasCriadas) * 100 : 0,
            'taxa_venda_crm' => $qtdVendas > 0 ? ($crmConcluido / $qtdVendas) * 100 : 0,
            'pre_vendas_convertidas' => $preVendasConvertidas,
            'crm_total' => $crmTotal,
        ];

        $graficoDiario = $this->graficoDiario($nfeQuery, $nfceQuery, $startDate, $endDate);
        $ranking = $this->rankingVendedores($nfeQuery, $nfceQuery, $funcionariosComerciais);
        $ultimasVendas = $this->ultimasVendas($nfeQuery, $nfceQuery);
        $proximosRetornos = $this->proximosRetornos($empresaId, $startDate, $endDate, $funcionarioFiltro, $funcionariosComerciaisIds);

        return view('dashboard_vendedor.index', compact(
            'funcionariosComerciais',
            'funcionarioId',
            'localId',
            'startDate',
            'endDate',
            'totalVendas',
            'qtdVendas',
            'ticketMedio',
            'totalMeta',
            'metaAtingida',
            'preVendasCriadas',
            'preVendasConvertidas',
            'crmTotal',
            'crmConcluido',
            'funil',
            'graficoDiario',
            'ranking',
            'ultimasVendas',
            'proximosRetornos'
        ));
    }

    private function resolvePeriodo(Request $request): array
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->toDateString()
            : now()->startOfMonth()->toDateString();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->toDateString()
            : now()->endOfMonth()->toDateString();

        if (Carbon::parse($startDate)->gt(Carbon::parse($endDate))) {
            return [$endDate, $startDate];
        }

        return [$startDate, $endDate];
    }

    private function applyFuncionarioFiltro($query, ?int $funcionarioId, array $funcionariosIds)
    {
        if ($funcionarioId) {
            return $query->where('funcionario_id', $funcionarioId);
        }

        if (!empty($funcionariosIds)) {
            return $query->whereIn('funcionario_id', $funcionariosIds);
        }

        return $query->whereRaw('1 = 0');
    }

    private function baseNfeQuery(int $empresaId, string $startDate, string $endDate, ?int $funcionarioId, array $funcionariosIds, ?int $localId)
    {
        $query = Nfe::where('empresa_id', $empresaId)
            ->where('estado', '!=', 'cancelado')
            ->where('orcamento', 0);

        ReportPeriodFilter::apply(
            $query,
            ReportPeriodFilter::coalesce('data_emissao'),
            $startDate,
            $endDate
        );

        if ($localId) {
            $query->where('local_id', $localId);
        }

        return $this->applyFuncionarioFiltro($query, $funcionarioId, $funcionariosIds);
    }

    private function baseNfceQuery(int $empresaId, string $startDate, string $endDate, ?int $funcionarioId, array $funcionariosIds, ?int $localId)
    {
        $query = Nfce::where('empresa_id', $empresaId)
            ->where('estado', '!=', 'cancelado');

        ReportPeriodFilter::apply(
            $query,
            ReportPeriodFilter::coalesce('data_emissao'),
            $startDate,
            $endDate
        );

        if ($localId) {
            $query->where('local_id', $localId);
        }

        return $this->applyFuncionarioFiltro($query, $funcionarioId, $funcionariosIds);
    }

    private function basePreVendaQuery(int $empresaId, string $startDate, string $endDate, ?int $funcionarioId, array $funcionariosIds, ?int $localId)
    {
        $query = PreVenda::where('empresa_id', $empresaId);
        ReportPeriodFilter::apply($query, 'created_at', $startDate, $endDate);

        if ($localId) {
            $query->where('local_id', $localId);
        }

        return $this->applyFuncionarioFiltro($query, $funcionarioId, $funcionariosIds);
    }

    private function baseCrmQuery(int $empresaId, string $startDate, string $endDate, ?int $funcionarioId, array $funcionariosIds)
    {
        $query = CrmAnotacao::where('empresa_id', $empresaId);
        ReportPeriodFilter::apply($query, 'created_at', $startDate, $endDate);

        return $this->applyFuncionarioFiltro($query, $funcionarioId, $funcionariosIds);
    }

    private function baseMetaQuery(int $empresaId, ?int $funcionarioId, array $funcionariosIds, ?int $localId)
    {
        $query = MetaResultado::where('empresa_id', $empresaId)
            ->where('tabela', 'Vendas');

        if ($localId) {
            $query->where('local_id', $localId);
        }

        return $this->applyFuncionarioFiltro($query, $funcionarioId, $funcionariosIds);
    }

    private function graficoDiario($nfeQuery, $nfceQuery, string $startDate, string $endDate): array
    {
        $dateExpression = 'DATE(COALESCE(data_emissao, created_at))';

        $nfeMap = (clone $nfeQuery)
            ->selectRaw($dateExpression . ' as dia, SUM(total) as total')
            ->groupBy(DB::raw($dateExpression))
            ->pluck('total', 'dia');

        $nfceMap = (clone $nfceQuery)
            ->selectRaw($dateExpression . ' as dia, SUM(total) as total')
            ->groupBy(DB::raw($dateExpression))
            ->pluck('total', 'dia');

        $periodo = CarbonPeriod::create(Carbon::parse($startDate), Carbon::parse($endDate));
        $labels = [];
        $values = [];

        foreach ($periodo as $data) {
            $dia = $data->format('Y-m-d');
            $labels[] = $data->format('d/m');
            $values[] = (float) ($nfeMap[$dia] ?? 0) + (float) ($nfceMap[$dia] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function rankingVendedores($nfeQuery, $nfceQuery, $funcionariosComerciais)
    {
        $mapFuncionarios = $funcionariosComerciais->keyBy('id');
        $ranking = collect();

        $agrupar = function ($itens) use (&$ranking, $mapFuncionarios) {
            foreach ($itens as $item) {
                if (!$item->funcionario_id) {
                    continue;
                }

                if (!$ranking->has($item->funcionario_id)) {
                    $ranking->put($item->funcionario_id, [
                        'funcionario_id' => $item->funcionario_id,
                        'funcionario' => $mapFuncionarios->get($item->funcionario_id),
                        'qtd_vendas' => 0,
                        'total_vendas' => 0,
                    ]);
                }

                $registro = $ranking->get($item->funcionario_id);
                $registro['qtd_vendas'] += (int) $item->qtd_vendas;
                $registro['total_vendas'] += (float) $item->total_vendas;
                $ranking->put($item->funcionario_id, $registro);
            }
        };

        $nfeAgg = (clone $nfeQuery)
            ->select('funcionario_id', DB::raw('COUNT(*) as qtd_vendas'), DB::raw('SUM(total) as total_vendas'))
            ->groupBy('funcionario_id')
            ->get();

        $nfceAgg = (clone $nfceQuery)
            ->select('funcionario_id', DB::raw('COUNT(*) as qtd_vendas'), DB::raw('SUM(total) as total_vendas'))
            ->groupBy('funcionario_id')
            ->get();

        $agrupar($nfeAgg);
        $agrupar($nfceAgg);

        return $ranking
            ->values()
            ->sortByDesc('total_vendas')
            ->values()
            ->all();
    }

    private function ultimasVendas($nfeQuery, $nfceQuery)
    {
        $nfe = (clone $nfeQuery)
            ->with(['cliente:id,razao_social,cpf_cnpj', 'funcionario:id,nome', 'localizacao:id,descricao'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'tipo' => 'NF-e',
                    'id' => $item->id,
                    'numero' => $item->numero_sequencial ?: $item->numero,
                    'cliente' => $item->cliente ? $item->cliente->info : 'Consumidor final',
                    'funcionario' => $item->funcionario ? $item->funcionario->nome : '--',
                    'localizacao' => $item->localizacao ? $item->localizacao->descricao : '--',
                    'data' => $item->created_at,
                    'total' => (float) $item->total,
                ];
            });

        $nfce = (clone $nfceQuery)
            ->with(['cliente:id,razao_social,cpf_cnpj', 'funcionario:id,nome', 'localizacao:id,descricao'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'tipo' => 'NFC-e',
                    'id' => $item->id,
                    'numero' => $item->numero_sequencial ?: $item->numero,
                    'cliente' => $item->cliente ? $item->cliente->info : ($item->cliente_nome ?: 'Consumidor final'),
                    'funcionario' => $item->funcionario ? $item->funcionario->nome : '--',
                    'localizacao' => $item->localizacao ? $item->localizacao->descricao : '--',
                    'data' => $item->created_at,
                    'total' => (float) $item->total,
                ];
            });

        return $nfe->toBase()
            ->concat($nfce->toBase())
            ->sortByDesc('data')
            ->values()
            ->take(10)
            ->all();
    }

    private function proximosRetornos(int $empresaId, string $startDate, string $endDate, ?int $funcionarioId, array $funcionariosIds)
    {
        $query = CrmAnotacao::where('empresa_id', $empresaId)
            ->whereNotNull('data_retorno')
            ->whereDate('data_retorno', '>=', today())
            ->whereDate('data_retorno', '<=', now()->addDays(7)->toDateString())
            ->with(['cliente:id,razao_social,cpf_cnpj', 'funcionario:id,nome']);

        ReportPeriodFilter::apply($query, 'created_at', $startDate, $endDate);

        $query = $this->applyFuncionarioFiltro($query, $funcionarioId, $funcionariosIds);

        return $query
            ->orderBy('data_retorno')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }
}
