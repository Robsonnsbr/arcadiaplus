<div class="row g-2">
    <div class="col-md-5">
        {!!Form::text('nome', 'Nome')
        ->required()
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::text('cpf_cnpj', 'CPF/CNPJ')->attrs(['class' => 'cpf_cnpj'])
        !!}
    </div>
    <div class="col-md-3">
        {!!Form::tel('telefone', 'Telefone')->attrs(['class' => 'fone'])
        !!}
    </div>
    <div class="col-md-5">
        {!!Form::text('rua', 'Rua')
        !!}
    </div>
    <div class="col-md-1">
        {!!Form::tel('numero', 'Número')
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('bairro', 'Bairro')
        !!}
    </div>
    <div class="col-md-3">
        @isset($item)
        {!!Form::select('cidade_id', 'Cidade')
        ->attrs(['class' => 'select2'])->options($item != null ? [$item->cidade_id => $item->cidade->info] : [])
        ->required()
        !!}
        @else
        {!!Form::select('cidade_id', 'Cidade')
        ->attrs(['class' => 'select2'])
        ->required()
        !!}
        @endisset
    </div>
    <div class="col-md-2">
        {!!Form::select('usuario_id', 'Usuário', ['' => 'Selecione'] + $usuario->pluck('name', 'id')->all())->attrs(['class' => 'form-select'])->required()
        !!}
    </div>
    <div class="col-md-3">
        <label class="{{ !isset($item) ? 'required' : '' }}">Classe/Cargo</label>
        <div class="input-group">
            <select id="inp-funcionario_cargo_id" name="funcionario_cargo_id" class="form-select" @if(!isset($item)) required @endif>
                <option value="">Selecione</option>
                @foreach($cargos as $cargo)
                <option value="{{ $cargo->id }}" {{ old('funcionario_cargo_id', isset($item) ? $item->funcionario_cargo_id : '') == $cargo->id ? 'selected' : '' }}>
                    {{ $cargo->nome }}
                </option>
                @endforeach
            </select>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-funcionario-cargo">
                Gerenciar
            </button>
        </div>
    </div>
    <div class="col-md-2">
        {!!Form::tel('comissao', '%Comissão')->attrs(['class' => 'moeda'])
        ->value(isset($item) ? __moeda($item->comissao) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::tel('salario', 'Salário')->attrs(['class' => 'moeda'])
        ->value(isset($item) ? __moeda($item->salario) : '')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('codigo', 'Código')
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Status', [1 => 'Ativo', 0 => 'Desativado'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('permite_alterar_valor_app', 'Permite alterar valor no App', [1 => 'Sim', 0 => 'Não'])
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

<div class="modal fade" id="modal-funcionario-cargo" tabindex="-1" aria-labelledby="modalFuncionarioCargoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFuncionarioCargoLabel">Classes/Cargos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="required">Novo cargo</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="novo_funcionario_cargo_nome" maxlength="60" placeholder="Ex: vendedor, consultor, gerente">
                        <button type="button" class="btn btn-primary" id="btn-salvar-funcionario-cargo">Adicionar</button>
                    </div>
                    <small class="text-muted">O novo cargo ficará disponível para esta empresa.</small>
                </div>

                <div id="funcionario-cargo-alerta" class="alert d-none mb-3"></div>

                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th class="text-center" style="width: 100px;">Em uso</th>
                                <th class="text-center" style="width: 90px;">Tipo</th>
                                <th class="text-end" style="width: 170px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="lista-funcionario-cargos">
                            <tr>
                                <td colspan="4" class="text-muted">Carregando cargos...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
    const funcionarioCargoRoutes = {
        index: "{{ route('funcionario-cargos.index') }}",
        store: "{{ route('funcionario-cargos.store') }}",
        update: "{{ route('funcionario-cargos.update', 0) }}",
        destroy: "{{ route('funcionario-cargos.destroy', 0) }}",
    };

    function funcionarioCargoUrl(route, id) {
        return route.replace('/0', '/' + id);
    }

    function funcionarioCargoEmpresaId() {
        return $('#empresa_id').val();
    }

    function funcionarioCargoToken() {
        return $('meta[name="csrf-token"]').attr('content');
    }

    function funcionarioCargoMensagem(mensagem, tipo = 'success') {
        $('#funcionario-cargo-alerta')
            .removeClass('d-none alert-success alert-danger alert-warning')
            .addClass('alert-' + tipo)
            .text(mensagem);
    }

    function limparFuncionarioCargoMensagem() {
        $('#funcionario-cargo-alerta')
            .addClass('d-none')
            .removeClass('alert-success alert-danger alert-warning')
            .text('');
    }

    function funcionarioCargoErro(xhr, mensagemPadrao) {
        if (xhr.responseJSON && xhr.responseJSON.message) {
            return xhr.responseJSON.message;
        }

        return mensagemPadrao;
    }

    function escapeFuncionarioCargo(texto) {
        return $('<div>').text(texto || '').html();
    }

    function atualizarSelectFuncionarioCargos(cargos, selectedId = null) {
        let $select = $('#inp-funcionario_cargo_id');
        let valorAtual = selectedId || $select.val();

        $select.html('<option value="">Selecione</option>');

        $.each(cargos, function (i, cargo) {
            $select.append(new Option(cargo.nome, cargo.id, false, String(cargo.id) === String(valorAtual)));
        });

        if (valorAtual && $select.find('option[value="' + valorAtual + '"]').length) {
            $select.val(valorAtual);
        } else {
            $select.val('');
        }

        $select.trigger('change');
    }

    function renderFuncionarioCargos(cargos) {
        let $tbody = $('#lista-funcionario-cargos');
        $tbody.html('');

        if (!cargos.length) {
            $tbody.append('<tr><td colspan="4" class="text-muted">Nenhum cargo cadastrado.</td></tr>');
            return;
        }

        $.each(cargos, function (i, cargo) {
            let nome = escapeFuncionarioCargo(cargo.nome);
            let emUso = cargo.funcionarios_count || 0;
            let canManage = cargo.can_manage ? 1 : 0;
            let tipo = canManage ? 'Empresa' : 'Padrão';
            let row = `
                <tr data-id="${cargo.id}" data-em-uso="${emUso}" data-can-manage="${canManage}">
                    <td>
                        <input type="text" class="form-control form-control-sm input-cargo-nome" value="${nome}" maxlength="60" disabled>
                    </td>
                    <td class="text-center">${emUso}</td>
                    <td class="text-center"><span class="badge bg-secondary">${tipo}</span></td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-warning btn-editar-cargo">Editar</button>
                        <button type="button" class="btn btn-sm btn-success btn-salvar-edicao-cargo d-none">Salvar</button>
                        <button type="button" class="btn btn-sm btn-light btn-cancelar-edicao-cargo d-none">Cancelar</button>
                        <button type="button" class="btn btn-sm btn-danger btn-excluir-cargo">Excluir</button>
                    </td>
                </tr>
            `;

            $tbody.append(row);
        });
    }

    function carregarFuncionarioCargos(selectedId = null) {
        $.get(funcionarioCargoRoutes.index, { empresa_id: funcionarioCargoEmpresaId() })
            .done(function (response) {
                atualizarSelectFuncionarioCargos(response, selectedId);
                renderFuncionarioCargos(response);
            })
            .fail(function (xhr) {
                funcionarioCargoMensagem(funcionarioCargoErro(xhr, 'Não foi possível carregar os cargos.'), 'danger');
            });
    }

    $('#modal-funcionario-cargo').on('shown.bs.modal', function () {
        limparFuncionarioCargoMensagem();
        carregarFuncionarioCargos();
    });

    $(document).on('keydown', '#novo_funcionario_cargo_nome', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            $('#btn-salvar-funcionario-cargo').trigger('click');
        }
    });

    $(document).on('click', '#btn-salvar-funcionario-cargo', function () {
        let nome = $('#novo_funcionario_cargo_nome').val().trim();
        let $button = $(this);

        if (!nome) {
            alert('Informe o nome da classe/cargo.');
            return;
        }

        $button.prop('disabled', true);

        $.ajax({
            url: funcionarioCargoRoutes.store,
            type: 'POST',
            dataType: 'json',
            data: {
                _token: funcionarioCargoToken(),
                empresa_id: funcionarioCargoEmpresaId(),
                nome: nome
            },
            success: function (response) {
                $('#novo_funcionario_cargo_nome').val('');
                funcionarioCargoMensagem('Cargo salvo com sucesso.');
                carregarFuncionarioCargos(response.id);
            },
            error: function (xhr) {
                funcionarioCargoMensagem(funcionarioCargoErro(xhr, 'Não foi possível cadastrar a classe/cargo.'), 'danger');
            },
            complete: function () {
                $button.prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.btn-editar-cargo', function () {
        let $row = $(this).closest('tr');

        if (parseInt($row.data('can-manage'), 10) !== 1) {
            funcionarioCargoMensagem('Cargos padrão não podem ser editados pela modal. Crie um novo cargo para personalizar a lista desta empresa.', 'warning');
            return;
        }

        $row.data('nome-original', $row.find('.input-cargo-nome').val());
        $row.find('.input-cargo-nome').prop('disabled', false).focus();
        $row.find('.btn-editar-cargo, .btn-excluir-cargo').addClass('d-none');
        $row.find('.btn-salvar-edicao-cargo, .btn-cancelar-edicao-cargo').removeClass('d-none');
    });

    $(document).on('click', '.btn-cancelar-edicao-cargo', function () {
        let $row = $(this).closest('tr');
        $row.find('.input-cargo-nome').val($row.data('nome-original')).prop('disabled', true);
        $row.find('.btn-editar-cargo, .btn-excluir-cargo').removeClass('d-none');
        $row.find('.btn-salvar-edicao-cargo, .btn-cancelar-edicao-cargo').addClass('d-none');
    });

    $(document).on('click', '.btn-salvar-edicao-cargo', function () {
        let $row = $(this).closest('tr');
        let id = $row.data('id');
        let nome = $row.find('.input-cargo-nome').val().trim();
        let $button = $(this);

        if (!nome) {
            funcionarioCargoMensagem('Informe o nome do cargo.', 'warning');
            return;
        }

        $button.prop('disabled', true);

        $.ajax({
            url: funcionarioCargoUrl(funcionarioCargoRoutes.update, id),
            type: 'POST',
            dataType: 'json',
            data: {
                _token: funcionarioCargoToken(),
                _method: 'PUT',
                empresa_id: funcionarioCargoEmpresaId(),
                nome: nome
            },
            success: function (response) {
                funcionarioCargoMensagem('Cargo atualizado com sucesso.');
                carregarFuncionarioCargos(response.id);
            },
            error: function (xhr) {
                funcionarioCargoMensagem(funcionarioCargoErro(xhr, 'Não foi possível atualizar o cargo.'), 'danger');
            },
            complete: function () {
                $button.prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.btn-excluir-cargo', function () {
        let $row = $(this).closest('tr');
        let id = $row.data('id');
        let emUso = parseInt($row.data('em-uso'), 10);

        if (parseInt($row.data('can-manage'), 10) !== 1) {
            funcionarioCargoMensagem('Cargos padrão não podem ser excluídos.', 'warning');
            return;
        }

        if (emUso > 0) {
            funcionarioCargoMensagem('Este cargo não pode ser excluído porque já está vinculado a um ou mais funcionários.', 'warning');
            return;
        }

        if (!confirm('Deseja excluir este cargo?')) {
            return;
        }

        $.ajax({
            url: funcionarioCargoUrl(funcionarioCargoRoutes.destroy, id),
            type: 'POST',
            dataType: 'json',
            data: {
                _token: funcionarioCargoToken(),
                _method: 'DELETE',
                empresa_id: funcionarioCargoEmpresaId()
            },
            success: function () {
                let selectedId = $('#inp-funcionario_cargo_id').val();
                funcionarioCargoMensagem('Cargo excluído com sucesso.');
                carregarFuncionarioCargos(String(selectedId) === String(id) ? null : selectedId);
            },
            error: function (xhr) {
                funcionarioCargoMensagem(funcionarioCargoErro(xhr, 'Não foi possível excluir o cargo.'), 'danger');
            }
        });
    });
</script>
@endsection
