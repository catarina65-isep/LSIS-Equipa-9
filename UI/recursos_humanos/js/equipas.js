/**
 * Funções para gerenciar operações de equipes via AJAX
 */

// URL base para as requisições
const API_EQUIPAS = 'processar_equipa.php';

/**
 * Exibe uma mensagem de alerta
 * @param {string} mensagem - Texto da mensagem
 * @param {string} tipo - Tipo de alerta (success, danger, warning, info)
 */
function mostrarMensagem(mensagem, tipo = 'info') {
    const alerta = `
        <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
            ${mensagem}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    `;
    
    // Adiciona a mensagem no topo da página
    $('#mensagens').html(alerta);
    
    // Remove a mensagem após 5 segundos
    setTimeout(() => {
        $('.alert').alert('close');
    }, 5000);
}

/**
 * Carrega a lista de equipes na tabela
 */
function carregarEquipas() {
    $.ajax({
        url: API_EQUIPAS,
        type: 'GET',
        data: { acao: 'listar' },
        dataType: 'json',
        beforeSend: function() {
            $('#tabelaEquipas tbody').html('<tr><td colspan="6" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div></td></tr>');
        },
        success: function(response) {
            if (response.sucesso && response.dados) {
                atualizarTabelaEquipas(response.dados);
            } else {
                mostrarMensagem('Erro ao carregar as equipes: ' + (response.erro || 'Erro desconhecido'), 'danger');
            }
        },
        error: function(xhr, status, error) {
            mostrarMensagem('Erro na requisição: ' + error, 'danger');
        }
    });
}

/**
 * Atualiza a tabela de equipes com os dados fornecidos
 * @param {Array} equipas - Lista de equipes
 */
function atualizarTabelaEquipas(equipas) {
    const tbody = $('#tabelaEquipas tbody');
    tbody.empty();
    
    if (equipas.length === 0) {
        tbody.html('<tr><td colspan="6" class="text-center">Nenhuma equipe cadastrada.</td></tr>');
        return;
    }
    
    equipas.forEach(function(equipa) {
        const tr = `
            <tr data-id="${equipa.id_equipa}">
                <td>${equipa.nome}</td>
                <td>${equipa.coordenador_nome || 'Não definido'}</td>
                <td>${equipa.total_membros || 0}</td>
                <td>${equipa.departamento_nome || '-'}</td>
                <td>${equipa.ativo ? '<span class="badge bg-success">Ativo</span>' : '<span class="badge bg-secondary">Inativo</span>'}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary btn-editar" data-id="${equipa.id_equipa}" title="Editar">
                        <i class="bx bx-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger btn-excluir" data-id="${equipa.id_equipa}" title="Excluir">
                        <i class="bx bx-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(tr);
    });
    
    // Inicializar tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
}

/**
 * Abre o modal para adicionar/editar uma equipe
 * @param {number} [id] - ID da equipe para edição (opcional)
 */
function abrirModalEquipa(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalEquipa'));
    const titulo = id ? 'Editar Equipe' : 'Nova Equipe';
    
    $('#modalEquipaLabel').text(titulo);
    
    if (id) {
        // Carregar dados da equipe para edição
        carregarDadosEquipa(id, modal);
    } else {
        // Limpar formulário para nova equipe
        $('#formEquipa')[0].reset();
        $('#equipaId').val('');
        modal.show();
    }
}

/**
 * Carrega os dados de uma equipe para edição
 * @param {number} id - ID da equipe
 * @param {Object} modal - Instância do modal
 */
function carregarDadosEquipa(id, modal) {
    $.ajax({
        url: API_EQUIPAS,
        type: 'GET',
        data: { 
            acao: 'obter',
            id: id
        },
        dataType: 'json',
        beforeSend: function() {
            $('#formContainer').html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div></div>');
        },
        success: function(response) {
            if (response.sucesso && response.dados) {
                const { equipa, membros } = response.dados;
                
                // Preencher o formulário
                $('#formEquipa')[0].reset();
                $('#equipaId').val(equipa.id_equipa);
                $('#nome').val(equipa.nome);
                $('#descricao').val(equipa.descricao || '');
                
                // Preencher coordenador
                if (equipa.id_coordenador) {
                    $('#coordenador_id').val(equipa.id_coordenador).trigger('change');
                }
                
                // Preencher departamento e equipe pai, se existirem
                if ($('#id_departamento').length && equipa.id_departamento) {
                    $('#id_departamento').val(equipa.id_departamento).trigger('change');
                }
                
                if ($('#id_equipa_pai').length && equipa.id_equipa_pai) {
                    $('#id_equipa_pai').val(equipa.id_equipa_pai).trigger('change');
                }
                
                // Mostrar o formulário
                modal.show();
            } else {
                mostrarMensagem('Erro ao carregar os dados da equipe: ' + (response.erro || 'Erro desconhecido'), 'danger');
            }
        },
        error: function(xhr, status, error) {
            mostrarMensagem('Erro na requisição: ' + error, 'danger');
        }
    });
}

/**
 * Envia o formulário de equipe
 */
function enviarFormulario() {
    const form = $('#formEquipa')[0];
    const formData = new FormData(form);
    const isEdicao = $('#equipaId').val() !== '';
    
    // Adicionar ação ao formData
    formData.append('acao', isEdicao ? 'editar' : 'criar');
    
    // Se for edição, adicionar o ID
    if (isEdicao) {
        formData.append('id', $('#equipaId').val());
    }
    
    // Adicionar membros selecionados
    const membrosSelecionados = [];
    $('.membro-checkbox:checked').each(function() {
        membrosSelecionados.push($(this).val());
    });
    formData.append('membros', JSON.stringify(membrosSelecionados));
    
    // Enviar requisição
    $.ajax({
        url: API_EQUIPAS,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        beforeSend: function() {
            $('#btnSalvar').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...');
        },
        success: function(response) {
            if (response.sucesso) {
                mostrarMensagem(response.mensagem || 'Operação realizada com sucesso!', 'success');
                $('#modalEquipa').modal('hide');
                carregarEquipas();
            } else {
                mostrarMensagem('Erro: ' + (response.erro || 'Erro desconhecido'), 'danger');
            }
        },
        error: function(xhr, status, error) {
            mostrarMensagem('Erro na requisição: ' + error, 'danger');
        },
        complete: function() {
            $('#btnSalvar').prop('disabled', false).html('<i class="bx bx-save"></i> Salvar');
        }
    });
}

/**
 * Exclui uma equipe após confirmação
 * @param {number} id - ID da equipe a ser excluída
 */
function excluirEquipa(id) {
    if (!confirm('Tem certeza que deseja excluir esta equipe? Esta ação não pode ser desfeita.')) {
        return;
    }
    
    $.ajax({
        url: API_EQUIPAS,
        type: 'POST',
        data: {
            acao: 'excluir',
            id: id
        },
        dataType: 'json',
        beforeSend: function() {
            $(`button[data-id="${id}"]`).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
        },
        success: function(response) {
            if (response.sucesso) {
                mostrarMensagem(response.mensagem || 'Equipe excluída com sucesso!', 'success');
                carregarEquipas();
            } else {
                mostrarMensagem('Erro ao excluir a equipe: ' + (response.erro || 'Erro desconhecido'), 'danger');
            }
        },
        error: function(xhr, status, error) {
            mostrarMensagem('Erro na requisição: ' + error, 'danger');
        }
    });
}

// Eventos quando o documento estiver pronto
$(document).ready(function() {
    // Carregar equipes ao iniciar
    carregarEquipas();
    
    // Abrir modal para nova equipe
    $('#btnNovaEquipa').click(function() {
        abrirModalEquipa();
    });
    
    // Enviar formulário
    $('#formEquipa').on('submit', function(e) {
        e.preventDefault();
        enviarFormulario();
    });
    
    // Delegar eventos para botões dinâmicos
    $(document).on('click', '.btn-editar', function() {
        const id = $(this).data('id');
        abrirModalEquipa(id);
    });
    
    $(document).on('click', '.btn-excluir', function() {
        const id = $(this).data('id');
        excluirEquipa(id);
    });
    
    // Inicializar selects com Select2
    if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Selecione uma opção',
            allowClear: true
        });
    }
});
