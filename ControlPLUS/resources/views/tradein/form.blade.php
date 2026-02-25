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
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase fs-12 mt-0">Cliente</h6>
                            <p class="mb-0">{{ $cliente->razao_social ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted text-uppercase fs-12 mt-0">Item</h6>
                            <p class="mb-0">{{ $tradein->nome_item }}</p>
                        </div>
                        <div class="col-md-4 mt-3">
                            <h6 class="text-muted text-uppercase fs-12 mt-0">Serial</h6>
                            <p class="mb-0">{{ $tradein->serial_number ?: '-' }}</p>
                        </div>
                        <div class="col-md-4 mt-3">
                            <h6 class="text-muted text-uppercase fs-12 mt-0">Valor pretendido</h6>
                            <p class="mb-0">R$ {{ __moeda($tradein->valor_pretendido) }}</p>
                        </div>
                        <div class="col-md-4 mt-3">
                            <h6 class="text-muted text-uppercase fs-12 mt-0">Status</h6>
                            <p class="mb-0">{{ $tradein->status }}</p>
                        </div>
                        <div class="col-12 mt-3">
                            <h6 class="text-muted text-uppercase fs-12 mt-0">Observacao do vendedor</h6>
                            <p class="mb-0">{{ $tradein->observacao_vendedor ?: '-' }}</p>
                        </div>
                    </div>

                    <form action="{{ route('tradein.update', $tradein->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="empresa_id" value="{{ request()->empresa_id ?? $tradein->empresa_id }}">

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="check_tela_ok" id="check_tela_ok" value="1"
                                           @checked(old('check_tela_ok', $tradein->check_tela_ok))>
                                    <label class="form-check-label" for="check_tela_ok">Tela ok</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="check_bateria_ok" id="check_bateria_ok" value="1"
                                           @checked(old('check_bateria_ok', $tradein->check_bateria_ok))>
                                    <label class="form-check-label" for="check_bateria_ok">Bateria ok</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="check_carregamento_ok" id="check_carregamento_ok" value="1"
                                           @checked(old('check_carregamento_ok', $tradein->check_carregamento_ok))>
                                    <label class="form-check-label" for="check_carregamento_ok">Carregamento ok</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="check_botoes_ok" id="check_botoes_ok" value="1"
                                           @checked(old('check_botoes_ok', $tradein->check_botoes_ok))>
                                    <label class="form-check-label" for="check_botoes_ok">Botoes ok</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="check_camera_ok" id="check_camera_ok" value="1"
                                           @checked(old('check_camera_ok', $tradein->check_camera_ok))>
                                    <label class="form-check-label" for="check_camera_ok">Camera ok</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="observacao_tecnico">Observacao tecnica</label>
                            <textarea name="observacao_tecnico" id="observacao_tecnico" rows="4" class="form-control">{{ old('observacao_tecnico', $tradein->observacao_tecnico) }}</textarea>
                            @error('observacao_tecnico')
                            <p class="text-danger mb-0">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label" for="valor_avaliado">Valor avaliado</label>
                                <input type="text" name="valor_avaliado" id="valor_avaliado" class="form-control"
                                       value="{{ old('valor_avaliado', $tradein->valor_avaliado ? __moeda($tradein->valor_avaliado) : '') }}">
                                @error('valor_avaliado')
                                <p class="text-danger mb-0">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="concluir_avaliacao" id="concluir_avaliacao" value="1"
                                           @checked(old('concluir_avaliacao', $tradein->status === \App\Models\Tradein::STATUS_COMPLETED))>
                                    <label class="form-check-label" for="concluir_avaliacao">Concluir avaliacao</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-check-line"></i> Salvar avaliacao
                            </button>
                            <a href="{{ route('tradein.index') }}" class="btn btn-light">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
