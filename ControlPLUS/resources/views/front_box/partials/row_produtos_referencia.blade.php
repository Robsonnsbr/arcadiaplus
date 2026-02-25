@php
    $codigoUnicoJson = $item->codigo_unico_json ?? '';
    $codigoUnicoLabels = [];
    if($codigoUnicoJson){
        $decoded = json_decode($codigoUnicoJson, true);
        if(is_array($decoded)){
            foreach($decoded as $cu){
                if(isset($cu['codigo'])){
                    $codigoUnicoLabels[] = $cu['codigo'];
                }
            }
        }
    }
    $isTipoUnico = $item->tipo_unico ?? ($item->produto->tipo_unico ?? 0);
@endphp
<tr class="line-product" data-tipo-unico="{{ $isTipoUnico ? 1 : 0 }}" data-produto="{{ $item->nome }}">
    <input readonly type="hidden" name="key" class="form-control" value="{{ $item->key }}">
    <input readonly type="hidden" name="produto_id[]" class="form-control" value="{{ $item->id }}">
    <input type="hidden" class="codigo_unico_ids" name="codigo_unico_ids[]" value="{{ $codigoUnicoJson }}">
    <td>
        <img src="{{ $item->img }}" style="width: 30px; height: 40px; border-radius: 10px;">
    </td>
    <td class="col-6">
        <input readonly type="text" name="produto_nome[]" class="form-control" value="{{ $item->nome }}">
        <div class="codigo-unico-wrapper @if(!$isTipoUnico) d-none @endif mt-2">
            @if($isTipoUnico)
            <span class="badge bg-warning text-dark">Código único obrigatório</span>
            <div class="codigo-unico-selected small text-primary mt-1">
                @if(sizeof($codigoUnicoLabels) > 0)
                    {{ implode(', ', $codigoUnicoLabels) }}
                @endif
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm mt-1 btn-open-codigo-unico">Selecionar códigos</button>
            @endif
        </div>
    </td>
    <td class="datatable-cell">
        <div class="form-group mb-2">
            <div class="input-group">
                <div class="input-group-prepend">
                    <button disabled id="" class="btn btn-danger" type="button">-</button>
                </div>
                <input type="tel" readonly class="form-control" name="quantidade[]" value="{{ number_format($quantidade, 3) }}">
                <div class="input-group-append">
                    <button disabled class="btn btn-success" type="button">+</button>
                </div>
            </div>
        </div>
    </td>
    <td>
        <input readonly type="tel" name="valor_unitario[]" class="form-control value-unit" value="{{ __moeda($item->valor_unitario) }}">
    </td>
    <td>
        <input readonly type="tel" name="subtotal_item[]" class="form-control subtotal-item" value="{{ __moedaInput($subtotal) }}">
    </td>
    <td>
        <button type="button" class="btn btn-danger btn-sm btn-delete-row"><i class="ri-delete-bin-line"></i></button>
    </td>
</tr>
