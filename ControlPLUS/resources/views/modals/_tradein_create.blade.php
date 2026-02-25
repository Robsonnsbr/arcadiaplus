<div class="modal fade" id="modal_tradein_create" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Trade-in</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="tradein_nome_item">Nome do item</label>
                    <input type="text" id="tradein_nome_item" class="form-control" placeholder="Ex: iPhone 12">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="tradein_serial_number">Código / Serial</label>
                    <input type="text" id="tradein_serial_number" class="form-control" placeholder="Opcional">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="tradein_valor_pretendido">Valor pretendido</label>
                    <input type="text" id="tradein_valor_pretendido" class="form-control moeda" placeholder="Opcional">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="tradein_observacao">Observações</label>
                    <textarea id="tradein_observacao" class="form-control" rows="3" placeholder="Opcional"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-create-tradein">Criar trade-in</button>
            </div>
        </div>
    </div>
</div>
