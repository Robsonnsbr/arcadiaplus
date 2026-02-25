<div class="modal fade" id="modal_tradein_status" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Trade-in</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tradein_status_id" value="">
                <div class="mb-2">
                    <strong>Status:</strong> <span id="tradein_status_text">--</span>
                </div>
                <div class="mb-2">
                    <strong>Valor avaliado:</strong> <span id="tradein_valor_text">R$ 0,00</span>
                </div>
                <div class="mb-2">
                    <strong>Aceite:</strong> <span id="tradein_aceite_text">--</span>
                </div>
            </div>
            <div class="modal-footer">
                <a class="btn btn-outline-secondary" id="btn-tradein-termo" href="#" target="_blank" disabled>Gerar termo</a>
                <button type="button" class="btn btn-success" id="btn-tradein-accept" disabled>Cliente aceitou</button>
                <button type="button" class="btn btn-danger" id="btn-tradein-reject" disabled>Cliente recusou</button>
                <button type="button" class="btn btn-outline-danger" id="btn-tradein-cancel">Cancelar trade-in</button>
            </div>
        </div>
    </div>
</div>
