<?php
session_start();

// Verifica se o usuário está logado e tem permissão (admin ou RH)
if (!isset($_SESSION['utilizador_id']) || ($_SESSION['id_perfilacesso'] != 1 && $_SESSION['id_perfilacesso'] != 2)) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../BLL/PerfilAcessoBLL.php';
require_once __DIR__ . '/../../BLL/UtilizadorBLL.php';

$perfilBLL = new PerfilAcessoBLL();
$utilizadorBLL = new UtilizadorBLL();

// Obter lista de perfis
$perfis = $perfilBLL->listarTodos();

// Contar usuários por perfil
$contagemUsuarios = [];
foreach ($perfis as $perfil) {
    $contagemUsuarios[$perfil['id_perfilacesso']] = $utilizadorBLL->contarPorPerfil($perfil['id_perfilacesso']);
}

$page_title = "Gerenciar Perfis de Acesso";
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - Tlantic</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #eef2ff;
            --secondary: #6c757d;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #ef476f;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        body {
            background-color: #f5f7fb;
            color: #4a5568;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Estilo da barra lateral */
        .sidebar {
            width: 250px !important;
            background-color: #1a1a2e;
            color: #fff;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 1040;
            overflow-y: auto;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Conteúdo principal */
        .main-content {
            margin-left: 250px;
            flex: 1;
            min-height: 100vh;
            transition: all 0.3s ease-in-out;
            background-color: #f5f7fb;
            position: relative;
        }
        
        /* Overlay para telas menores */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1039;
            transition: all 0.3s ease-in-out;
        }
        
        /* Ajustes para telas menores */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                margin-left: 0;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .sidebar.active + .sidebar-overlay {
                display: block;
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            body.sidebar-active {
                overflow: hidden;
            }
        }
        
        /* Estilo para o cabeçalho fixo */
        .page-header {
            position: sticky;
            top: 0;
            z-index: 100;
            background-color: #fff;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        /* Ajuste para o container principal */
        .container-fluid {
            padding: 1.5rem;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e3e6f0;
            padding: 1.25rem 1.5rem;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .form-control, .form-select, .select2-selection {
            border-radius: 0.35rem;
            padding: 0.5rem 0.75rem;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: #3a56d4;
            border-color: #3a56d4;
        }
        
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
        
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }
        
        .bg-success-light {
            background-color: rgba(40, 167, 69, 0.1) !important;
            color: #28a745 !important;
        }
        
        .bg-danger-light {
            background-color: rgba(239, 71, 111, 0.1) !important;
            color: #ef476f !important;
        }
        
        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.1) !important;
            color: #ffc107 !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <!-- Sidebar -->
        <div class="sidebar">
            <?php include 'includes/sidebar.php'; ?>
        </div>
        
        <!-- Overlay para fechar a sidebar -->
        <div class="sidebar-overlay"></div>

        <!-- Main Content -->
        <main class="main-content">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 p-4 bg-white shadow-sm">
                    <div>
                        <h1 class="h3 mb-1">Gerenciar Perfis de Acesso</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Perfis de Acesso</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-secondary me-2 d-lg-none" id="sidebarToggle">
                            <i class='bx bx-menu'></i>
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novoPerfilModal">
                            <i class='bx bx-plus-circle me-2'></i>Novo Perfil
                        </button>
                    </div>
                </div>

                <div class="container-fluid px-4">
                    <!-- Cards de Estatísticas -->
                    <div class="row g-4 mb-4">
                        <!-- Total de Perfis -->
                        <div class="col-md-3">
                            <div class="card bg-primary bg-opacity-10 border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                            <i class='bx bx-group text-primary' style="font-size: 1.75rem;"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 text-muted">Total de Perfis</h6>
                                            <h3 class="mb-0"><?= count($perfis) ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Perfis Ativos -->
                        <div class="col-md-3">
                            <div class="card bg-success bg-opacity-10 border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                            <i class='bx bx-check-circle text-success' style="font-size: 1.75rem;"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 text-muted">Perfis Ativos</h6>
                                            <h3 class="mb-0">
                                                <?= count(array_filter($perfis, fn($p) => $p['ativo'])) ?>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total de Usuários -->
                        <div class="col-md-3">
                            <div class="card bg-info bg-opacity-10 border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper rounded-circle bg-info bg-opacity-10 p-3 me-3">
                                            <i class='bx bx-user text-info' style="font-size: 1.75rem;"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 text-muted">Total de Usuários</h6>
                                            <h3 class="mb-0"><?= array_sum($contagemUsuarios) ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Última Atualização -->
                        <div class="col-md-3">
                            <div class="card bg-warning bg-opacity-10 border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                            <i class='bx bx-time-five text-warning' style="font-size: 1.75rem;"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 text-muted">Última Atualização</h6>
                                            <h6 class="mb-0">
                                                <?= date('d/m/Y H:i') ?>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabela de Perfis -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Lista de Perfis</h5>
                                <div class="d-flex">
                                    <div class="input-group input-group-sm" style="width: 250px;">
                                        <span class="input-group-text bg-transparent"><i class='bx bx-search'></i></span>
                                        <input type="text" class="form-control" id="searchInput" placeholder="Pesquisar perfis...">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="perfisTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Nome do Perfil</th>
                                            <th>Descrição</th>
                                            <th class="text-center">Usuários</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-end">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($perfis as $perfil): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="icon-wrapper bg-<?= $perfil['ativo'] ? 'primary' : 'secondary' ?>-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                            <i class='bx bx-<?= $perfil['icone'] ?? 'user' ?> text-<?= $perfil['ativo'] ? 'primary' : 'secondary' ?>'></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0"><?= htmlspecialchars($perfil['nome_perfil']) ?></h6>
                                                            <small class="text-muted">ID: <?= $perfil['id_perfilacesso'] ?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?= htmlspecialchars($perfil['descricao'] ?? 'Sem descrição') ?></td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                                        <?= $contagemUsuarios[$perfil['id_perfilacesso']] ?? 0 ?> usuário(s)
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="form-check form-switch d-inline-block">
                                                        <input class="form-check-input toggle-status" type="checkbox" 
                                                               role="switch" id="status<?= $perfil['id_perfilacesso'] ?>" 
                                                               data-id="<?= $perfil['id_perfilacesso'] ?>" 
                                                               <?= $perfil['ativo'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="status<?= $perfil['id_perfilacesso'] ?>">
                                                            <span class="status-badge badge bg-<?= $perfil['ativo'] ? 'success' : 'secondary' ?>">
                                                                <?= $perfil['ativo'] ? 'Ativo' : 'Inativo' ?>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                                type="button" id="dropdownMenuButton<?= $perfil['id_perfilacesso'] ?>" 
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class='bx bx-dots-horizontal-rounded'></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end" 
                                                            aria-labelledby="dropdownMenuButton<?= $perfil['id_perfilacesso'] ?>">
                                                            <li>
                                                                <a class="dropdown-item btn-editar" href="#" 
                                                                   data-id="<?= $perfil['id_perfilacesso'] ?>"
                                                                   data-nome="<?= htmlspecialchars($perfil['nome_perfil']) ?>"
                                                                   data-descricao="<?= htmlspecialchars($perfil['descricao'] ?? '') ?>"
                                                                   data-icone="<?= $perfil['icone'] ?? 'user' ?>"
                                                                   data-ativo="<?= $perfil['ativo'] ? 'true' : 'false' ?>">
                                                                    <i class='bx bx-edit-alt me-2'></i>Editar
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item text-danger btn-delete" 
                                                                   href="#" 
                                                                   data-id="<?= $perfil['id_perfilacesso'] ?>" 
                                                                   data-nome="<?= htmlspecialchars($perfil['nome_perfil']) ?>">
                                                                    <i class='bx bx-trash me-2'></i>Excluir
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item" 
                                                                   href="permissoes.php?perfil=<?= $perfil['id_perfilacesso'] ?>">
                                                                    <i class='bx bx-lock me-2'></i>Gerenciar Permissões
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
    </div>

    <!-- Modal Novo Perfil -->
    <div class="modal fade" id="novoPerfilModal" tabindex="-1" aria-labelledby="novoPerfilModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="novoPerfilModalLabel">Novo Perfil de Acesso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="formNovoPerfil" action="processa_perfil.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nomePerfil" class="form-label">Nome do Perfil <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nomePerfil" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="descricaoPerfil" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricaoPerfil" name="descricao" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ícone</label>
                            <div class="d-flex flex-wrap gap-2" id="iconesContainer">
                                <?php
                                $icones = ['user', 'user-pin', 'user-check', 'user-voice', 'user-x', 'user-plus', 'user-minus'];
                                foreach ($icones as $icone): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="icone" id="icone_<?= $icone ?>" 
                                               value="<?= $icone ?>" <?= $icone === 'user' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="icone_<?= $icone ?>">
                                            <i class='bx bx-<?= $icone ?>'></i>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="ativoPerfil" name="ativo" checked>
                            <label class="form-check-label" for="ativoPerfil">Ativo</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Perfil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Perfil -->
    <div class="modal fade" id="editarPerfilModal" tabindex="-1" aria-labelledby="editarPerfilModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarPerfilModalLabel">Editar Perfil de Acesso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="formEditarPerfil" action="processa_perfil.php" method="POST">
                    <input type="hidden" id="editarPerfilId" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editarNomePerfil" class="form-label">Nome do Perfil <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editarNomePerfil" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="editarDescricaoPerfil" class="form-label">Descrição</label>
                            <textarea class="form-control" id="editarDescricaoPerfil" name="descricao" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ícone</label>
                            <div class="d-flex flex-wrap gap-2" id="editarIconesContainer">
                                <?php
                                $icones = ['user', 'user-pin', 'user-check', 'user-voice', 'user-x', 'user-plus', 'user-minus'];
                                foreach ($icones as $icone): ?>
                                    <div class="form-check">
                                        <input class="form-check-input icone-radio" type="radio" name="icone" id="editarIcone_<?= $icone ?>" 
                                               value="<?= $icone ?>">
                                        <label class="form-check-label" for="editarIcone_<?= $icone ?>">
                                            <i class='bx bx-<?= $icone ?>'></i>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="editarAtivoPerfil" name="ativo">
                            <label class="form-check-label" for="editarAtivoPerfil">Ativo</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Atualizar Perfil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // Toggle da sidebar em telas pequenas
            $('#sidebarToggle').on('click', function(e) {
                e.stopPropagation();
                $('.sidebar').toggleClass('active');
                $('body').toggleClass('sidebar-active');
            });

            // Fechar a sidebar ao clicar no overlay
            $('.sidebar-overlay').on('click', function() {
                $('.sidebar').removeClass('active');
                $('body').removeClass('sidebar-active');
            });

            // Fechar a sidebar ao clicar em um link
            $('.sidebar .nav-link').on('click', function() {
                if ($(window).width() < 992) {
                    $('.sidebar').removeClass('active');
                    $('body').removeClass('sidebar-active');
                }
            });

            // Ajustar o layout quando a janela for redimensionada
            function handleResize() {
                if ($(window).width() >= 992) {
                    $('.sidebar').removeClass('active');
                    $('body').removeClass('sidebar-active');
                }
            }

            // Executar ao carregar e ao redimensionar a janela
            $(window).on('resize', handleResize);
            handleResize();
            // Manipular clique no botão de editar perfil
            $(document).on('click', '.btn-editar', function(e) {
                e.preventDefault();
                
                // Obter os dados do perfil dos atributos data
                const perfilId = $(this).data('id');
                const nomePerfil = $(this).data('nome');
                const descricao = $(this).data('descricao');
                const icone = $(this).data('icone');
                const ativo = $(this).data('ativo') === 'true';
                
                // Preencher o formulário de edição
                $('#editarPerfilId').val(perfilId);
                $('#editarNomePerfil').val(nomePerfil);
                $('#editarDescricaoPerfil').val(descricao);
                
                // Marcar o ícone correto
                $(`#editarIconesContainer input[value="${icone}"]`).prop('checked', true);
                
                // Definir o estado do switch de ativo
                $('#editarAtivoPerfil').prop('checked', ativo);
                
                // Exibir o modal
                const editarModal = new bootstrap.Modal(document.getElementById('editarPerfilModal'));
                editarModal.show();
            });
            
            // Manipular envio do formulário de edição
            $('#formEditarPerfil').on('submit', function(e) {
                e.preventDefault();
                
                // Aqui você pode adicionar validação adicional se necessário
                
                // Enviar o formulário via AJAX
                $.ajax({
                    url: 'processa_perfil.php',
                    type: 'POST',
                    data: $(this).serialize() + '&acao=atualizar',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Fechar o modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editarPerfilModal'));
                            modal.hide();
                            
                            // Exibir mensagem de sucesso
                            Swal.fire({
                                title: 'Sucesso!',
                                text: response.message || 'Perfil atualizado com sucesso!',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Recarregar a página para atualizar a lista
                                window.location.reload();
                            });
                        } else {
                            // Exibir mensagem de erro
                            Swal.fire({
                                title: 'Erro!',
                                text: response.message || 'Ocorreu um erro ao atualizar o perfil.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function() {
                        // Exibir mensagem de erro genérica
                        Swal.fire({
                            title: 'Erro!',
                            text: 'Ocorreu um erro ao atualizar o perfil. Tente novamente mais tarde.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
            
            // Inicialização do DataTable
            const table = $('#perfisTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-PT.json',
                    search: "",
                    searchPlaceholder: "Pesquisar perfis..."
                },
                order: [[0, 'asc']],
                pageLength: 10,
                responsive: true,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                columnDefs: [
                    { orderable: false, targets: [3, 4] } // Desabilita ordenação nas colunas de status e ações
                ]
            });

            // Alternar status do perfil
            $(document).on('change', '.toggle-status', function() {
                const perfilId = $(this).data('id');
                const isAtivo = $(this).is(':checked');
                const $statusBadge = $(this).closest('tr').find('.status-badge');
                
                // Simulação de atualização (substitua por chamada AJAX real)
                if (isAtivo) {
                    $statusBadge.removeClass('bg-secondary').addClass('bg-success').text('Ativo');
                    
                    // Atualiza o ícone do perfil na tabela
                    const $icon = $(this).closest('tr').find('.icon-wrapper i');
                    $icon.removeClass('text-secondary').addClass('text-primary');
                    $icon.closest('.icon-wrapper').removeClass('bg-secondary-light').addClass('bg-primary-light');
                } else {
                    $statusBadge.removeClass('bg-success').addClass('bg-secondary').text('Inativo');
                    
                    // Atualiza o ícone do perfil na tabela
                    const $icon = $(this).closest('tr').find('.icon-wrapper i');
                    $icon.removeClass('text-primary').addClass('text-secondary');
                    $icon.closest('.icon-wrapper').removeClass('bg-primary-light').addClass('bg-secondary-light');
                }
                
                // Exemplo de como seria a chamada AJAX real:
                /*
                $.ajax({
                    url: 'atualiza_status_perfil.php',
                    type: 'POST',
                    data: {
                        id: perfilId,
                        ativo: isAtivo ? 1 : 0
                    },
                    success: function(response) {
                        if (!response.success) {
                            // Reverte a mudança em caso de erro
                            $(this).prop('checked', !isAtivo);
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: response.message || 'Ocorreu um erro ao atualizar o status do perfil.'
                            });
                        }
                    },
                    error: function() {
                        // Reverte a mudança em caso de erro
                        $(this).prop('checked', !isAtivo);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Não foi possível conectar ao servidor. Tente novamente.'
                        });
                    }
                });
                */
            });

            // Confirmação de exclusão
            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                const perfilId = $(this).data('id');
                const perfilNome = $(this).data('nome');
                const $row = $(this).closest('tr');
                
                Swal.fire({
                    title: 'Tem certeza?',
                    text: `Você está prestes a excluir o perfil "${perfilNome}". Esta ação não pode ser desfeita!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#4361ee',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mostra o loading
                        Swal.fire({
                            title: 'Excluindo...',
                            text: 'Por favor, aguarde.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        // Faz a chamada AJAX para excluir o perfil
                        $.ajax({
                            url: 'excluir_perfil.php',
                            type: 'POST',
                            data: { id: perfilId },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Sucesso!',
                                        text: response.message || 'Perfil excluído com sucesso.',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        // Recarrega a página para atualizar a lista
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Erro!',
                                        text: response.message || 'Ocorreu um erro ao excluir o perfil.'
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Erro na requisição:', status, error);
                                let errorMessage = 'Não foi possível conectar ao servidor. Tente novamente.';
                                
                                try {
                                    const response = xhr.responseJSON;
                                    if (response && response.message) {
                                        errorMessage = response.message;
                                    }
                                } catch (e) {
                                    console.error('Erro ao processar resposta de erro:', e);
                                }
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro!',
                                    text: errorMessage
                                });
                            }
                        });
                    }
                });
            });

            // Validação do formulário de novo perfil
            $('#formNovoPerfil').on('submit', function(e) {
                e.preventDefault();
                
                const nomePerfil = $('#nomePerfil').val().trim();
                
                if (!nomePerfil) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Campo obrigatório',
                        text: 'Por favor, insira um nome para o perfil.'
                    });
                    return false;
                }
                
                // Mostra o loading
                Swal.fire({
                    title: 'Salvando...',
                    text: 'Por favor, aguarde.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Prepara os dados do formulário
                const formData = $(this).serialize();
                
                // Envia os dados via AJAX
                $.ajax({
                    url: 'processa_perfil.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: response.message || 'Perfil criado com sucesso!',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                // Fecha o modal e recarrega a página
                                $('#novoPerfilModal').modal('hide');
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: response.message || 'Ocorreu um erro ao criar o perfil.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro na requisição:', status, error);
                        let errorMessage = 'Não foi possível conectar ao servidor. Tente novamente.';
                        
                        try {
                            const response = xhr.responseJSON;
                            if (response && response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            console.error('Erro ao processar resposta de erro:', e);
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: errorMessage
                        });
                    },
                    complete: function() {
                        // Esconde o loading
                        Swal.close();
                    }
                });
                return false;
            });

            // Fechar modal ao clicar fora
            $('.modal').on('click', function(e) {
                if ($(e.target).hasClass('modal')) {
                    $(this).modal('hide');
                }
            });

            // Limpar formulário ao fechar o modal
            $('#novoPerfilModal').on('hidden.bs.modal', function () {
                $(this).find('form').trigger('reset');
            });

            // Inicialização do Select2 (se necessário)
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });
    </script>
</body>
</html>
