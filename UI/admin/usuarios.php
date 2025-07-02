<?php
session_start();

// Verifica se o usuário está logado e é administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['id_perfilacesso'] != 1) {
    header('Location: ../login.php');
    exit;
}

$page_title = "Gerenciar Usuários - Tlantic";
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        /* Sidebar and Main Content Layout */
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
                width: 100%;
            }
            .main-content.active {
                margin-left: 250px;
            }
        }
        
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --warning-color: #f8961e;
            --danger-color: #ef476f;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-600: #6c757d;
            --gray-800: #343a40;
        }
        
        body {
            background-color: #f5f7fb;
            color: #4a5568;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Estilos para cards */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 1.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top-left-radius: 10px !important;
            border-top-right-radius: 10px !important;
        }
        
        /* Estilos para botões */
        .btn {
            border-radius: 6px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn i {
            font-size: 1.1em;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #3a56d4;
            border-color: #3a56d4;
            transform: translateY(-1px);
        }
        
        .btn-outline-secondary {
            border-color: var(--gray-300);
            color: var(--gray-600);
        }
        
        .btn-outline-secondary:hover {
            background-color: var(--gray-100);
            color: var(--gray-800);
        }
        
        /* Estilos para a tabela */
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            border-top: none;
            border-bottom: 1px solid var(--gray-200);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: var(--gray-600);
            padding: 1rem 1.5rem;
        }
        
        .table tbody td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
            border-color: var(--gray-100);
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .table tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.03);
        }
        
        /* Badges de status */
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            border-radius: 6px;
        }
        
        .badge.bg-success {
            background-color: rgba(40, 167, 69, 0.1) !important;
            color: #28a745;
        }
        
        .badge.bg-warning {
            background-color: rgba(255, 193, 7, 0.1) !important;
            color: #ffc107;
        }
        
        .badge.bg-danger {
            background-color: rgba(220, 53, 69, 0.1) !important;
            color: #dc3545;
        }
        
        /* Cards de resumo */
        .summary-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        
        .summary-card:hover {
            transform: translateY(-3px);
        }
        
        .summary-card .card-body {
            padding: 1.5rem;
        }
        
        .summary-card .icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .summary-card .icon i {
            font-size: 1.5rem;
        }
        
        .summary-card .count {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .summary-card .title {
            color: var(--gray-600);
            font-size: 0.875rem;
            margin-bottom: 0;
        }
        
        /* Barra de pesquisa e filtros */
        .search-box {
            position: relative;
        }
        
        .search-box .form-control {
            padding-left: 2.5rem;
            border-radius: 6px;
            border: 1px solid var(--gray-300);
            height: calc(1.5em + 1rem + 2px);
        }
        
        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-600);
        }
        
        /* Paginação */
        .pagination {
            margin-bottom: 0;
        }
        
        .page-item .page-link {
            border: none;
            color: var(--gray-600);
            margin: 0 2px;
            border-radius: 6px;
            min-width: 32px;
            text-align: center;
            transition: all 0.2s;
        }
        
        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .page-item.disabled .page-link {
            color: var(--gray-400);
        }
        
        /* Ajustes responsivos */
        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .btn-add {
                width: 100%;
                justify-content: center;
            }
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
                <div class="d-flex justify-content-between align-items-center mb-4 p-4 bg-white rounded-3 shadow-sm">
                    <div>
                        <h1 class="h3 mb-1 fw-bold text-gray-800">Usuários</h1>
                        <p class="mb-0 text-muted">Gerencie os usuários e permissões do sistema</p>
                    </div>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novoUsuarioModal">
                            <i class='bx bx-plus me-2'></i> Adicionar Usuário
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="container-fluid px-4">
                    <!-- Cards de Estatísticas -->
                    <div class="row g-4 mb-4">
                        <!-- Total de Usuários -->
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="card summary-card border-left-primary h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="text-uppercase text-muted small fw-bold">Total de Usuários</span>
                                            <h2 class="count mt-1 mb-0">48</h2>
                                            <div class="mt-2">
                                                <span class="text-success small"><i class='bx bx-up-arrow-alt'></i> 12%</span>
                                                <span class="text-muted small ms-1">desde o mês passado</span>
                                            </div>
                                        </div>
                                        <div class="icon bg-primary bg-opacity-10 text-primary">
                                            <i class='bx bx-user'></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Usuários Ativos -->
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="card summary-card border-left-success h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="text-uppercase text-muted small fw-bold">Ativos</span>
                                            <h2 class="count mt-1 mb-0">42</h2>
                                            <div class="mt-2">
                                                <span class="text-success small"><i class='bx bx-up-arrow-alt'></i> 5%</span>
                                                <span class="text-muted small ms-1">desde o mês passado</span>
                                            </div>
                                        </div>
                                        <div class="icon bg-success bg-opacity-10 text-success">
                                            <i class='bx bx-check-circle'></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Usuários Inativos -->
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="card summary-card border-left-warning h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="text-uppercase text-muted small fw-bold">Inativos</span>
                                            <h2 class="count mt-1 mb-0">4</h2>
                                            <div class="mt-2">
                                                <span class="text-danger small"><i class='bx bx-down-arrow-alt'></i> 2%</span>
                                                <span class="text-muted small ms-1">desde o mês passado</span>
                                            </div>
                                        </div>
                                        <div class="icon bg-warning bg-opacity-10 text-warning">
                                            <i class='bx bx-time-five'></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Usuários Bloqueados -->
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="card summary-card border-left-danger h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="text-uppercase text-muted small fw-bold">Bloqueados</span>
                                            <h2 class="count mt-1 mb-0">2</h2>
                                            <div class="mt-2">
                                                <span class="text-success small"><i class='bx bx-down-arrow-alt'></i> 1%</span>
                                                <span class="text-muted small ms-1">desde o mês passado</span>
                                            </div>
                                        </div>
                                        <div class="icon bg-danger bg-opacity-10 text-danger">
                                            <i class='bx bx-lock-alt'></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <!-- Barra de Pesquisa e Filtros -->
                    <div class="card border-0 mb-4">
                        <div class="card-body p-4">
                            <form id="filtroUsuarios">
                                <div class="row g-3">
                                    <!-- Campo de Pesquisa -->
                                    <div class="col-12 col-md-4">
                                        <div class="search-box">
                                            <i class='bx bx-search'></i>
                                            <input type="text" class="form-control ps-4" placeholder="Pesquisar por nome, e-mail..." id="pesquisaGeral">
                                        </div>
                                    </div>
                                    
                                    <!-- Filtro de Status -->
                                    <div class="col-12 col-sm-6 col-md-2">
                                        <select class="form-select" id="filtroStatus">
                                            <option value="" selected>Todos os status</option>
                                            <option value="ativo">Ativos</option>
                                            <option value="inativo">Inativos</option>
                                            <option value="bloqueado">Bloqueados</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Filtro de Perfil -->
                                    <div class="col-12 col-sm-6 col-md-3">
                                        <select class="form-select" id="filtroPerfil">
                                            <option value="" selected>Todos os perfis</option>
                                            <option value="1">Administrador</option>
                                            <option value="2">Gerente</option>
                                            <option value="3">Coordenador</option>
                                            <option value="4">Colaborador</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Botões de Ação -->
                                    <div class="col-12 col-sm-6 col-md-3 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary flex-grow-1">
                                            <i class='bx bx-filter-alt me-1'></i> Aplicar Filtros
                                        </button>
                                        <button type="reset" class="btn btn-outline-secondary" id="limparFiltros">
                                            <i class='bx bx-x'></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Filtros Avançados (Opcional) -->
                                <div class="row mt-3" id="filtrosAvancados" style="display: none;">
                                    <div class="col-12 col-md-3">
                                        <label class="form-label small text-muted mb-1">Data de Criação</label>
                                        <input type="date" class="form-control" id="dataCriacao">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="form-label small text-muted mb-1">Último Acesso</label>
                                        <select class="form-select" id="ultimoAcesso">
                                            <option value="">Qualquer data</option>
                                            <option value="today">Hoje</option>
                                            <option value="week">Últimos 7 dias</option>
                                            <option value="month">Últimos 30 dias</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <a href="#" class="small text-primary" id="toggleFiltros">
                                        <i class='bx bx-chevron-down me-1'></i> Mais filtros
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tabela de Usuários -->
                    <div class="card border-0">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0">Lista de Usuários</h5>
                            <div class="d-flex">
                                <button class="btn btn-sm btn-outline-secondary me-2">
                                    <i class='bx bx-export me-1'></i> Exportar
                                </button>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class='bx bx-dots-horizontal-rounded'></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                                        <li><a class="dropdown-item" href="#"><i class='bx bx-show me-2'></i>Visualizar Colunas</a></li>
                                        <li><a class="dropdown-item" href="#"><i class='bx bx-download me-2'></i>Exportar Dados</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#"><i class='bx bx-reset me-2'></i>Redefinir Filtros</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="usuariosTable" class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>Usuário</th>
                                            <th>E-mail</th>
                                            <th>Perfil</th>
                                            <th>Status</th>
                                            <th>Último Acesso</th>
                                            <th class="text-end" style="width: 120px;">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar bg-primary text-white rounded-circle me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                        <span>AD</span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">Admin User</div>
                                                        <small class="text-muted">admin</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>admin@tlantic.com</td>
                                            <td><span class="badge bg-primary bg-opacity-10 text-primary">Administrador</span></td>
                                            <td><span class="badge bg-success bg-opacity-10 text-success">Ativo</span></td>
                                            <td><small class="text-muted">Hoje, 14:30</small></td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Visualizar">
                                                        <i class='bx bx-show'></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Editar">
                                                        <i class='bx bx-edit'></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Desativar">
                                                        <i class='bx bx-user-x'></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Adicione mais linhas conforme necessário -->
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Paginação -->
                            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                                <div class="text-muted small">
                                    Mostrando <strong>1</strong> a <strong>10</strong> de <strong>48</strong> registros
                                </div>
                                <nav>
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1">Anterior</a>
                                        </li>
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                                        <li class="page-item">
                                            <a class="page-link" href="#">Próximo</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    Tem certeza de que deseja excluir este usuário? Esta ação não pode ser desfeita.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Excluir</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicialização da DataTable
            var table = $('#usuariosTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-PT.json'
                },
                responsive: true,
                order: [[0, 'desc']],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
            });

            // Pesquisa personalizada
            $('#searchInput').keyup(function(){
                table.search($(this).val()).draw();
            });

            // Confirmação de exclusão
            $('.btn-danger').on('click', function() {
                var deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                deleteModal.show();
                
                // Armazena o ID do usuário a ser excluído
                var userId = $(this).data('user-id');
                
                $('#confirmDeleteBtn').off('click').on('click', function() {
                    // Aqui você adicionaria a lógica para excluir o usuário
                    console.log('Excluindo usuário ID:', userId);
                    // Simulando uma requisição AJAX
                    setTimeout(function() {
                        // Atualiza a tabela após a exclusão
                        table.row($(this).parents('tr')).remove().draw();
                        deleteModal.hide();
                        
                        // Mostra uma mensagem de sucesso
                        alert('Usuário excluído com sucesso!');
                    }, 1000);
                });
            });
        });
    </script>
</body>
</html>
