<?php
session_start();

// Verifica se o usuário está logado e é administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['id_perfilacesso'] != 1) {
    header('Location: ../login.php');
    exit;
}

$page_title = "Gerenciar Perfis - Tlantic";
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
            --primary: #4361ee;
            --primary-light: #eef2ff;
            --secondary: #6c757d;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #ef476f;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-400: #ced4da;
            --gray-500: #adb5bd;
            --gray-600: #6c757d;
            --gray-700: #495057;
            --gray-800: #343a40;
            --gray-900: #212529;
        }
        
        body {
            background-color: #f5f7fb;
            color: #4a5568;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
        }
        
        /* Card Styles */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e3e6f0;
            padding: 1.25rem 1.5rem;
            border-top-left-radius: 10px !important;
            border-top-right-radius: 10px !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Table Styles */
        .table th {
            border-top: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #6c757d;
            background-color: #f8f9fc;
            padding: 1rem;
        }
        
        .table > :not(:first-child) {
            border-top: none;
        }
        
        .table > :not(caption) > * > * {
            padding: 0.75rem 1rem;
            vertical-align: middle;
        }
        
        .table-hover > tbody > tr:hover {
            background-color: #f8f9fc;
        }
        
        /* Sidebar Styles */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1a2a3a 0%, #2c3e50 100%);
            color: #fff;
            padding: 20px 0;
            position: fixed;
            width: 16.666667%;
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .sidebar-brand {
            padding: 0 1.5rem 1.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-brand img {
            max-height: 40px;
        }
        
        .sidebar-nav {
            padding: 0 1rem;
            overflow-y: auto;
            height: calc(100vh - 100px);
        }
        
        .sidebar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            transition: all 0.2s;
            font-size: 0.9rem;
        }
        
        .sidebar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            text-decoration: none;
        }
        
        .sidebar-nav .nav-link i {
            font-size: 1.25rem;
            margin-right: 0.75rem;
            width: 24px;
            text-align: center;
        }
        
        .sidebar-nav .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            font-weight: 500;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 2rem;
            min-height: 100vh;
            transition: all 0.3s;
        }
        
        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a202c;
            margin: 0;
        }
        
        .page-subtitle {
            color: #718096;
            margin: 0.5rem 0 0;
            font-size: 1rem;
        }
        
        /* Card Styles */
        /* Status Badges */
        .status-badge {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        
        .status-active { background-color: var(--success); }
        .status-inactive { background-color: var(--danger); }
        
        /* Avatar */
        .avatar-sm {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        /* Buttons */
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }
        
        /* Stats Cards */
        .stat-card {
            border-left: 4px solid;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .stat-card.primary {
            border-left-color: var(--primary);
        }
        
        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        
        .stat-card.primary .stat-icon {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary);
        }
        
        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a202c;
            line-height: 1.2;
        }
        
        .stat-card .stat-label {
            font-size: 0.875rem;
            color: #718096;
        }
        
        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 1.25rem 1.5rem;
            border-top-left-radius: 0.75rem !important;
            border-top-right-radius: 0.75rem !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a202c;
            margin: 0;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Stats Cards */
        .stat-card {
            border-left: 4px solid;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .stat-card.primary {
            border-left-color: var(--primary-color);
        }
        
        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        
        .stat-card.primary .stat-icon {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
        }
        
        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a202c;
            line-height: 1.2;
        }
        
        .stat-card .stat-label {
            font-size: 0.875rem;
            color: #718096;
            margin-top: 0.25rem;
        }
        
        /* Table Styles */
        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            color: #6b7280;
            background-color: #f9fafb;
            border-bottom-width: 1px;
        }
        
        /* Permission Groups */
        .permission-group {
            background: #f9fafb;
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
        }
        
        .permission-group h5 {
            font-size: 1rem;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .permission-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            background: #fff;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            margin-bottom: 0.75rem;
            transition: all 0.2s;
        }
        
        .permission-item:hover {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transform: translateY(-1px);
        }
        
        /* Responsive Styles */
        @media (max-width: 991.98px) {
            .sidebar {
                left: -280px;
            }
            
            .sidebar.show {
                left: 0;
                box-shadow: 5px 0 15px rgba(0, 0, 0, 0.1);
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
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
                <div class="d-flex justify-content-between align-items-center mb-4 p-4 bg-white shadow-sm">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800">Perfis de Acesso</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Perfis</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novoPerfilModal">
                            <i class='bx bx-plus me-2'></i>Novo Perfil
                        </button>
                    </div>
                </div>

                <!-- Conteúdo principal -->
                <div class="container-fluid px-4">
                    <!-- Cards de Estatísticas -->
                    <div class="row g-4 mb-4">
                        <!-- Total de Perfis -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-muted mb-2 small">Total de Perfis</h6>
                                            <h2 class="mb-0">8</h2>
                                        </div>
                                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                            <i class='bx bx-group text-primary' style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Perfis Ativos -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-muted mb-2 small">Ativos</h6>
                                            <h2 class="mb-0">7</h2>
                                        </div>
                                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                                            <i class='bx bx-check-circle text-success' style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Perfis Inativos -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-muted mb-2 small">Inativos</h6>
                                            <h2 class="mb-0">1</h2>
                                        </div>
                                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                                            <i class='bx bx-x-circle text-warning' style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Total de Permissões -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-muted mb-2 small">Permissões</h6>
                                            <h2 class="mb-0">24</h2>
                                        </div>
                                        <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                                            <i class='bx bx-shield-alt-2 text-info' style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Barra de Pesquisa e Filtros -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-3">
                            <form class="row g-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0">
                                            <i class='bx bx-search text-muted'></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0" placeholder="Pesquisar perfis...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select">
                                        <option value="">Todos os status</option>
                                        <option value="1">Ativo</option>
                                        <option value="0">Inativo</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select">
                                        <option value="">Todas as permissões</option>
                                        <option value="admin">Administrador</option>
                                        <option value="editor">Editor</option>
                                        <option value="visualizador">Visualizador</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class='bx bx-filter-alt me-1'></i>Filtrar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tabela de Perfis -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" id="perfisTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="border-0">Perfil</th>
                                            <th class="border-0">Descrição</th>
                                            <th class="border-0 text-center">Usuários</th>
                                            <th class="border-0 text-center">Status</th>
                                            <th class="border-0 text-end">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                        <i class='bx bxs-user-check text-primary'></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">Administrador</h6>
                                                        <small class="text-muted">admin</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Acesso total ao sistema</td>
                                            <td class="text-center">3</td>
                                            <td class="text-center">
                                                <span class="badge bg-success">Ativo</span>
                                            </td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-link text-primary p-1" title="Editar">
                                                    <i class='bx bx-edit-alt fs-5'></i>
                                                </button>
                                                <button class="btn btn-sm btn-link text-danger p-1" title="Excluir">
                                                    <i class='bx bx-trash fs-5'></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                        <i class='bx bxs-edit text-success'></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">Editor</h6>
                                                        <small class="text-muted">editor</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Pode criar e editar conteúdo</td>
                                            <td class="text-center">5</td>
                                            <td class="text-center">
                                                <span class="badge bg-success">Ativo</span>
                                            </td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-link text-primary p-1" title="Editar">
                                                    <i class='bx bx-edit-alt fs-5'></i>
                                                </button>
                                                <button class="btn btn-sm btn-link text-danger p-1" title="Excluir">
                                                    <i class='bx bx-trash fs-5'></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                        <i class='bx bxs-show text-warning'></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">Visualizador</h6>
                                                        <small class="text-muted">viewer</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Apenas visualização</td>
                                            <td class="text-center">12</td>
                                            <td class="text-center">
                                                <span class="badge bg-success">Ativo</span>
                                            </td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-link text-primary p-1" title="Editar">
                                                    <i class='bx bx-edit-alt fs-5'></i>
                                                </button>
                                                <button class="btn btn-sm btn-link text-danger p-1" title="Excluir">
                                                    <i class='bx bx-trash fs-5'></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-wrapper bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                        <i class='bx bxs-lock-alt text-danger'></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">Restrito</h6>
                                                        <small class="text-muted">restricted</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>Acesso limitado</td>
                                            <td class="text-center">2</td>
                                            <td class="text-center">
                                                <span class="badge bg-danger">Inativo</span>
                                            </td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-link text-primary p-1" title="Editar">
                                                    <i class='bx bx-edit-alt fs-5'></i>
                                                </button>
                                                <button class="btn btn-sm btn-link text-danger p-1" title="Excluir">
                                                    <i class='bx bx-trash fs-5'></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3">
                            <nav aria-label="Navegação da tabela">
                                <ul class="pagination justify-content-end mb-0">
                                    <li class="page-item disabled">
                                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a>
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

                <!-- Footer -->
                <footer class="footer mt-auto py-3 bg-light">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">© 2023 Tlantic. Todos os direitos reservados.</div>
                            <div>
                                <a href="#" class="text-decoration-none">Política de Privacidade</a>
                                <span class="mx-2">|</span>
                                <a href="#" class="text-decoration-none">Termos de Uso</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicialização do DataTable
            $('#perfisTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-PT.json',
                    search: "",
                    searchPlaceholder: "Pesquisar...",
                    lengthMenu: "Mostrar _MENU_ registros por página",
                    zeroRecords: "Nenhum registro encontrado",
                    info: "Mostrando página _PAGE_ de _PAGES_",
                    infoEmpty: "Nenhum registro disponível",
                    infoFiltered: "(filtrado de _MAX_ registros totais)",
                    paginate: {
                        first: "Primeira",
                        last: "Última",
                        next: "Próxima",
                        previous: "Anterior"
                    }
                },
                responsive: true,
                order: [[0, 'asc']],
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                columnDefs: [
                    { orderable: false, targets: [4] } // Desabilita ordenação na coluna de ações
                ]
            });

            // Inicialização do Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Selecione uma opção',
                allowClear: true
            });

            // Toggle para ativar/desativar perfil
            $('.toggle-status').change(function() {
                const perfilId = $(this).data('id');
                const isActive = $(this).is(':checked');
                // Aqui você pode adicionar a lógica para atualizar o status no banco de dados
                console.log(`Perfil ${perfilId} - Ativo: ${isActive}`);
                
                // Exemplo de feedback visual
                const statusBadge = $(this).closest('tr').find('.status-badge');
                if (isActive) {
                    statusBadge.removeClass('bg-secondary').addClass('bg-success').text('Ativo');
                } else {
                    statusBadge.removeClass('bg-success').addClass('bg-secondary').text('Inativo');
                }
            });

            // Confirmação antes de excluir
            $('.btn-delete').click(function(e) {
                e.preventDefault();
                const perfilNome = $(this).data('nome');
                
                if (confirm(`Tem certeza que deseja excluir o perfil "${perfilNome}"? Esta ação não pode ser desfeita.`)) {
                    // Aqui você pode adicionar a lógica para excluir o perfil
                    console.log(`Excluindo perfil: ${perfilNome}`);
                    // Exemplo de remoção da linha da tabela
                    $(this).closest('tr').fadeOut(300, function() {
                        $(this).remove();
                    });
                }
            });

            // Abrir modal de edição
            $('.btn-edit').click(function() {
                const perfilId = $(this).data('id');
                // Aqui você pode carregar os dados do perfil via AJAX e preencher o modal de edição
                console.log(`Editando perfil ID: ${perfilId}`);
                $('#editarPerfilModal').modal('show');
            });
        });
    </script>
    </div>          <main class="main-content">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 p-4 bg-white shadow-sm">
                    <div>
                        <h1 class="h3 mb-1 text-gray-800">Perfis de Acesso</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Perfis</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novoPerfilModal">
                            <i class='bx bx-plus me-2'></i>Novo Perfil
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
                                            <h6 class="text-uppercase text-primary mb-1">Total de Perfis</h6>
                                            <h2 class="mb-0">5</h2>
                                        </div>
                                        <div class="bg-primary bg-opacity-25 p-3 rounded-circle">
                                            <i class='bx bx-group text-primary' style="font-size: 1.5rem;"></i>
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
                                            <h2 class="mb-0">4</h2>
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
                                            <h2 class="mb-0">1</h2>
                                        </div>
                                        <div class="bg-warning bg-opacity-25 p-3 rounded-circle">
                                            <i class='bx bx-x-circle text-warning' style="font-size: 1.5rem;"></i>
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
                                            <h6 class="text-uppercase text-info mb-1">Permissões</h6>
                                            <h2 class="mb-0">24</h2>
                                        </div>
                                        <div class="bg-info bg-opacity-25 p-3 rounded-circle">
                                            <i class='bx bx-shield-alt-2 text-info' style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="card border-0 mb-4">
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent"><i class='bx bx-search'></i></span>
                                        <input type="text" class="form-control" placeholder="Pesquisar perfis...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select">
                                        <option value="">Todos os status</option>
                                        <option>Ativo</option>
                                        <option>Inativo</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-outline-primary w-100">
                                        <i class='bx bx-filter-alt me-1'></i> Filtrar
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-outline-secondary w-100">
                                        <i class='bx bx-export me-1'></i> Exportar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <span>Lista de Perfis</span>
                                </div>
                                <div class="list-group list-group-flush" id="perfisList">
                                    <a href="#" class="list-group-item list-group-item-action active" data-perfil="1">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">Administrador</h6>
                                            <small>1</small>
                                        </div>
                                        <p class="mb-1">Acesso total ao sistema</p>
                                        <small>5 usuários</small>
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action" data-perfil="2">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">Recursos Humanos</h6>
                                            <small>2</small>
                                        </div>
                                        <p class="mb-1">Gerenciamento de colaboradores</p>
                                        <small>3 usuários</small>
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action" data-perfil="3">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">Coordenador</h6>
                                            <small>3</small>
                                        </div>
                                        <p class="mb-1">Visualização de equipes</p>
                                        <small>8 usuários</small>
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action" data-perfil="4">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">Colaborador</h6>
                                            <small>4</small>
                                        </div>
                                        <p class="mb-1">Acesso limitado</p>
                                        <small>45 usuários</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <span>Permissões do Perfil: <span id="perfilNome">Administrador</span></span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="perfilAtivo" checked>
                                        <label class="form-check-label" for="perfilAtivo">Ativo</label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form id="permissoesForm">
                                        <div class="permission-group">
                                            <h5>Usuários</h5>
                                            <div class="permission-item">
                                                <div class="form-check me-3">
                                                    <input class="form-check-input" type="checkbox" id="userCreate" checked disabled>
                                                    <label class="form-check-label" for="userCreate">Criar</label>
                                                </div>
                                                <div class="form-check me-3">
                                                    <input class="form-check-input" type="checkbox" id="userRead" checked disabled>
                                                    <label class="form-check-label" for="userRead">Visualizar</label>
                                                </div>
                                                <div class="form-check me-3">
                                                    <input class="form-check-input" type="checkbox" id="userUpdate" checked>
                                                    <label class="form-check-label" for="userUpdate">Editar</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="userDelete" checked>
                                                    <label class="form-check-label" for="userDelete">Excluir</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="permission-group">
                                            <h5>Colaboradores</h5>
                                            <div class="permission-item">
                                                <div class="form-check me-3">
                                                    <input class="form-check-input" type="checkbox" id="colabCreate" checked>
                                                    <label class="form-check-label" for="colabCreate">Criar</label>
                                                </div>
                                                <div class="form-check me-3">
                                                    <input class="form-check-input" type="checkbox" id="colabRead" checked>
                                                    <label class="form-check-label" for="colabRead">Visualizar</label>
                                                </div>
                                                <div class="form-check me-3">
                                                    <input class="form-check-input" type="checkbox" id="colabUpdate" checked>
                                                    <label class="form-check-label" for="colabUpdate">Editar</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="colabDelete">
                                                    <label class="form-check-label" for="colabDelete">Excluir</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="permission-group">
                                            <h5>Relatórios</h5>
                                            <div class="permission-item">
                                                <div class="form-check me-3">
                                                    <input class="form-check-input" type="checkbox" id="reportView" checked>
                                                    <label class="form-check-label" for="reportView">Visualizar Relatórios</label>
                                                </div>
                                                <div class="form-check me-3">
                                                    <input class="form-check-input" type="checkbox" id="reportExport" checked>
                                                    <label class="form-check-label" for="reportExport">Exportar Dados</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-end mt-4">
                                            <button type="button" class="btn btn-secondary me-2">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Novo Perfil -->
    <div class="modal fade" id="novoPerfilModal" tabindex="-1" aria-labelledby="novoPerfilModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="novoPerfilModalLabel">Novo Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="novoPerfilForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nomePerfil" class="form-label">Nome do Perfil</label>
                            <input type="text" class="form-control" id="nomePerfil" required>
                        </div>
                        <div class="mb-3">
                            <label for="descricaoPerfil" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricaoPerfil" rows="3"></textarea>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="perfilAtivoNovo" checked>
                            <label class="form-check-label" for="perfilAtivoNovo">Ativo</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Criar Perfil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Troca de perfil ativo
            $('#perfisList .list-group-item').on('click', function(e) {
                e.preventDefault();
                $('#perfisList .list-group-item').removeClass('active');
                $(this).addClass('active');
                
                // Atualiza o nome do perfil no cabeçalho
                const perfilNome = $(this).find('h6').text();
                $('#perfilNome').text(perfilNome);
                
                // Aqui você carregaria as permissões do perfil selecionado via AJAX
                console.log('Carregando permissões para o perfil:', perfilNome);
            });
            
            // Submissão do formulário de permissões
            $('#permissoesForm').on('submit', function(e) {
                e.preventDefault();
                // Aqui você implementaria a lógica para salvar as permissões
                alert('Permissões salvas com sucesso!');
            });
            
            // Submissão do formulário de novo perfil
            $('#novoPerfilForm').on('submit', function(e) {
                e.preventDefault();
                const nomePerfil = $('#nomePerfil').val();
                const descricao = $('#descricaoPerfil').val();
                const ativo = $('#perfilAtivoNovo').is(':checked');
                
                // Aqui você implementaria a lógica para criar o novo perfil
                console.log('Criando novo perfil:', { nomePerfil, descricao, ativo });
                
                // Fecha o modal
                $('#novoPerfilModal').modal('hide');
                
                // Limpa o formulário
                this.reset();
                
                // Recarrega a lista de perfis (simulação)
                alert(`Perfil "${nomePerfil}" criado com sucesso!`);
            });
        });
    </script>
</body>
</html>
