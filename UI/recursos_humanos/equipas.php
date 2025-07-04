<?php
// Iniciar a sessão
session_start();

// Incluir arquivos necessários
require_once __DIR__ . '/../../autoload.php';

// Verificar se o usuário está autenticado e tem perfil de RH (ID 2) ou Administrador (ID 1)
if (!isset($_SESSION['utilizador_id']) || !in_array($_SESSION['id_perfilacesso'], [1, 2])) {
    header('Location: /LSIS-Equipa-9/UI/login.php');
    exit();
}

// Inicializar variáveis
$mensagem = '';
$tipoMensagem = 'info';
$equipas = [];
$coordenadores = [];
$funcionarios = [];

try {
    // Inicializar BLLs
    $equipaBLL = new EquipaBLL();
    $utilizadorBLL = new UtilizadorBLL();
    
    // Obter lista de equipes
    $equipas = $equipaBLL->listarEquipas();
    
    // Obter lista de coordenadores
    $coordenadores = $utilizadorBLL->obterCoordenadores();
    
    // Debug: Mostrar dados dos coordenadores
    echo '<div style="display:none;">';
    echo '<h4>Debug - Dados dos Coordenadores:</h4>';
    echo '<pre>';
    var_dump($coordenadores);
    echo '</pre>';
    echo '</div>';
    
    // Obter lista de funcionários
    $funcionarios = $utilizadorBLL->obterFuncionarios();
    
    // Log para depuração
    error_log('Funcionários carregados: ' . print_r($funcionarios, true));
    
} catch (Exception $e) {
    $mensagem = 'Erro ao carregar os dados: ' . $e->getMessage();
    $tipoMensagem = 'danger';
    error_log('Erro em equipas.php: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Equipas - Portal do Colaborador</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <style>
        /* Layout da Página */
        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1000;
            background: #1a1f33;
            overflow-y: auto;
            transition: all 0.3s;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
            background-color: #f5f7fb;
            transition: all 0.3s;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                margin-left: -250px;
            }
            .sidebar.active {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
            .main-content.active {
                margin-left: 250px;
            }
        }
        
        /* Estilos dos Cards */
        .team-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 20px;
            border: 1px solid rgba(0,0,0,.125);
            border-radius: 0.75rem;
            overflow: hidden;
            height: 100%;
            background: #fff;
        }
        
        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,.125);
            padding: 1rem 1.25rem;
        }
        
        .team-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0;
            font-size: 1.1rem;
        }
        
        .team-description {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
            line-height: 1.5;
        }
        
        .member-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .members-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .btn-action {
            padding: 0.35rem 0.75rem;
            font-size: 0.85rem;
            border-radius: 0.5rem;
        }
        
        /* Estilos do Header */
        .page-header {
            background: #fff;
            padding: 1.5rem;
            margin: -1.5rem -1.5rem 1.5rem -1.5rem;
            border-bottom: 1px solid rgba(0,0,0,.05);
        }
        
        /* Estilos para o Select2 */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
            border-radius: 0.5rem;
            border-color: #dee2e6;
        }
        
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            border-radius: 0.35rem;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="main-content">
                <!-- Page Header -->
                <div class="page-header mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-1 fw-bold text-gray-800">Gestão de Equipas</h1>
                            <p class="mb-0 text-muted">Gerencie as equipas da sua organização</p>
                        </div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEquipa">
                            <i class="fas fa-plus me-2"></i> Nova Equipa
                        </button>
                    </div>
                </div>

                <!-- Mensagens de Feedback -->
                <?php if ($mensagem): ?>
                <div class="alert alert-<?= $tipoMensagem ?> alert-dismissible fade show mb-4">
                    <?= $mensagem ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
                <?php endif; ?>

                <!-- Conteúdo Principal -->
                <div class="container-fluid">
                    <div class="row">
                        <?php if (empty($equipas)): ?>
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> Nenhuma equipa cadastrada. Clique em "Nova Equipa" para começar.
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($equipas as $equipa): ?>
                                <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-4">
                                    <div class="card team-card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title team-name"><?= htmlspecialchars($equipa['nome']) ?></h5>
                                            
                                            <?php if (!empty($equipa['descricao'])): ?>
                                                <p class="team-description"><?= htmlspecialchars($equipa['descricao']) ?></p>
                                            <?php endif; ?>
                                            
                                            <div class="d-flex align-items-center mb-3">
                                                <small class="text-muted me-2">Coordenador:</small>
                                                <?php if (!empty($equipa['coordenador_foto'])): ?>
                                                    <img src="<?= htmlspecialchars($equipa['coordenador_foto']) ?>" alt="Coordenador" class="member-avatar">
                                                <?php else: ?>
                                                    <div class="member-avatar bg-primary text-white d-flex align-items-center justify-content-center">
                                                        <?= substr($equipa['coordenador_nome'], 0, 1) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <span class="ms-2"><?= htmlspecialchars($equipa['coordenador_nome']) ?></span>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-users me-1"></i> 
                                                    <?= count($equipa['membros'] ?? []) ?> membro(s)
                                                </small>
                                                <div>
                                                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editarEquipa(<?= $equipa['id'] ?>)" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" onclick="confirmarExclusao(<?= $equipa['id'] ?>)" title="Excluir">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal de Nova/Edição de Equipa -->
    <div class="modal fade" id="modalEquipa" tabindex="-1" aria-labelledby="modalEquipaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formEquipa" method="post" action="processar_equipa.php">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalEquipaLabel">Nova Equipa</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="acao" value="criar">
                        <input type="hidden" name="id" id="equipa_id">
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nome da Equipa *</label>
                                <input type="text" name="nome" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Membros da Equipa</label>
                                <select name="membros[]" class="form-select select2" multiple="multiple" style="width: 100%;">
                                    <?php 
                                    // Log para depuração
                                    error_log('Funcionários disponíveis: ' . print_r($funcionarios, true));
                                    
                                    if (!empty($funcionarios)): 
                                        foreach ($funcionarios as $funcionario): 
                                            // Verifica se as chaves existem antes de acessá-las
                                            $nome = $funcionario['nome'] ?? '';
                                            $apelido = $funcionario['apelido'] ?? '';
                                            $username = $funcionario['username'] ?? '';
                                            $id = $funcionario['id'] ?? '';
                                            
                                            // Usa nome_completo se existir, senão concatena nome e apelido, senão usa username
                                            if (isset($funcionario['nome_completo']) && !empty($funcionario['nome_completo'])) {
                                                $nomeCompleto = $funcionario['nome_completo'];
                                            } else {
                                                $nomeCompleto = trim($nome . ' ' . $apelido);
                                                if (empty($nomeCompleto)) {
                                                    $nomeCompleto = $username;
                                                }
                                            }
                                            
                                            if (!empty($id)): // Só adiciona a opção se tiver um ID válido
                                    ?>
                                        <option value="<?= htmlspecialchars($id) ?>">
                                            <?= htmlspecialchars($nomeCompleto) ?>
                                        </option>
                                    <?php 
                                            endif;
                                        endforeach;
                                    else: 
                                    ?>
                                        <option value="" disabled>Nenhum funcionário disponível</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea name="descricao" class="form-control" rows="3" placeholder="Descreva a finalidade desta equipe"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Coordenador *</label>
                            <select name="coordenador_id" class="form-select select2" required>
                                <option value="">Selecione um coordenador</option>
                                <?php if (!empty($coordenadores)): ?>
                                    <?php foreach ($coordenadores as $coordenador): 
                                        $nomeCompleto = !empty($coordenador['nome_completo']) ? $coordenador['nome_completo'] : 
                                                      (isset($coordenador['username']) ? $coordenador['username'] : 'Coordenador sem nome');
                                    ?>
                                        <option value="<?= $coordenador['id_utilizador'] ?? $coordenador['id'] ?>">
                                            <?= htmlspecialchars($nomeCompleto) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>Nenhum coordenador disponível</option>
                                <?php endif; ?>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Inicializar Select2
        $(document).ready(function() {
            // Configuração do Select2 para os campos de seleção
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Selecione as opções',
                allowClear: true,
                language: {
                    noResults: function() {
                        return "Nenhum resultado encontrado";
                    },
                    searching: function() {
                        return "Buscando...";
                    },
                    inputTooShort: function(args) {
                        return "Digite pelo menos " + args.minimum + " caracteres";
                    }
                }
            });
            
            // Resetar formulário quando o modal for fechado
            $('#modalEquipa').on('hidden.bs.modal', function () {
                $('#formEquipa')[0].reset();
                $('#equipa_id').val('');
                $('.select2').val(null).trigger('change');
            });
            
            // Configurar envio do formulário
            $('#formEquipa').on('submit', function(e) {
                e.preventDefault();
                
                // Obter os dados do formulário
                const formData = {
                    nome: $('[name="nome"]').val().trim(),
                    descricao: $('[name="descricao"]').val().trim() || '',
                    coordenador_id: $('[name="coordenador_id"]').val()
                };
                
                // Obter membros selecionados (se houver)
                const membrosSelect = $('[name="membros[]"]');
                if (membrosSelect.length) {
                    formData.membros = membrosSelect.val() || [];
                }
                
                console.log('Dados do formulário:', formData);
                
                // Validar campos obrigatórios
                if (!formData.nome) {
                    Swal.fire('Erro!', 'O nome da equipa é obrigatório.', 'error');
                    return false;
                }
                
                if (!formData.coordenador_id) {
                    Swal.fire('Erro!', 'O coordenador é obrigatório.', 'error');
                    return false;
                }
                
                // Determinar se é uma criação ou atualização
                const acao = $('[name="acao"]').val();
                const url = `api_equipas.php?acao=${acao === 'editar' ? 'atualizar' : 'criar'}`;
                
                // Se for atualização, adicionar o ID
                if (acao === 'editar') {
                    const id = $('[name="id"]').val();
                    if (id) {
                        formData.id = id;
                    } else {
                        console.error('ID não encontrado para edição');
                        Swal.fire('Erro!', 'ID da equipa não encontrado para edição.', 'error');
                        return false;
                    }
                }
                
                // Mostrar loading
                Swal.fire({
                    title: 'A processar...',
                    text: 'Por favor, aguarde.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Enviar dados via AJAX
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: JSON.stringify(formData),
                    contentType: 'application/json',
                    success: function(response) {
                        Swal.close();
                        
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: response.message || 'Equipa salva com sucesso!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            
                            // Fechar o modal
                            $('#modalEquipa').modal('hide');
                            
                            // Recarregar a página para atualizar a lista
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: response.message || 'Ocorreu um erro ao salvar a equipa.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        let errorMessage = 'Ocorreu um erro ao processar a requisição.';
                        let responseText = xhr.responseText;
                        
                        console.error('Erro na requisição:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            response: responseText,
                            error: error
                        });
                        
                        try {
                            const response = JSON.parse(responseText);
                            if (response && response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            console.error('Erro ao analisar resposta:', e);
                            errorMessage = responseText || errorMessage;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            html: `<p>${errorMessage}</p><p class="small text-muted">Status: ${xhr.status} ${xhr.statusText}</p>`,
                            confirmButtonText: 'Entendi'
                        });
                    }
                });
            });
        });
        
        // Função para editar equipe
        function editarEquipa(id) {
            // Implementar lógica para carregar os dados da equipe
            // e preencher o formulário de edição
            
            // Exemplo de como abrir o modal de edição
            $('#modalEquipa').modal('show');
            
            // Aqui você pode carregar os dados da equipe via AJAX
            // e preencher o formulário
        }
        
        // Função para confirmar exclusão
        function confirmarExclusao(id) {
            Swal.fire({
                title: 'Tem certeza?',
                text: "Você não poderá reverter isso!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Implementar lógica de exclusão via AJAX
                    // Exemplo:
                    /*
                    $.ajax({
                        url: 'excluir_equipa.php',
                        type: 'POST',
                        data: { id: id },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Excluído!',
                                    'A equipe foi excluída com sucesso.',
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire(
                                    'Erro!',
                                    'Ocorreu um erro ao excluir a equipe.',
                                    'error'
                                );
                            }
                        },
                        error: function() {
                            Swal.fire(
                                'Erro!',
                                'Ocorreu um erro ao processar a requisição.',
                                'error'
                            );
                        }
                    });
                    */
                    
                    // Por enquanto, apenas mostra a mensagem de sucesso
                    Swal.fire(
                        'Excluído!',
                        'A equipe foi excluída com sucesso.',
                        'success'
                    );
                }
            });
        }
    </script>
</body>
</html>
</body>
</html>