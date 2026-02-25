@extends('layouts.app', ['title' => 'Trade-in'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="card-title mb-1">Trade-in</h4>
                        <small class="text-muted">#{{ $tradein->id }} - {{ $tradein->nome_item }}</small>
                    </div>
                    <div class="d-flex gap-2 mt-2 mt-md-0">
                        <a href="{{ route('tradein.index') }}" class="btn btn-light btn-sm">Voltar</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <h6 class="text-muted text-uppercase fs-12 mt-0">Cliente</h6>
                            <p class="mb-0">{{ $tradein->cliente_id }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted text-uppercase fs-12 mt-0">Status</h6>
                            <p class="mb-0">{{ $tradein->status }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted text-uppercase fs-12 mt-0">Valor pretendido</h6>
                            <p class="mb-0">{{ $tradein->valor_pretendido ? 'R$ ' . __moeda($tradein->valor_pretendido) : '--' }}</p>
                        </div>
                    </div>

                    @if($tradein->status === \App\Models\Tradein::STATUS_SUBMITTED)
                        <form method="post" action="{{ route('tradein.start', $tradein->id) }}">
                            @csrf
                            <input type="hidden" name="empresa_id" value="{{ request()->empresa_id ?? $tradein->empresa_id }}">
                            <button type="submit" class="btn btn-primary">Iniciar avaliação</button>
                        </form>
                    @else
                        <form method="post" action="{{ route('tradein.complete', $tradein->id) }}">
                            @csrf
                            <input type="hidden" name="empresa_id" value="{{ request()->empresa_id ?? $tradein->empresa_id }}">

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="check_tela_ok" name="check_tela_ok" value="1" {{ old('check_tela_ok', $tradein->check_tela_ok) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="check_tela_ok">Tela ok</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="check_bateria_ok" name="check_bateria_ok" value="1" {{ old('check_bateria_ok', $tradein->check_bateria_ok) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="check_bateria_ok">Bateria ok</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="check_carregamento_ok" name="check_carregamento_ok" value="1" {{ old('check_carregamento_ok', $tradein->check_carregamento_ok) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="check_carregamento_ok">Carregamento ok</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="check_botoes_ok" name="check_botoes_ok" value="1" {{ old('check_botoes_ok', $tradein->check_botoes_ok) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="check_botoes_ok">Botões ok</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="check_camera_ok" name="check_camera_ok" value="1" {{ old('check_camera_ok', $tradein->check_camera_ok) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="check_camera_ok">Câmera ok</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="form-label" for="observacao_tecnico">Observações técnicas</label>
                                <textarea name="observacao_tecnico" id="observacao_tecnico" rows="3" class="form-control">{{ old('observacao_tecnico', $tradein->observacao_tecnico) }}</textarea>
                            </div>

                            <div class="mt-3">
                                <label class="form-label" for="valor_avaliado">Valor avaliado</label>
                                <input type="text" name="valor_avaliado" id="valor_avaliado" class="form-control moeda" value="{{ old('valor_avaliado', $tradein->valor_avaliado) }}">
                                @error('valor_avaliado')
                                    <p class="text-danger mb-0">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-success">Concluir avaliação</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
