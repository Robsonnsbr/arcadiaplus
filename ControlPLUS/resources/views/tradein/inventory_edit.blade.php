@extends('layouts.app', ['title' => 'Editar Item de Inventário'])

@section('content')
<div class="card mt-1">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Editar Item de Inventário Trade-in #{{ $item->id }}</h4>
        <a href="{{ route('tradein.inventory.index', ['empresa_id' => request()->empresa_id]) }}" class="btn btn-danger btn-sm px-3">
            <i class="ri-arrow-left-double-fill"></i> Voltar
        </a>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('tradein.inventory.update', ['id' => $item->id, 'empresa_id' => request()->empresa_id]) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Produto do catálogo</label>
                    <select class="form-select" name="produto_id" id="inv-produto-select" style="width:100%">
                        @if($produto)
                            <option value="{{ $produto->id }}" selected>{{ $produto->nome }}</option>
                        @endif
                    </select>
                    <small class="text-muted">Busque o produto no catálogo (opcional).</small>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Serial / IMEI</label>
                    <input type="text" class="form-control" name="serial" value="{{ old('serial', $item->serial) }}" placeholder="Serial ou IMEI">
                </div>

                <div class="col-md-8">
                    <label class="form-label">Descrição do item</label>
                    <input type="text" class="form-control" name="descricao_item" value="{{ old('descricao_item', $item->descricao_item) }}" placeholder="Descrição">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="{{ \App\Models\TradeinInventoryItem::STATUS_PENDING_TRANSFER }}" @selected(old('status', $item->status) === \App\Models\TradeinInventoryItem::STATUS_PENDING_TRANSFER)>Aguardando transferência</option>
                        <option value="{{ \App\Models\TradeinInventoryItem::STATUS_TRANSFERRED }}" @selected(old('status', $item->status) === \App\Models\TradeinInventoryItem::STATUS_TRANSFERRED)>Transferido</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Observação técnica</label>
                    <textarea class="form-control" name="observacao_tecnica" rows="3">{{ old('observacao_tecnica', $item->observacao_tecnica) }}</textarea>
                </div>
            </div>

            <div class="mt-4 text-end">
                <a href="{{ route('tradein.inventory.index', ['empresa_id' => request()->empresa_id]) }}" class="btn btn-light me-2">Cancelar</a>
                <button type="submit" class="btn btn-success px-5">Salvar alterações</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
$(function() {
    $("#inv-produto-select").select2({
        minimumInputLength: 2,
        language: "pt-BR",
        placeholder: "Buscar produto...",
        allowClear: true,
        width: "100%",
        ajax: {
            cache: false,
            url: path_url + "api/produtos",
            dataType: "json",
            data: function (params) {
                return {
                    pesquisa: params.term,
                    empresa_id: $("#empresa_id").val(),
                    usuario_id: $("#usuario_id").val(),
                };
            },
            processResults: function (response) {
                var results = [];
                $.each(response, function (i, v) {
                    results.push({ id: v.id, text: v.nome || v.text || String(v.id) });
                });
                return { results: results };
            },
        },
    });
});
</script>
@endsection
