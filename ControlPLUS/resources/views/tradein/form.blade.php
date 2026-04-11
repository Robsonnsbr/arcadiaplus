@extends('layouts.app', ['title' => 'Trade-in'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="card-title mb-1">Avaliacao Trade-in</h4>
                        <small class="text-muted">#{{ $tradein->id }}</small>
                    </div>
                    <div class="d-flex gap-2 mt-2 mt-md-0">
                        <a href="{{ route('tradein.index', ['empresa_id' => request()->empresa_id]) }}" class="btn btn-light btn-sm">
                            <i class="ri-arrow-left-line"></i> Voltar
                        </a>
                        @can('tradein_delete')
                        <form method="POST" action="{{ route('tradein.destroy', ['id' => $tradein->id, 'empresa_id' => request()->empresa_id]) }}"
                              onsubmit="return confirm('Tem certeza que deseja excluir este trade-in? Esta ação não pode ser desfeita.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="ri-delete-bin-line"></i> Excluir
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    @include('tradein.partials._form_content', ['tradein' => $tradein, 'cliente' => $cliente, 'snapshot' => $snapshot, 'checklistTemplate' => $checklistTemplate, 'isModal' => false])
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="/js/tradein_checklist_tecnico.js"></script>
@endsection
