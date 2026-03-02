@extends('layouts.app', ['title' => 'Estoque'])
@section('css')

<style type="text/css">
    .img-wrapper {
        height: 180px;
        overflow: hidden;
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
        background-color: #f8f9fa;
    }
    .produto-img {
        height: 100%;
        width: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .produto-card {
        border-radius: 1rem;
        transition: all 0.3s ease;
        background-color: #fff;
    }
    .produto-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
    }
    .produto-card:hover .produto-img {
        transform: scale(1.05);
    }
</style>
@endsection
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 col-12 mt-1">
                        @can('estoque_create')
                            <a href="{{ route('estoque.create') }}" class="btn btn-success">
                                <i class="ri-add-circle-fill"></i>
                                Adicionar estoque
                            </a>
                        @endcan
                    </div>
                    <div class="col-md-10 col-12 mt-1"  style="text-align: right;">
                        @can('estoque_create')
                            <a href="{{ route('estoque.retirada') }}" class="btn btn-light">
                                <i class="ri-inbox-archive-fill"></i>
                                Retirada de Estoque
                            </a>
                            <a href="{{ route('apontamento.create') }}" class="btn btn-info">
                                <i class="ri-settings-3-line"></i>
                                Apontamento de Produção
                            </a>
                        @endcan
                        @canany(['localizacao_create', 'localizacao_delete'])
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-cadastrar-local">
                                <i class="ri-map-pin-add-line"></i>
                                Cadastrar Local
                            </button>
                        @endcanany
                        @can('estoque_edit')
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal-cadastrar-status">
                                <i class="ri-price-tag-3-line"></i>
                                Cadastrar Status
                            </button>
                        @endcan
                    </div>
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    @php
                        $statusOperacionalOptions = $statusOperacionalOptions ?? ['TODOS' => 'Todos'];
                        $statusOperacionalSelecionado = $statusOperacionalSelecionado ?? 'TODOS';
                    @endphp
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::text('produto', 'Pesquisar por produto')
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('categoria_id', 'Categoria', ['' => 'Selecione'] + $categorias->pluck('nome', 'id')->all())
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        @if(__countLocalAtivo() > 1)
                        <div class="col-md-2">
                            {!!Form::select('local_id', 'Local', ['' => 'Selecione'] + __getLocaisAtivoUsuario()->pluck('descricao', 'id')->all())
                            ->attrs(['class' => 'select2'])
                            !!}
                        </div>
                        @endif
                        <div class="col-md-3">
                            <label class="form-label">Status operacional</label>
                            <select name="status_operacional" class="form-select">
                                @foreach($statusOperacionalOptions as $value => $label)
                                    <option value="{{ $value }}" {{ (string)$statusOperacionalSelecionado === (string)$value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('estoque.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">

                    @if($tipoExibe == 'tabela')
                    @include('estoque.partials.tabela')
                    @else
                    @include('estoque.partials.card')
                    @endif
                </div>
                <br>
                {!! $data->appends(request()->all())->links() !!}

            </div>
        </div>
    </div>
</div>

@canany(['localizacao_create', 'localizacao_delete'])
<div class="modal fade" id="modal-cadastrar-local" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cadastrar Local</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                @can('localizacao_create')
                    <form id="form-cadastrar-local-estoque" method="post" action="{{ route('estoque.localizacao.store') }}">
                        @csrf
                        <label for="descricao_local_estoque" class="form-label">Nome do local</label>
                        <input
                            type="text"
                            id="descricao_local_estoque"
                            name="descricao"
                            class="form-control @error('descricao') is-invalid @enderror"
                            value="{{ old('descricao') }}"
                            maxlength="150"
                            required
                        >
                        @error('descricao')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </form>
                @endcan

                <div class="table-responsive mt-3">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Local</th>
                                <th>Tipo</th>
                                <th class="text-end">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($locaisCadastros ?? []) as $localItem)
                                <tr>
                                    <td>{{ $localItem['descricao'] }}</td>
                                    <td>
                                        @if(!empty($localItem['is_system']))
                                            <span class="badge bg-secondary">Base</span>
                                        @else
                                            <span class="badge bg-info">Custom</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @can('localizacao_delete')
                                            @if(!empty($localItem['can_delete']))
                                                <form method="post" action="{{ route('localizacao.destroy', $localItem['id']) }}" class="d-inline">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Excluir local">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </form>
                                            @elseif(!empty($localItem['in_use']))
                                                <span class="text-muted small">Em uso</span>
                                            @else
                                                <span class="text-muted small">Protegido</span>
                                            @endif
                                        @else
                                            <span class="text-muted small">Sem permissão</span>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Sem locais cadastrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                @can('localizacao_create')
                <button type="submit" form="form-cadastrar-local-estoque" class="btn btn-primary">Salvar</button>
                @endcan
            </div>
        </div>
    </div>
</div>
@endcanany

@can('estoque_edit')
<div class="modal fade" id="modal-cadastrar-status" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cadastrar Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form id="form-cadastrar-status" method="post" action="{{ route('estoque.status.store') }}">
                    @csrf
                    <label for="nome_status_estoque" class="form-label">Nome do status</label>
                    <input
                        type="text"
                        id="nome_status_estoque"
                        name="nome_status"
                        class="form-control @error('nome_status') is-invalid @enderror"
                        value="{{ old('nome_status') }}"
                        maxlength="80"
                        required
                    >
                    @error('nome_status')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </form>

                <div class="table-responsive mt-3">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Status</th>
                                <th>Tipo</th>
                                <th class="text-end">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($statusCadastros ?? []) as $statusItem)
                                <tr>
                                    <td><code>{{ $statusItem['status_key'] }}</code></td>
                                    <td>
                                        @if(!empty($statusItem['is_system']))
                                            <span class="badge bg-secondary">Base</span>
                                        @else
                                            <span class="badge bg-info">Custom</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(!empty($statusItem['can_delete']))
                                            <form method="post" action="{{ route('estoque.status.destroy', $statusItem['id']) }}" class="d-inline">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Excluir status">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form>
                                        @elseif(!empty($statusItem['in_use']))
                                            <span class="text-muted small">Em uso</span>
                                        @else
                                            <span class="text-muted small">Protegido</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Sem status cadastrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="form-cadastrar-status" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>
@endcan

@can('estoque_view')
<div class="modal fade" id="modal-distribuicao-estoque" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Distribuição de estoque</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div id="distribuicao-feedback" class="alert alert-danger d-none"></div>

                <div class="mb-3">
                    <h6 class="mb-0" id="distribuicao-produto-nome">--</h6>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>Local</th>
                                <th>Status</th>
                                <th>Quantidade</th>
                            </tr>
                        </thead>
                        <tbody id="distribuicao-linhas">
                            <tr>
                                <td colspan="3" class="text-center text-muted">Selecione um item para detalhar.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div id="distribuicao-seriais-wrapper" class="d-none mt-3">
                    <label class="form-label">Unidades serializadas disponíveis</label>
                    <div class="row g-2 align-items-end mb-2">
                        <div class="col-md-4">
                            <label class="form-label mb-1">Filtrar local</label>
                            <select class="form-select form-select-sm" id="dist-seriais-local-filtro">
                                <option value="">Todos</option>
                            </select>
                        </div>
                        <div class="col-md-8 text-md-end">
                            <div class="d-inline-flex align-items-center gap-1 me-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="dist-seriais-prev">Anterior</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="dist-seriais-next">Próxima</button>
                            </div>
                            <small class="text-muted" id="distribuicao-seriais-meta">--</small>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Local</th>
                                    <th>Status</th>
                                    @can('estoque_edit')
                                    <th class="text-end">Ação</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody id="distribuicao-seriais-linhas"></tbody>
                        </table>
                    </div>
                </div>

                <div id="distribuicao-resumo-nao-serial" class="d-none mt-3">
                    <div class="alert alert-light border mb-0">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <strong>ATIVO disponível:</strong>
                                <span id="dist-resumo-ativo">0</span>
                            </div>
                            <div class="col-md-8">
                                <strong>Reservas não-ATIVO:</strong>
                                <span id="dist-resumo-reservas">Sem reservas</span>
                            </div>
                        </div>
                    </div>
                </div>

                @can('estoque_edit')
                <hr>
                <form id="form-distribuicao-estoque">
                    @csrf
                    <input type="hidden" name="modo" id="distribuicao-modo">

                    <div id="distribuicao-form-quantidade" class="row g-2">
                        <div class="col-md-2">
                            <label class="form-label">Local origem</label>
                            <select class="form-select" name="local_origem_id" id="dist-local-origem"></select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status origem</label>
                            <select class="form-select" name="status_origem" id="dist-status-origem"></select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Quantidade</label>
                            <input type="number" step="0.0001" min="0.0001" class="form-control" name="quantidade" id="dist-quantidade" value="1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Local destino</label>
                            <select class="form-select" name="local_destino_id" id="dist-local-destino"></select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status destino</label>
                            <select class="form-select" name="status_destino" id="dist-status-destino"></select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Outro status (opcional)</label>
                            <input type="text" class="form-control" name="status_key" id="dist-status-custom" maxlength="60" placeholder="Ex: EM_TESTE">
                        </div>
                    </div>

                    <div id="distribuicao-form-serial" class="row g-2 d-none">
                        <div class="col-md-4">
                            <label class="form-label">Código único</label>
                            <select class="form-select" name="produto_unico_id" id="dist-serial-id"></select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Local destino</label>
                            <select class="form-select" name="local_destino_id" id="dist-serial-local-destino"></select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status destino</label>
                            <select class="form-select" name="status_destino" id="dist-serial-status-destino"></select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Outro status (opcional)</label>
                            <input type="text" class="form-control" name="status_key" id="dist-serial-status-custom" maxlength="60" placeholder="Ex: EM_TESTE">
                        </div>
                    </div>
                </form>
                @endcan
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                @can('estoque_edit')
                <button type="button" class="btn btn-primary" id="btn-distribuicao-salvar">Aplicar</button>
                @endcan
            </div>
        </div>
    </div>
</div>
@endcan
@endsection

@section('js')
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        const shouldOpenLocalModal = @json($errors->has('descricao') && auth()->user()->can('localizacao_create'));
        if (shouldOpenLocalModal) {
            const modalEl = document.getElementById('modal-cadastrar-local');
            if (modalEl && window.bootstrap) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }

        const shouldOpenStatusModal = @json($errors->has('nome_status') && auth()->user()->can('estoque_edit'));
        if (shouldOpenStatusModal) {
            const modalEl = document.getElementById('modal-cadastrar-status');
            if (modalEl && window.bootstrap) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        }
    });
</script>

@can('estoque_view')
<script type="text/javascript">
    $(function () {
        const distribuicaoModalEl = document.getElementById('modal-distribuicao-estoque');
        if (!distribuicaoModalEl || !window.bootstrap) {
            return;
        }

        const canEdit = @json(auth()->user()->can('estoque_edit'));
        const modalDistribuicao = new bootstrap.Modal(distribuicaoModalEl);
        const distribuicaoUrlTemplate = @json(route('estoque.distribuicao', ['id' => '__ID__']));
        const distribuicaoSeriaisUrlTemplate = @json(route('estoque.distribuicao.seriais', ['id' => '__ID__']));
        const movimentarUrlTemplate = @json(route('estoque.distribuicao.movimentar', ['id' => '__ID__']));

        const $feedback = $('#distribuicao-feedback');
        const $produtoNome = $('#distribuicao-produto-nome');
        const $linhas = $('#distribuicao-linhas');
        const $seriaisWrapper = $('#distribuicao-seriais-wrapper');
        const $seriaisLinhas = $('#distribuicao-seriais-linhas');
        const $seriaisMeta = $('#distribuicao-seriais-meta');
        const $seriaisFiltroLocal = $('#dist-seriais-local-filtro');
        const $seriaisPrev = $('#dist-seriais-prev');
        const $seriaisNext = $('#dist-seriais-next');
        const $resumoNaoSerial = $('#distribuicao-resumo-nao-serial');
        const $resumoAtivo = $('#dist-resumo-ativo');
        const $resumoReservas = $('#dist-resumo-reservas');
        const $form = $('#form-distribuicao-estoque');
        const $btnSalvar = $('#btn-distribuicao-salvar');
        const $modo = $('#distribuicao-modo');

        const $formQuantidade = $('#distribuicao-form-quantidade');
        const $formSerial = $('#distribuicao-form-serial');

        const $localOrigem = $('#dist-local-origem');
        const $localDestino = $('#dist-local-destino');
        const $statusOrigem = $('#dist-status-origem');
        const $statusDestino = $('#dist-status-destino');
        const $statusCustom = $('#dist-status-custom');
        const $quantidade = $('#dist-quantidade');

        const $serialId = $('#dist-serial-id');
        const $serialLocalDestino = $('#dist-serial-local-destino');
        const $serialStatusDestino = $('#dist-serial-status-destino');
        const $serialStatusCustom = $('#dist-serial-status-custom');

        const state = {
            estoqueId: null,
            payload: null,
            seriais: [],
            seriaisMeta: null,
            seriaisPage: 1
        };

        function escapeHtml(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function formatQuantidade(v) {
            const n = Number(v || 0);
            return n.toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 4 });
        }

        function resetFeedback() {
            $feedback.addClass('d-none').text('');
        }

        function showFeedback(msg) {
            $feedback.removeClass('d-none').text(msg || 'Erro ao carregar distribuição.');
        }

        function getColspanSeriais() {
            return canEdit ? 4 : 3;
        }

        function renderLocaisOptions($select, locais, selected) {
            $select.empty();
            locais.forEach(function (local) {
                const option = $('<option>', {
                    value: local.id,
                    text: local.descricao,
                    selected: String(selected) === String(local.id)
                });
                $select.append(option);
            });
        }

        function renderStatusOptions($select, statuses, selected) {
            $select.empty();
            statuses.forEach(function (status) {
                const option = $('<option>', {
                    value: status.value,
                    text: status.label,
                    selected: String(selected) === String(status.value)
                });
                $select.append(option);
            });
        }

        function renderFiltroLocalSeriais(locais) {
            $seriaisFiltroLocal.empty();
            $seriaisFiltroLocal.append($('<option>', { value: '', text: 'Todos' }));
            (locais || []).forEach(function (local) {
                $seriaisFiltroLocal.append($('<option>', {
                    value: local.id,
                    text: local.descricao
                }));
            });
        }

        function renderDistribuicao(payload) {
            $linhas.empty();
            if (!payload.distribuicao || !payload.distribuicao.length) {
                $linhas.append('<tr><td colspan="3" class="text-center text-muted">Sem distribuição cadastrada.</td></tr>');
            } else {
                payload.distribuicao.forEach(function (linha) {
                    (linha.statuses || []).forEach(function (statusRow) {
                        $linhas.append(
                            `<tr>
                                <td>${linha.local_nome}</td>
                                <td>${statusRow.label}</td>
                                <td>${formatQuantidade(statusRow.quantidade)}</td>
                            </tr>`
                        );
                    });
                });
            }
        }

        function renderResumoNaoSerial(payload) {
            if (!payload.item || payload.item.tipo_unico) {
                $resumoNaoSerial.addClass('d-none');
                return;
            }

            const localSelecionado = String($localOrigem.val() || '');
            const linha = (payload.distribuicao || []).find(function (item) {
                return String(item.local_id) === localSelecionado;
            }) || null;

            if (!linha) {
                $resumoAtivo.text('0');
                $resumoReservas.text('Sem reservas');
                $resumoNaoSerial.removeClass('d-none');
                return;
            }

            const reservas = (linha.statuses || [])
                .filter(function (statusItem) {
                    return statusItem.status !== 'ATIVO' && Number(statusItem.quantidade || 0) > 0;
                })
                .map(function (statusItem) {
                    return `${statusItem.label}: ${formatQuantidade(statusItem.quantidade)}`;
                });

            $resumoAtivo.text(formatQuantidade(linha.ativo_disponivel || 0));
            $resumoReservas.text(reservas.length ? reservas.join(' | ') : 'Sem reservas');
            $resumoNaoSerial.removeClass('d-none');
        }

        function preencherSelectSeriais(seriais) {
            $serialId.empty();
            if (!seriais.length) {
                $serialId.append($('<option>', { value: '', text: 'Sem códigos disponíveis' }));
                return;
            }

            seriais.forEach(function (serial) {
                const option = $('<option>', {
                    value: serial.produto_unico_id,
                    text: `${serial.codigo} | ${serial.local_nome || '--'} | ${serial.status_label || serial.status}`
                });
                $serialId.append(option);
            });
            $serialId.val(String(seriais[0].produto_unico_id));
        }

        function renderSeriais() {
            if (!state.payload || !state.payload.item || !state.payload.item.tipo_unico) {
                $seriaisWrapper.addClass('d-none');
                return;
            }

            $seriaisWrapper.removeClass('d-none');
            $seriaisLinhas.empty();

            const seriais = state.seriais || [];
            if (!seriais.length) {
                $seriaisLinhas.append(`<tr><td colspan="${getColspanSeriais()}" class="text-center text-muted">Sem unidades serializadas disponíveis.</td></tr>`);
                return;
            }

            seriais.forEach(function (serial) {
                const actionHtml = canEdit
                    ? `<td class="text-end"><button type="button" class="btn btn-outline-primary btn-sm btn-distribuicao-serial-alterar" data-serial-id="${serial.produto_unico_id}">Alterar</button></td>`
                    : '';
                $seriaisLinhas.append(
                    `<tr>
                        <td><code>${escapeHtml(serial.codigo)}</code></td>
                        <td>${escapeHtml(serial.local_nome || '--')}</td>
                        <td>${escapeHtml(serial.status_label || serial.status)}</td>
                        ${actionHtml}
                    </tr>`
                );
            });
        }

        function renderMetaSeriais(meta) {
            if (!meta) {
                $seriaisMeta.text('--');
                $seriaisPrev.prop('disabled', true);
                $seriaisNext.prop('disabled', true);
                return;
            }
            $seriaisMeta.text(`Página ${meta.current_page} de ${meta.last_page} • ${meta.total} unidade(s)`);
            $seriaisPrev.prop('disabled', Number(meta.current_page) <= 1);
            $seriaisNext.prop('disabled', Number(meta.current_page) >= Number(meta.last_page));
        }

        function selecionarSerialNoForm(serial) {
            if (!serial || !$form.length) {
                return;
            }
            const localDestino = serial.local_id || ($serialLocalDestino.find('option:first').val() || '');
            $serialId.val(String(serial.produto_unico_id));
            $serialLocalDestino.val(String(localDestino));
            $serialStatusDestino.val(String(serial.status || 'ATIVO'));
            $serialStatusCustom.val('');
        }

        function carregarSeriais(page = 1) {
            if (!state.payload || !state.payload.item || !state.payload.item.tipo_unico || !state.estoqueId) {
                state.seriais = [];
                state.seriaisMeta = null;
                renderSeriais();
                renderMetaSeriais(null);
                return;
            }

            const query = {
                page: page,
                per_page: 50
            };
            if ($seriaisFiltroLocal.length && $seriaisFiltroLocal.val()) {
                query.local_id = $seriaisFiltroLocal.val();
            }

            $.get(distribuicaoSeriaisUrlTemplate.replace('__ID__', state.estoqueId), query)
                .done(function (res) {
                    state.seriais = res.seriais || [];
                    state.seriaisMeta = res.meta || null;
                    state.seriaisPage = state.seriaisMeta ? Number(state.seriaisMeta.current_page || 1) : 1;
                    preencherSelectSeriais(state.seriais);
                    renderSeriais();
                    renderMetaSeriais(state.seriaisMeta);
                    const selected = state.seriais.length ? state.seriais[0] : null;
                    selecionarSerialNoForm(selected);
                })
                .fail(function (err) {
                    state.seriais = [];
                    state.seriaisMeta = null;
                    renderSeriais();
                    renderMetaSeriais(null);
                    const msg = (err && err.responseJSON && err.responseJSON.message)
                        ? err.responseJSON.message
                        : 'Não foi possível carregar os códigos serializados.';
                    showFeedback(msg);
                });
        }

        function toggleFormByTipo(payload) {
            if (!$form.length) {
                return;
            }

            const isSerial = !!(payload.item && payload.item.tipo_unico);
            if (isSerial) {
                $modo.val('serial');
                $formQuantidade.addClass('d-none').find('input,select').prop('disabled', true);
                $formSerial.removeClass('d-none').find('input,select').prop('disabled', false);
            } else {
                $modo.val('quantidade');
                $formSerial.addClass('d-none').find('input,select').prop('disabled', true);
                $formQuantidade.removeClass('d-none').find('input,select').prop('disabled', false);
            }
        }

        function preencherForm(payload) {
            if (!$form.length) {
                return;
            }

            const locais = payload.locais || [];
            const statuses = payload.status_options || [];
            const primeiraLinha = (payload.distribuicao && payload.distribuicao.length) ? payload.distribuicao[0] : null;
            const localPadrao = primeiraLinha ? primeiraLinha.local_id : (locais[0] ? locais[0].id : null);

            renderLocaisOptions($localOrigem, locais, localPadrao);
            renderLocaisOptions($localDestino, locais, localPadrao);
            renderLocaisOptions($serialLocalDestino, locais, localPadrao);

            renderStatusOptions($statusOrigem, statuses, 'ATIVO');
            renderStatusOptions($statusDestino, statuses, 'ASSISTENCIA');
            renderStatusOptions($serialStatusDestino, statuses, 'ASSISTENCIA');

            $quantidade.val('1');
            $statusCustom.val('');
            $serialStatusCustom.val('');
            toggleFormByTipo(payload);
            renderFiltroLocalSeriais(locais);
            renderResumoNaoSerial(payload);
        }

        function loadDistribuicao(estoqueId) {
            resetFeedback();
            state.estoqueId = estoqueId;

            $produtoNome.text('Carregando...');
            $linhas.html('<tr><td colspan="3" class="text-center text-muted">Carregando...</td></tr>');
            $seriaisWrapper.addClass('d-none');
            $resumoNaoSerial.addClass('d-none');
            state.seriais = [];
            state.seriaisMeta = null;
            state.seriaisPage = 1;
            renderMetaSeriais(null);

            $.get(distribuicaoUrlTemplate.replace('__ID__', estoqueId))
                .done(function (res) {
                    state.payload = res;
                    $produtoNome.text(res.item.produto_nome || '--');
                    renderDistribuicao(res);
                    preencherForm(res);
                    if (res.item && res.item.tipo_unico) {
                        carregarSeriais(1);
                    } else {
                        renderSeriais();
                    }
                })
                .fail(function (err) {
                    const msg = (err && err.responseJSON && err.responseJSON.message)
                        ? err.responseJSON.message
                        : 'Não foi possível carregar a distribuição.';
                    showFeedback(msg);
                    $produtoNome.text('--');
                    $linhas.html('<tr><td colspan="3" class="text-center text-muted">Falha ao carregar dados.</td></tr>');
                });
        }

        $(document).on('click', '.btn-distribuicao', function () {
            const estoqueId = $(this).data('estoque-id');
            if (!estoqueId) {
                return;
            }
            modalDistribuicao.show();
            loadDistribuicao(estoqueId);
        });

        $seriaisFiltroLocal.on('change', function () {
            carregarSeriais(1);
        });
        $seriaisPrev.on('click', function () {
            if (!state.seriaisMeta) {
                return;
            }
            const page = Number(state.seriaisMeta.current_page || 1);
            if (page > 1) {
                carregarSeriais(page - 1);
            }
        });
        $seriaisNext.on('click', function () {
            if (!state.seriaisMeta) {
                return;
            }
            const page = Number(state.seriaisMeta.current_page || 1);
            const lastPage = Number(state.seriaisMeta.last_page || 1);
            if (page < lastPage) {
                carregarSeriais(page + 1);
            }
        });

        $(document).on('click', '.btn-distribuicao-serial-alterar', function () {
            if (!$form.length) {
                return;
            }
            const serialId = $(this).data('serial-id');
            const serial = (state.seriais || []).find(function (item) {
                return String(item.produto_unico_id) === String(serialId);
            });
            selecionarSerialNoForm(serial || null);
        });

        $serialId.on('change', function () {
            if (!state.seriais || !state.seriais.length) {
                return;
            }

            const selecionado = state.seriais.find(function (s) {
                return String(s.produto_unico_id) === String($serialId.val());
            });
            selecionarSerialNoForm(selecionado || null);
        });

        $localOrigem.on('change', function () {
            if (!state.payload) {
                return;
            }
            renderResumoNaoSerial(state.payload);
        });

        $btnSalvar.on('click', function () {
            if (!state.estoqueId || !$form.length) {
                return;
            }

            resetFeedback();
            $btnSalvar.prop('disabled', true);

            const formData = $form.serialize();

            $.ajax({
                url: movimentarUrlTemplate.replace('__ID__', state.estoqueId),
                method: 'POST',
                data: formData,
            })
            .done(function (res) {
                if (typeof swal === 'function') {
                    swal('Sucesso', res.message || 'Distribuição atualizada com sucesso.', 'success');
                }
                loadDistribuicao(state.estoqueId);
            })
            .fail(function (err) {
                let msg = (err && err.responseJSON && err.responseJSON.message)
                    ? err.responseJSON.message
                    : 'Não foi possível atualizar a distribuição.';
                if (err && err.responseJSON && err.responseJSON.errors) {
                    const firstKey = Object.keys(err.responseJSON.errors)[0];
                    if (firstKey && err.responseJSON.errors[firstKey] && err.responseJSON.errors[firstKey].length) {
                        msg = err.responseJSON.errors[firstKey][0];
                    }
                }
                showFeedback(msg);
            })
            .always(function () {
                $btnSalvar.prop('disabled', false);
            });
        });
    });
</script>
@endcan
@endsection
