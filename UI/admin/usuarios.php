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
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --warning-color: #f8961e;
            --danger-color: #ef476f;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        body {
            background-color: #f5f7fb;
            color: #4a5568;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e3e6f0;
            padding: 1.25rem 1.5rem;
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #eee;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-add {
            background: #2c3e50;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .btn-add:hover {
            background: #34495e;
            color: white;
        }
        .action-btns .btn {
            margin: 0 2px;
            padding: 2px 8px;
            font-size: 0.9em;
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
                <div class="d-flex justify-content-between align-items-center mb-4 p-4 bg-white shadow-sm">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800">Usuários</h1>
                        <p class="mb-0">Gerencie os usuários do sistema</p>
                    </div>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novoUsuarioModal">
                            <i class='bx bx-plus me-2'></i> Novo Usuário
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="container-fluid px-4">
                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary bg-opacity-10 border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-primary mb-1">Total de Usuários</h6>
                                            <h2 class="mb-0">48</h2>
                                        </div>
                                        <div class="bg-primary bg-opacity-25 p-3 rounded-circle">
                                            <i class='bx bx-user text-primary' style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success bg-opacity-10 border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-success mb-1">Ativos</h6>
                                            <h2 class="mb-0">42</h2>
                                        </div>
                                        <div class="bg-success bg-opacity-25 p-3 rounded-circle">
                                            <i class='bx bx-check-circle text-success' style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning bg-opacity-10 border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-warning mb-1">Inativos</h6>
                                            <h2 class="mb-0">6</h2>
                                        </div>
                                        <div class="bg-warning bg-opacity-25 p-3 rounded-circle">
                                            <i class='bx bx-user-x text-warning' style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info bg-opacity-10 border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-info mb-1">Novos (7d)</h6>
                                            <h2 class="mb-0">5</h2>
                                        </div>
                                        <div class="bg-info bg-opacity-25 p-3 rounded-circle">
                                            <i class='bx bx-user-plus text-info' style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="card border-0 mb-4">
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent"><i class='bx bx-search'></i></span>
                                        <input type="text" class="form-control" placeholder="Pesquisar usuários..." id="searchInput">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" id="statusFilter">
                                        <option value="">Todos os status</option>
                                        <option value="ativo">Ativo</option>
                                        <option value="inativo">Inativo</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" id="perfilFilter">
                                        <option value="">Todos os perfis</option>
                                        <option>Administrador</option>
                                        <option>Gerente</option>
                                        <option>Usuário</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-outline-primary w-100">
                                        <i class='bx bx-filter-alt me-1'></i> Filtrar
                                    </button>
                                </div>
                            </div>
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
