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
                        @can('localizacao_create')
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-cadastrar-local">
                                <i class="ri-map-pin-add-line"></i>
                                Cadastrar Local
                            </button>
                        @endcan
                    </div>
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
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

@can('localizacao_create')
<div class="modal fade" id="modal-cadastrar-local" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cadastrar Local</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form method="post" action="{{ route('estoque.localizacao.store') }}">
                @csrf
                <div class="modal-body">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

@section('js')
@if($errors->has('descricao') && auth()->user()->can('localizacao_create'))
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('modal-cadastrar-local');
        if (modalEl && window.bootstrap) {
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });
</script>
@endif
@endsection
