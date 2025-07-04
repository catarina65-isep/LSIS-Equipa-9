<?php
session_start();

// Verifica se o usuário está logado e tem permissão (admin ou RH)
if (!isset($_SESSION['utilizador_id']) || ($_SESSION['id_perfilacesso'] != 1 && $_SESSION['id_perfilacesso'] != 2)) {
    header('Location: ../login.php');
    exit;
}

$page_title = "Gerenciar Colaboradores - Tlantic";
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
        
        /* Estilo para os botões de ação na tabela */
        .table-actions .btn {
            padding: 0.25rem 0.5rem;
            font-size: 1.1rem;
            line-height: 1;
            opacity: 0.8;
            transition: all 0.2s;
        }
        
        .table-actions .btn:hover {
            opacity: 1;
            transform: translateY(-1px);
        }
        
        /* Estilo para os toasts */
        .toast {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        /* Estilo para os cards de estatísticas */
        .stat-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1) !important;
        }
        
        /* Estilo para o formulário no modal */
        .form-label.required:after {
            content: ' *';
            color: #dc3545;
        }
        
        /* Estilo para a tabela */
        .table-hover tbody tr {
            transition: background-color 0.15s ease;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #6c757d;
        }
        
        .table > :not(:first-child) {
            border-top: none;
        }
        
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }
    </style>
    <style>
        /* Estilos da Sidebar */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1a2a3a 0%, #2c3e50 100%);
            color: #fff;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            width: 16.666667%;
            z-index: 1000;
        }
        
        /* Estilos dos links de navegação */
        .nav-link {
            transition: all 0.3s ease;
            color: #ecf0f1;
            margin: 2px 0;
            border-radius: 6px;
            padding: 10px 15px;
            display: flex;
            align-items: center;
        }
        .nav-link:not(.active):hover {
            background: rgba(255, 255, 255, 0.1) !important;
            transform: translateX(5px);
            color: #fff;
        }
        .nav-link.active {
            background: rgba(255, 255, 255, 0.15) !important;
            border-left: 3px solid #fff;
            font-weight: 500;
        }
        .nav-link i {
            transition: all 0.3s ease;
            margin-right: 10px;
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }
        .nav-link:hover i {
            transform: scale(1.1);
        }
        
        /* Estilos do dropdown do usuário */
        .dropdown-menu {
            background: #2c3e50;
            border: 1px solid rgba(255,255,255,0.1);
            min-width: 200px;
        }
        .dropdown-item {
            color: #ecf0f1;
            padding: 8px 15px;
            display: flex;
            align-items: center;
        }
        .dropdown-item i {
            margin-right: 8px;
        }
        .dropdown-item:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
        .dropdown-divider {
            border-color: rgba(255,255,255,0.1);
            margin: 0.5rem 0;
        }
        
        /* Ajustes no conteúdo principal */
        .main-content {
            margin-left: 16.666667%;
            width: 83.333333%;
            padding: 20px;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .main-content {
            padding: 20px;
            background: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #eee;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .avatar-sm {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .status-badge {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        .status-active { background-color: #28a745; }
        .status-inactive { background-color: #dc3545; }
        .status-onleave { background-color: #ffc107; }
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
                        <h1 class="h3 mb-1 text-gray-800">Colaboradores</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Colaboradores</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novoColaboradorModal">
                            <i class='bx bx-plus me-2'></i>Novo Colaborador
                        </button>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="container-fluid px-4">
                    <div class="row g-4 mb-4">
                        <!-- Total de Colaboradores -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm stat-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-muted mb-2 small">Total de Colaboradores</h6>
                                            <h2 class="mb-0">248</h2>
                                        </div>
                                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                            <i class='bx bx-group text-primary' style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Colaboradores Ativos -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm stat-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-muted mb-2 small">Ativos</h6>
                                            <h2 class="mb-0">230</h2>
                                        </div>
                                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                                            <i class='bx bx-check-circle text-success' style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Em Férias -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm stat-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-muted mb-2 small">Em Férias</h6>
                                            <h2 class="mb-0">15</h2>
                                        </div>
                                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                                            <i class='bx bx-sun text-warning' style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Em Treinamento -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card h-100 border-0 shadow-sm stat-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase text-muted mb-2 small">Em Treinamento</h6>
                                            <h2 class="mb-0">3</h2>
                                        </div>
                                        <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                                            <i class='bx bx-book-reader text-info' style="font-size: 1.5rem;"></i>
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
                                            <h2 class="mb-0">215</h2>
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
                                            <h6 class="text-uppercase text-warning mb-1">Férias</h6>
                                            <h2 class="mb-0">18</h2>
                                        </div>
                                        <div class="bg-warning bg-opacity-25 p-3 rounded-circle">
                                            <i class='bx bx-sun text-warning' style="font-size: 1.5rem;"></i>
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
                                            <h6 class="text-uppercase text-info mb-1">Novos (30d)</h6>
                                            <h2 class="mb-0">15</h2>
                                        </div>
                                        <div class="bg-info bg-opacity-25 p-3 rounded-circle">
                                            <i class='bx bx-user-plus text-info' style="font-size: 1.5rem;"></i>
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
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent"><i class='bx bx-search'></i></span>
                                        <input type="text" class="form-control" placeholder="Pesquisar colaboradores...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select">
                                        <option value="">Todos departamentos</option>
                                        <option>TI</option>
                                        <option>RH</option>
                                        <option>Financeiro</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select">
                                        <option value="">Status</option>
                                        <option>Ativo</option>
                                        <option>Inativo</option>
                                        <option>Férias</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-outline-primary w-100">
                                        <i class='bx bx-filter-alt me-1'></i> Filtrar
                                    </button>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success bg-opacity-10 border-0">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-uppercase text-success mb-1">Ativos</h6>
                                                <h2 class="mb-0">215</h2>
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
                                                <h6 class="text-uppercase text-warning mb-1">Férias</h6>
                                                <h2 class="mb-0">18</h2>
                                            </div>
                                            <div class="bg-warning bg-opacity-25 p-3 rounded-circle">
                                                <i class='bx bx-sun text-warning' style="font-size: 1.5rem;"></i>
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
                                                <h6 class="text-uppercase text-info mb-1">Novos (30d)</h6>
                                                <h2 class="mb-0">15</h2>
                                            </div>
                                            <div class="bg-info bg-opacity-25 p-3 rounded-circle">
                                                <i class='bx bx-user-plus text-info' style="font-size: 1.5rem;"></i>
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
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text bg-transparent"><i class='bx bx-search'></i></span>
                                            <input type="text" class="form-control" placeholder="Pesquisar colaboradores...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select">
                                            <option value="">Todos departamentos</option>
                                            <option>TI</option>
                                            <option>RH</option>
                                            <option>Financeiro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select">
                                            <option value="">Status</option>
                                            <option>Ativo</option>
                                            <option>Inativo</option>
                                            <option>Férias</option>
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

                    <!-- Tabela de Colaboradores -->
                    <div class="card border-0">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <div class="d-flex align-items-center">
                                <h5 class="mb-0 me-3">Lista de Colaboradores</h5>
                                <div class="d-flex">
                                    <button class="btn btn-sm btn-outline-secondary me-2">
                                        <i class='bx bx-export me-1'></i> Exportar
                                    </button>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class='bx bx-dots-horizontal-rounded'></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="dropdownMenuButton1">
                                            <li><a class="dropdown-item" href="#"><i class='bx bx-show me-2'></i>Visualizar Colunas</a></li>
                                            <li><a class="dropdown-item" href="#"><i class='bx bx-download me-2'></i>Exportar Dados</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#"><i class='bx bx-reset me-2'></i>Redefinir Filtros</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novoColaboradorModal">
                                <i class='bx bx-plus me-2'></i>Novo Colaborador
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="colaboradoresTable" class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>Colaborador</th>
                                            <th>Departamento</th>
                                            <th>Cargo</th>
                                            <th>Status</th>
                                            <th>Última Atualização</th>
                                            <th style="width: 180px;">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><img src="https://randomuser.me/api/portraits/men/1.jpg" alt="" class="avatar-sm"></td>
                                            <td>
                                                <div class="fw-bold">João Silva</div>
                                                <small class="text-muted">joao.silva@email.com</small>
                                            </td>
                                            <td>TI</td>
                                            <td>Desenvolvedor Sênior</td>
                                            <td><span class="status-badge status-active"></span> Ativo</td>
                                            <td>10/06/2023</td>
                                            <td>
                                                <div class="d-flex">
                                                    <button class="btn btn-sm btn-link text-primary p-1 me-1" title="Visualizar">
                                                        <i class='bx bx-show fs-5'></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-link text-warning p-1 me-1" title="Editar">
                                                        <i class='bx bx-edit fs-5'></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-link text-danger p-1" title="Desativar">
                                                        <i class='bx bx-user-x fs-5'></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Mais linhas aqui -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Novo Colaborador -->
    <div class="modal fade" id="novoColaboradorModal" tabindex="-1" aria-labelledby="novoColaboradorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="novoColaboradorModalLabel"><i class='bx bx-user-plus me-2'></i>Novo Colaborador</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="novoColaboradorForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nome" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="departamento" class="form-label">Departamento</label>
                                    <select class="form-select" id="departamento">
                                        <option value="">Selecione...</option>
                                        <option value="TI">TI</option>
                                        <option value="RH">Recursos Humanos</option>
                                        <option value="Financeiro">Financeiro</option>
                                        <option value="Comercial">Comercial</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cargo" class="form-label">Cargo</label>
                                    <input type="text" class="form-control" id="cargo">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefone" class="form-label">Telefone</label>
                                    <input type="tel" class="form-control" id="telefone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="dataAdmissao" class="form-label">Data de Admissão</label>
                                    <input type="date" class="form-control" id="dataAdmissao">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="statusAtivo" checked>
                                    <label class="form-check-label" for="statusAtivo">Ativo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class='bx bx-x me-1'></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class='bx bx-save me-1'></i>Salvar Colaborador
                        </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast de Sucesso -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="toastSuccess" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class='bx bx-check-circle me-2'></i> Colaborador salvo com sucesso!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
            </div>
        </div>
    </div>

    <!-- Toast de Erro -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="toastError" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class='bx bx-error-circle me-2'></i> Por favor, preencha todos os campos obrigatórios.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicialização da DataTable
            $('#colaboradoresTable').DataTable({
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
                order: [[1, 'asc']],
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
            });

            // Inicialização do Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Selecione uma opção',
                allowClear: true
            });

            // Máscaras de entrada
            $('#telefone').mask('(00) 00000-0000');
            $('#dataAdmissao').mask('00/00/0000');

            // Validação do formulário
            $('#novoColaboradorForm').on('submit', function(e) {
                e.preventDefault();
                
                // Validação dos campos obrigatórios
                let isValid = true;
                $(this).find('[required]').each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });
                
                if (!isValid) {
                    // Exibe mensagem de erro
                    const toast = new bootstrap.Toast(document.getElementById('toastError'));
                    toast.show();
                    return;
                }
                
                // Simulação de envio do formulário
                const formData = {
                    nome: $('#nome').val(),
                    email: $('#email').val(),
                    departamento: $('#departamento').val(),
                    cargo: $('#cargo').val(),
                    telefone: $('#telefone').val(),
                    dataAdmissao: $('#dataAdmissao').val(),
                    status: $('#statusAtivo').is(':checked') ? 'Ativo' : 'Inativo'
                };
                
                console.log('Dados do formulário:', formData);
                
                // Aqui você faria a requisição AJAX para salvar os dados
                // Por enquanto, apenas exibimos uma mensagem de sucesso
                const toast = new bootstrap.Toast(document.getElementById('toastSuccess'));
                toast.show();
                
                // Fecha o modal após 1,5 segundos
                setTimeout(() => {
                    $('#novoColaboradorModal').modal('hide');
                    this.reset();
                    // Recarrega a tabela após adicionar um novo colaborador
                    $('#colaboradoresTable').DataTable().ajax.reload();
                }, 1500);
            });
            
            // Limpa os erros ao fechar o modal
            $('#novoColaboradorModal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
                $(this).find('.is-invalid').removeClass('is-invalid');
            });
        });
    </script>
</body>
</html>
