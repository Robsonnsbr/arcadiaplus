<div class="modal fade" id="modal_codigo_unico" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalCodigoUnicoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-light">
                <h5 class="modal-title" id="modalCodigoUnicoLabel">Selecionar códigos únicos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning d-none" id="modal_codigo_unico_alert"></div>
                <div class="mb-2">
                    <strong>Produto:</strong> <span id="modal_codigo_unico_produto">--</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-centered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 250px">Código</th>
                                <th>Observação</th>
                            </tr>
                        </thead>
                        <tbody id="modal_codigo_unico_body">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="modal_codigo_unico_salvar">Salvar códigos</button>
            </div>
        </div>
    </div>
</div>
