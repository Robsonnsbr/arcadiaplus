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
                        <a href="{{ route('tradein.index') }}" class="btn btn-light btn-sm">
                            <i class="ri-arrow-left-line"></i> Voltar
                        </a>
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
