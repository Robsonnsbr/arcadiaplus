<div class="table-responsive">
    <table class="table table-striped table-centered mb-0">
        <thead class="table-dark">
            <tr>
                <th></th>
                <th>#</th>
                <th>Produto</th>
                <th>Categoria</th>
                <th>Quantidade</th>
                <th>Disponível (ATIVO)</th>
                @if(!empty($mostrarColunaStatusFiltro))
                <th>{{ $statusOperacionalLabel ?? 'Status' }}</th>
                @endif
                <th>Valor de venda</th>
                <th>Unidade</th>
                <th>Códigos de série</th>
                @if(__countLocalAtivo() > 1)
                <th>Local</th>
                @endif
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                <td><img class="img-60" src="{{ $item->produto->img }}"></td>
                <td data-label="Código">{{ $item->produto->numero_sequencial }}</td>
                <td data-label="Descrição">
                    {{ $item->descricao() }}
                </td>
                <td data-label="Categoria">{{ $item->produto->categoria ? $item->produto->categoria->nome : '' }}</td>
                <td data-label="Quantidade">
                    @if(!$item->produto->unidadeDecimal())
                    {{ number_format($item->quantidade, 0, '.', '') }}
                    @else
                    {{ number_format($item->quantidade, 3, '.', '') }}
                    @endif
                </td>
                @php
                    $casas = $item->produto->tipo_unico ? 0 : ($item->produto->unidadeDecimal() ? 3 : 0);
                    $disponivelAtivo = isset($item->disponivel_ativo_qtd) ? (float)$item->disponivel_ativo_qtd : 0;
                    $statusQtd = isset($item->status_operacional_qtd) ? (float)$item->status_operacional_qtd : 0;
                @endphp
                <td data-label="Disponível (ATIVO)">
                    {{ number_format($disponivelAtivo, $casas, '.', '') }}
                </td>
                @if(!empty($mostrarColunaStatusFiltro))
                <td data-label="{{ $statusOperacionalLabel ?? 'Status' }}">
                    {{ number_format($statusQtd, $casas, '.', '') }}
                </td>
                @endif
                <td data-label="Variação">
                    @if($item->produtoVariacao)
                    {{ __moeda($item->produtoVariacao->valor) }}
                    @else
                    {{ __moeda($item->produto->valor_unitario) }}
                    @endif
                </td>
                <td data-label="Unidade">{{ $item->produto->unidade }}</td>
                <td data-label="Códigos de série">
                    @php
                        $seriaisDisponiveis = $item->produto && $item->produto->relationLoaded('produtoUnicosDisponiveis')
                            ? $item->produto->produtoUnicosDisponiveis
                            : collect();
                    @endphp

                    @if($seriaisDisponiveis->isNotEmpty())
                        <div class="dropdown">
                            <button
                                class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                type="button"
                                data-bs-toggle="dropdown"
                                data-bs-auto-close="outside"
                                aria-expanded="false"
                            >
                                Ver códigos ({{ $seriaisDisponiveis->count() }})
                            </button>

                            <div class="dropdown-menu p-2 shadow" style="min-width: 260px; max-width: 360px;">
                                <div style="max-height: 220px; overflow-y: auto;">
                                    <ul class="list-unstyled mb-0">
                                        @foreach($seriaisDisponiveis as $serial)
                                            <li class="small py-1 border-bottom">
                                                <code>{{ $serial->codigo }}</code>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @else
                        <span class="text-muted small">Sem códigos de série</span>
                    @endif
                </td>
                @if(__countLocalAtivo() > 1)
                <td data-label="Local">{{ $item->local ? $item->local->descricao : '--' }}</td>
                @endif
                <td>
                    <form style="width: 240px;" action="{{ route('estoque.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                        @method('delete')
                        @csrf
                        @can('estoque_view')
                        @php
                            $acaoDistribuicao = $item->produto->tipo_unico ? 'Gerenciar unidades' : 'Mover quantidade / status';
                        @endphp
                        <button
                            type="button"
                            class="btn btn-info btn-sm btn-distribuicao"
                            data-estoque-id="{{ $item->id }}"
                            title="{{ $acaoDistribuicao }}"
                        >
                            <i class="ri-list-check-2 me-1"></i>{{ $acaoDistribuicao }}
                        </button>
                        @endcan
                        @can('estoque_edit')
                        <a title="Editar estoque" href="{{ route('estoque.edit', [$item->id]) }}" class="btn btn-dark btn-sm">
                            <i class="ri-pencil-fill"></i>
                        </a>
                        @endcan
                        @can('produtos_edit')
                        <a title="Editar produto" href="{{ route('produtos.edit', [$item->produto_id]) }}" class="btn btn-warning btn-sm">
                            <i class="ri-pencil-fill"></i>
                        </a>
                        @endcan

                        @can('estoque_delete')
                        <button type="button" class="btn btn-delete btn-sm btn-danger">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                        @endcan

                    </form>

                </td>
            </tr>
            @empty
            <tr>
                @php
                    $colspanBase = __countLocalAtivo() > 1 ? 11 : 10;
                    $colspan = $colspanBase + (!empty($mostrarColunaStatusFiltro) ? 1 : 0);
                @endphp
                <td colspan="{{ $colspan }}" class="text-center">Nada encontrado</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
