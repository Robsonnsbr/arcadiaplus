<div class="modal fade" id="modal_tradein_credit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aplicar crédito (trade-in)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">
                    Disponível: <strong id="tradein_credit_available">R$ 0,00</strong>
                </p>
                <div class="row g-2">
                    <div class="col-md-12">
                        {!! Form::text('valor_tradein_credito', 'Valor a aplicar')->attrs(['class' => 'moeda', 'id' => 'tradein_credit_input']) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btn-confirm-tradein" class="btn btn-primary">Aplicar</button>
            </div>
        </div>
    </div>
</div>
