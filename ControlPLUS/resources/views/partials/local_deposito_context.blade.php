@php
use App\Models\Deposito;

$locaisOperacao = __getLocaisAtivoUsuario();
$localIdsOperacao = $locaisOperacao->pluck('id')
    ->map(function ($id) {
        return (int)$id;
    })
    ->filter()
    ->unique()
    ->values();

foreach ($localIdsOperacao as $localOperacaoId) {
    Deposito::ensureDefaultForLocalId((int)$localOperacaoId);
}

$depositosOperacao = $localIdsOperacao->isNotEmpty()
    ? Deposito::with('localizacao:id,descricao')
        ->whereIn('local_id', $localIdsOperacao->all())
        ->where('ativo', 1)
        ->orderBy('local_id')
        ->orderByDesc('padrao')
        ->orderBy('nome')
        ->get()
    : collect();

$localOperacaoSelecionado = old(
    'local_id',
    isset($item) && $item && $item->local_id
        ? (int)$item->local_id
        : ((isset($caixa) && $caixa && $caixa->local_id)
            ? (int)$caixa->local_id
            : (function_exists('__getLocalAtivo') && __getLocalAtivo() ? (int)__getLocalAtivo()->id : null))
);

$depositoOperacaoSelecionado = old(
    'deposito_id',
    isset($item) && $item && isset($item->deposito_id) && $item->deposito_id
        ? (int)$item->deposito_id
        : Deposito::resolveDefaultIdByLocalId($localOperacaoSelecionado)
);

$depositoOptionsContext = $depositosOperacao->map(function ($deposito) use ($localIdsOperacao) {
    $descricaoLocal = optional($deposito->localizacao)->descricao;
    $label = (string)$deposito->nome;

    if ($localIdsOperacao->count() > 1 && $descricaoLocal) {
        $label .= ' - ' . $descricaoLocal;
    }

    return [
        'id' => (int)$deposito->id,
        'local_id' => (int)$deposito->local_id,
        'label' => $label,
        'padrao' => (bool)$deposito->padrao,
    ];
})->values();

$depositosRenderizados = $depositosOperacao->filter(function ($deposito) use ($localOperacaoSelecionado) {
    return !$localOperacaoSelecionado || (int)$deposito->local_id === (int)$localOperacaoSelecionado;
});

if ($depositosRenderizados->isEmpty()) {
    $depositosRenderizados = $depositosOperacao;
}
@endphp

<div class="row mb-2">
    @if(__countLocalAtivo() > 1 && __escolheLocalidade())
    <div class="col-md-3">
        <label for="inp-local_id">Local</label>
        <select id="inp-local_id" required class="select2 class-required" data-toggle="select2" name="local_id">
            @foreach($locaisOperacao as $local)
            <option @if((int)$localOperacaoSelecionado === (int)$local->id) selected @endif value="{{ $local->id }}">{{ $local->descricao }}</option>
            @endforeach
        </select>
        <small class="text-muted">Contexto fiscal e operacional da unidade.</small>
    </div>
    @elseif($localOperacaoSelecionado)
    <input id="inp-local_id" type="hidden" value="{{ $localOperacaoSelecionado }}" name="local_id">
    @endif

    @if($depositoOptionsContext->isNotEmpty())
    <div class="col-md-3">
        <label for="inp-deposito_id">Depósito</label>
        <select
            id="inp-deposito_id"
            class="select2"
            data-toggle="select2"
            name="deposito_id"
            data-selected="{{ $depositoOperacaoSelecionado }}"
            data-options='@json($depositoOptionsContext)'
        >
            <option value="">Selecione</option>
            @foreach($depositosRenderizados as $deposito)
            @php
            $descricaoLocal = optional($deposito->localizacao)->descricao;
            $labelDeposito = $deposito->nome;
            if ($localIdsOperacao->count() > 1 && $descricaoLocal) {
                $labelDeposito .= ' - ' . $descricaoLocal;
            }
            @endphp
            <option
                value="{{ $deposito->id }}"
                data-local-id="{{ $deposito->local_id }}"
                @if((int)$depositoOperacaoSelecionado === (int)$deposito->id) selected @endif
            >
                {{ $labelDeposito }}
            </option>
            @endforeach
        </select>
        <small class="text-muted">Usado para o estoque fisico movimentado pela operação.</small>
    </div>
    @endif
</div>
