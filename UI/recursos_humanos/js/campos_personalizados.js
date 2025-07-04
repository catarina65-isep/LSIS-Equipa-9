/**
 * Script para gerenciar a interface de campos personalizados
 */

class CamposPersonalizadosUI {
    constructor() {
        this.camposPorCategoria = {};
        this.categorias = [];
        this.idColaborador = null;
        
        // Inicializa os eventos
        this.inicializarEventos();
        
        // Carrega os dados iniciais
        this.carregarDadosIniciais();
    }
    
    /**
     * Inicializa os eventos da interface
     */
    inicializarEventos() {
        // Evento para salvar um campo
        $(document).on('click', '.btn-salvar-campo', (e) => this.salvarCampo(e));
        
        // Evento para excluir um campo
        $(document).on('click', '.btn-excluir-campo', (e) => this.excluirCampo(e));
        
        // Evento para adicionar uma opção
        $(document).on('click', '.btn-adicionar-opcao', (e) => this.adicionarOpcao(e));
        
        // Evento para remover uma opção
        $(document).on('click', '.btn-remover-opcao', (e) => this.removerOpcao(e));
        
        // Evento para mudar o tipo de campo
        $(document).on('change', '#tipoCampo', (e) => this.atualizarTipoCampo(e));
        
        // Evento para abrir o modal de edição
        $(document).on('click', '.btn-editar-campo', (e) => this.abrirModalEdicao(e));
        
        // Evento para abrir o modal de confirmação de exclusão
        $(document).on('click', '.btn-confirmar-exclusao', (e) => this.confirmarExclusao(e));
    }
    
    /**
     * Carrega os dados iniciais da página
     */
    async carregarDadosIniciais() {
        try {
            // Mostra o loading
            this.mostrarLoading(true);
            
            // Carrega as categorias
            const responseCategorias = await this.obterCategorias();
            
            if (responseCategorias.sucesso) {
                this.categorias = responseCategorias.dados;
                this.atualizarFiltroCategorias();
                
                // Carrega os campos da primeira categoria por padrão
                if (this.categorias.length > 0) {
                    await this.carregarCamposPorCategoria(this.categorias[0]);
                }
            } else {
                this.mostrarErro('Erro ao carregar categorias: ' + responseCategorias.erro);
            }
            
        } catch (error) {
            this.mostrarErro('Erro ao carregar dados iniciais: ' + error.message);
        } finally {
            this.mostrarLoading(false);
        }
    }
    
    /**
     * Atualiza o filtro de categorias na interface
     */
    atualizarFiltroCategorias() {
        const $filtro = $('#filtroCategoria');
        $filtro.empty();
        
        this.categorias.forEach(categoria => {
            $filtro.append(`<option value="${categoria}">${this.formatarCategoria(categoria)}</option>`);
        });
        
        // Adiciona evento de mudança
        $filtro.off('change').on('change', async (e) => {
            await this.carregarCamposPorCategoria(e.target.value);
        });
    }
    
    /**
     * Formata o nome da categoria para exibição
     */
    formatarCategoria(categoria) {
        return categoria
            .split('_')
            .map(palavra => palavra.charAt(0).toUpperCase() + palavra.slice(1))
            .join(' ');
    }
    
    /**
     * Carrega os campos de uma categoria específica
     */
    async carregarCamposPorCategoria(categoria) {
        try {
            this.mostrarLoading(true);
            
            const response = await $.ajax({
                url: 'api/campos_personalizados.php',
                method: 'GET',
                data: { categoria }
            });
            
            if (response.sucesso) {
                this.camposPorCategoria[categoria] = response.dados;
                this.atualizarListaCampos(categoria);
            } else {
                this.mostrarErro('Erro ao carregar campos: ' + (response.erro || 'Erro desconhecido'));
            }
            
        } catch (error) {
            this.mostrarErro('Erro ao carregar campos: ' + error.message);
        } finally {
            this.mostrarLoading(false);
        }
    }
    
    /**
     * Atualiza a lista de campos na interface
     */
    atualizarListaCampos(categoria) {
        const $container = $('#listaCampos');
        $container.empty();
        
        const campos = this.camposPorCategoria[categoria] || [];
        
        if (campos.length === 0) {
            $container.html(`
                <div class="alert alert-info">
                    Nenhum campo encontrado nesta categoria.
                </div>
            `);
            return;
        }
        
        const $tabela = $(`
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Obrigatório</th>
                        <th>Requer Comprovativo</th>
                        <th>Status</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        `);
        
        const $tbody = $tabela.find('tbody');
        
        campos.forEach(campo => {
            const $linha = $(`
                <tr data-id="${campo.id_campo}">
                    <td>${campo.rotulo}</td>
                    <td>${this.obterNomeTipoCampo(campo.tipo)}</td>
                    <td>${campo.obrigatorio ? 'Sim' : 'Não'}</td>
                    <td>${campo.requer_comprovativo ? 'Sim' : 'Não'}</td>
                    <td>
                        <span class="badge ${campo.ativo ? 'bg-success' : 'bg-secondary'}">
                            ${campo.ativo ? 'Ativo' : 'Inativo'}
                        </span>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-primary btn-editar-campo" data-id="${campo.id_campo}">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-danger btn-excluir-campo" data-id="${campo.id_campo}">
                            <i class="fas fa-trash"></i> Excluir
                        </button>
                    </td>
                </tr>
            `);
            
            $tbody.append($linha);
        });
        
        $container.append($tabela);
    }
    
    /**
     * Obtém o nome amigável de um tipo de campo
     */
    obterNomeTipoCampo(tipo) {
        const tipos = {
            'texto': 'Texto',
            'numero': 'Número',
            'data': 'Data',
            'email': 'E-mail',
            'telefone': 'Telefone',
            'cep': 'CEP',
            'cpf': 'CPF',
            'cnpj': 'CNPJ',
            'select': 'Seleção',
            'checkbox': 'Checkbox',
            'radio': 'Botão de Rádio',
            'textarea': 'Área de Texto',
            'arquivo': 'Arquivo',
            'mod99': 'Mod 99',
            'nif': 'NIF',
            'niss': 'NISS',
            'cartaocidadao': 'Cartão de Cidadão'
        };
        
        return tipos[tipo] || tipo;
    }
    
    /**
     * Abre o modal para edição de um campo
     */
    async abrirModalEdicao(event) {
        const idCampo = $(event.currentTarget).data('id');
        
        try {
            this.mostrarLoading(true);
            
            const response = await $.ajax({
                url: 'api/campos_personalizados.php',
                method: 'GET',
                data: { id: idCampo }
            });
            
            if (response.sucesso) {
                const campo = response.dados;
                this.preencherFormulario(campo);
                $('#campoModal').modal('show');
            } else {
                this.mostrarErro('Erro ao carregar campo: ' + (response.erro || 'Erro desconhecido'));
            }
            
        } catch (error) {
            this.mostrarErro('Erro ao carregar campo: ' + error.message);
        } finally {
            this.mostrarLoading(false);
        }
    }
    
    /**
     * Preenche o formulário com os dados de um campo
     */
    preencherFormulario(campo) {
        // Limpa o formulário
        $('#formCampo')[0].reset();
        
        // Preenche os campos básicos
        $('#campoId').val(campo.id_campo || '');
        $('#nomeCampo').val(campo.nome || '');
        $('#rotuloCampo').val(campo.rotulo || '');
        $('#tipoCampo').val(campo.tipo || 'texto');
        $('#descricaoCampo').val(campo.descricao || '');
        $('#categoriaCampo').val(campo.categoria || 'outros');
        $('#valorPadrao').val(campo.valor_padrao || '');
        $('#tamanhoMaximo').val(campo.tamanho_maximo || '');
        $('#campoObrigatorio').prop('checked', !!campo.obrigatorio);
        $('#campoAtivo').prop('checked', campo.ativo !== undefined ? !!campo.ativo : true);
        $('#requerComprovativo').prop('checked', !!campo.requer_comprovativo);
        $('#ajudaCampo').val(campo.ajuda || '');
        
        // Atualiza a visibilidade dos campos com base no tipo
        this.atualizarTipoCampo();
        
        // Limpa as opções atuais
        $('#opcoesContainer').empty();
        
        // Se for um campo com opções, preenche as opções
        if (campo.opcoes && campo.opcoes.length > 0) {
            campo.opcoes.forEach((opcao, index) => {
                this.adicionarOpcao(opcao, index);
            });
        } else if (campo.tipo && ['select', 'radio', 'checkbox'].includes(campo.tipo)) {
            // Se for um campo que requer opções mas não tem, adiciona um campo vazio
            this.adicionarOpcao();
        }
        
        // Define o título do modal
        $('#campoModalLabel').text(campo.id_campo ? 'Editar Campo' : 'Novo Campo');
    }
    
    /**
     * Atualiza a interface com base no tipo de campo selecionado
     */
    atualizarTipoCampo() {
        const tipo = $('#tipoCampo').val();
        const $opcoesContainer = $('#opcoesContainer');
        const $containerOpcoes = $('.container-opcoes');
        const $containerTamanhoMaximo = $('.container-tamanho-maximo');
        
        // Mostra/oculta o container de opções
        if (['select', 'radio', 'checkbox'].includes(tipo)) {
            $containerOpcoes.show();
            
            // Se não houver opções, adiciona uma vazia
            if ($opcoesContainer.children().length === 0) {
                this.adicionarOpcao();
            }
        } else {
            $containerOpcoes.hide();
        }
        
        // Mostra/oculta o campo de tamanho máximo
        if (['texto', 'textarea'].includes(tipo)) {
            $containerTamanhoMaximo.show();
        } else {
            $containerTamanhoMaximo.hide();
        }
    }
    
    /**
     * Adiciona uma nova opção ao campo
     */
    adicionarOpcao(valor = '', rotulo = '') {
        const index = $('#opcoesContainer').children().length;
        
        const $opcao = $(`
            <div class="row mb-2 opcao-campo">
                <div class="col-md-5">
                    <input type="text" class="form-control opcao-valor" placeholder="Valor" value="${valor}">
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control opcao-rotulo" placeholder="Rótulo" value="${rotulo || valor}">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm btn-remover-opcao">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `);
        
        $('#opcoesContainer').append($opcao);
    }
    
    /**
     * Remove uma opção do campo
     */
    removerOpcao(event) {
        $(event.currentTarget).closest('.opcao-campo').remove();
    }
    
    /**
     * Salva um campo (cria ou atualiza)
     */
    async salvarCampo(event) {
        event.preventDefault();
        
        // Obtém os dados do formulário
        const dados = {
            id: $('#campoId').val() || null,
            nome: $('#nomeCampo').val(),
            rotulo: $('#rotuloCampo').val(),
            tipo: $('#tipoCampo').val(),
            descricao: $('#descricaoCampo').val(),
            categoria: $('#categoriaCampo').val(),
            valor_padrao: $('#valorPadrao').val(),
            tamanho_maximo: $('#tamanhoMaximo').val() || null,
            obrigatorio: $('#campoObrigatorio').is(':checked'),
            ativo: $('#campoAtivo').is(':checked'),
            requer_comprovativo: $('#requerComprovativo').is(':checked'),
            ajuda: $('#ajudaCampo').val(),
            opcoes: []
        };
        
        // Obtém as opções do campo
        $('.opcao-campo').each(function() {
            const valor = $(this).find('.opcao-valor').val().trim();
            const rotulo = $(this).find('.opcao-rotulo').val().trim();
            
            if (valor) {
                dados.opcoes.push({
                    valor,
                    rotulo: rotulo || valor
                });
            }
        });
        
        try {
            this.mostrarLoading(true);
            
            const metodo = dados.id ? 'PUT' : 'POST';
            const url = 'api/campos_personalizados.php';
            
            const response = await $.ajax({
                url,
                method: metodo,
                contentType: 'application/json',
                data: JSON.stringify(dados)
            });
            
            if (response.sucesso) {
                this.mostrarSucesso(dados.id ? 'Campo atualizado com sucesso!' : 'Campo criado com sucesso!');
                
                // Fecha o modal e recarrega os dados
                $('#campoModal').modal('hide');
                await this.carregarCamposPorCategoria($('#filtroCategoria').val());
            } else {
                this.mostrarErro('Erro ao salvar campo: ' + (response.erro || 'Erro desconhecido'));
            }
            
        } catch (error) {
            this.mostrarErro('Erro ao salvar campo: ' + (error.responseJSON?.erro || error.message));
        } finally {
            this.mostrarLoading(false);
        }
    }
    
    /**
     * Exclui um campo
     */
    async excluirCampo(event) {
        const idCampo = $(event.currentTarget).data('id');
        
        // Mostra o modal de confirmação
        $('#confirmarExclusaoModal').data('id', idCampo);
        $('#confirmarExclusaoModal').modal('show');
    }
    
    /**
     * Confirma a exclusão de um campo
     */
    async confirmarExclusao(event) {
        const idCampo = $(event.currentTarget).data('id') || $('#confirmarExclusaoModal').data('id');
        
        try {
            this.mostrarLoading(true);
            
            const response = await $.ajax({
                url: 'api/campos_personalizados.php',
                method: 'DELETE',
                data: { id: idCampo }
            });
            
            if (response.sucesso) {
                this.mostrarSucesso('Campo excluído com sucesso!');
                
                // Fecha o modal e recarrega os dados
                $('#confirmarExclusaoModal').modal('hide');
                await this.carregarCamposPorCategoria($('#filtroCategoria').val());
            } else {
                this.mostrarErro('Erro ao excluir campo: ' + (response.erro || 'Erro desconhecido'));
            }
            
        } catch (error) {
            this.mostrarErro('Erro ao excluir campo: ' + (error.responseJSON?.erro || error.message));
        } finally {
            this.mostrarLoading(false);
        }
    }
    
    /**
     * Obtém a lista de categorias
     */
    async obterCategorias() {
        try {
            const response = await $.ajax({
                url: 'api/campos_personalizados.php',
                method: 'GET',
                data: { categorias: 1 }
            });
            
            return response;
        } catch (error) {
            console.error('Erro ao obter categorias:', error);
            return { sucesso: false, erro: error.message };
        }
    }
    
    /**
     * Mostra uma mensagem de sucesso
     */
    mostrarSucesso(mensagem) {
        this.mostrarMensagem('success', 'Sucesso!', mensagem);
    }
    
    /**
     * Mostra uma mensagem de erro
     */
    mostrarErro(mensagem) {
        this.mostrarMensagem('danger', 'Erro!', mensagem);
    }
    
    /**
     * Mostra uma mensagem na interface
     */
    mostrarMensagem(tipo, titulo, mensagem) {
        const $alerta = $(`
            <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                <strong>${titulo}</strong> ${mensagem}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        `);
        
        $('#mensagens').empty().append($alerta);
        
        // Remove o alerta após 5 segundos
        setTimeout(() => {
            $alerta.alert('close');
        }, 5000);
    }
    
    /**
     * Mostra ou esconde o indicador de carregamento
     */
    mostrarLoading(mostrar) {
        if (mostrar) {
            $('#loading').show();
        } else {
            $('#loading').hide();
        }
    }
}

// Inicializa a interface quando o documento estiver pronto
$(document).ready(() => {
    window.camposPersonalizadosUI = new CamposPersonalizadosUI();
    
    // Evento para abrir o modal de novo campo
    $('#btnNovoCampo').on('click', () => {
        window.camposPersonalizadosUI.preencherFormulario({});
        $('#campoModal').modal('show');
    });
    
    // Evento para confirmar a exclusão
    $('#btnConfirmarExclusao').on('click', (e) => {
        window.camposPersonalizadosUI.confirmarExclusao(e);
    });
});
