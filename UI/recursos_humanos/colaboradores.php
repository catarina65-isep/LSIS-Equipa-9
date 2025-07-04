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
        /* Estilos gerais */
        :root {
            --primary-color: #4e73df;
            --secondary-color: #6c757d;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-x: hidden;
        }
        
        /* Estatísticas */
        .stat-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        
        .stat-card .card-body {
            padding: 1.25rem;
        }
        
        .stat-card i {
            font-size: 1.75rem;
            opacity: 0.8;
        }
        
        /* Tabela */
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            border-bottom: 1px solid #e3e6f0;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            color: #4e73df;
            padding: 1rem;
            background-color: #f8f9fc;
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #e3e6f0;
        }
        
        /* Botões de ação */
        .btn-action {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 2px;
        }
        
        /* Badges */
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
        
        /* Formulário de filtro */
        .filter-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }
            
            .stat-card {
                margin-bottom: 1rem;
            }
            
            .table-responsive {
                border-radius: 0.35rem;
            }
        }
        
        /* Estilos da Sidebar */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1a2a3a 0%, #2c3e50 100%);
            color: #fff;
            padding: 20px 0;
            position: sticky;
            top: 0;
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
                    <!-- Botão removido para evitar duplicação -->
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
                                            <h2 class="mb-0 total-colaboradores">0</h2>
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
                                            <h2 class="mb-0 ativos">0</h2>
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
                                            <h2 class="mb-0 ferias">0</h2>
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
                                            <h2 class="mb-0 treinamento">0</h2>
                                        </div>
                                        <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                                            <i class='bx bx-book-reader text-info' style="font-size: 1.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter -->
                <!-- Barra de ferramentas e filtros -->
                <div class="card border-0 mb-4">
                    <div class="card-body p-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class='bx bx-search'></i></span>
                                    <input type="text" id="searchInput" class="form-control" placeholder="Pesquisar por nome ou email...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select id="filterDepartment" class="form-select">
                                    <option value="">Todos os departamentos</option>
                                    <option>TI</option>
                                    <option>RH</option>
                                    <option>Financeiro</option>
                                    <option>Vendas</option>
                                    <option>Marketing</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="filterStatus" class="form-select">
                                    <option value="">Todos os status</option>
                                    <option>Ativo</option>
                                    <option>Inativo</option>
                                    <option>Férias</option>
                                    <option>Em Treinamento</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button id="btnFilter" class="btn btn-outline-primary w-100">
                                    <i class='bx bx-filter-alt me-1'></i> Filtrar
                                </button>
                            </div>
                            <div class="col-md-2 ms-auto">
                                <button type="button" class="btn btn-primary w-100 btn-novo-colaborador">
                                    <i class='bx bx-plus me-2'></i>Novo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                    <!-- Tabela de Colaboradores -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <div class="d-flex align-items-center">
                                <h5 class="mb-0 me-3">Lista de Colaboradores</h5>
                                <span class="badge bg-primary rounded-pill total-colaboradores">0</span>
                            </div>
                            <div class="d-flex">
                                <button class="btn btn-sm btn-outline-secondary me-2" id="btnExport">
                                    <i class='bx bx-export me-1'></i> Exportar
                                </button>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class='bx bx-dots-horizontal-rounded'></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                                        <li><a class="dropdown-item" href="#" id="btnShowColumns"><i class='bx bx-show me-2'></i>Visualizar Colunas</a></li>
                                        <li><a class="dropdown-item" href="#" id="btnExportData"><i class='bx bx-download me-2'></i>Exportar Dados</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" id="btnResetFilters"><i class='bx bx-reset me-2'></i>Redefinir Filtros</a></li>
                                    </ul>
                                </div>
                            </div>
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
                                        <!-- As linhas serão preenchidas dinamicamente pelo JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Novo/Editar Colaborador -->
    <div class="modal fade" id="colaboradorModal" tabindex="-1" aria-labelledby="colaboradorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="colaboradorModalLabel">Novo Colaborador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form id="colaboradorForm">
                    <input type="hidden" id="colaboradorId">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nome" class="form-label">Nome Completo *</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">E-mail *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-md-6">
                                <label for="departamento" class="form-label">Departamento *</label>
                                <select class="form-select select2" id="departamento" name="departamento" required>
                                    <option value="">Selecione...</option>
                                    <option value="TI">TI</option>
                                    <option value="RH">Recursos Humanos</option>
                                    <option value="Financeiro">Financeiro</option>
                                    <option value="Vendas">Vendas</option>
                                    <option value="Marketing">Marketing</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="cargo" class="form-label">Cargo *</label>
                                <input type="text" class="form-control" id="cargo" name="cargo" required>
                            </div>
                            <div class="col-md-6">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input type="tel" class="form-control" id="telefone" name="telefone">
                            </div>
                            <div class="col-md-6">
                                <label for="dataAdmissao" class="form-label">Data de Admissão</label>
                                <input type="date" class="form-control" id="dataAdmissao" name="dataAdmissao">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Status</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusAtivo" value="Ativo" checked>
                                    <label class="form-check-label" for="statusAtivo">
                                        Ativo
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusFerias" value="Férias">
                                    <label class="form-check-label" for="statusFerias">
                                        Férias
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="statusTreinamento" value="Em Treinamento">
                                    <label class="form-check-label" for="statusTreinamento">
                                        Em Treinamento
                                    </label>
                                </div>
                            </div>
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
        // Função para obter colaboradores do localStorage ou retornar dados padrão
        function obterColaboradores() {
            const colaboradoresSalvos = localStorage.getItem('colaboradores');
            if (colaboradoresSalvos) {
                return JSON.parse(colaboradoresSalvos);
            } else {
                // Dados iniciais caso não haja nada salvo
                const dadosIniciais = [
                    {
                        id: 1,
                        nome: "João Silva",
                        email: "joao.silva@email.com",
                        departamento: "TI",
                        cargo: "Desenvolvedor Sênior",
                        status: "Ativo",
                        dataAdmissao: "2022-01-15",
                        emFerias: false,
                        emTreinamento: false
                    },
                    {
                        id: 2,
                        nome: "Maria Santos",
                        email: "maria.santos@email.com",
                        departamento: "RH",
                        cargo: "Gerente de Recursos Humanos",
                        status: "Ativo",
                        dataAdmissao: "2021-11-10",
                        emFerias: false,
                        emTreinamento: false
                    },
                    {
                        id: 3,
                        nome: "Carlos Oliveira",
                        email: "carlos.oliveira@email.com",
                        departamento: "Financeiro",
                        cargo: "Analista Financeiro",
                        status: "Férias",
                        dataAdmissao: "2022-03-05",
                        emFerias: true,
                        emTreinamento: false
                    },
                    {
                        id: 4,
                        nome: "Ana Pereira",
                        email: "ana.pereira@email.com",
                        departamento: "Marketing",
                        cargo: "Especialista em Mídias Sociais",
                        status: "Em Treinamento",
                        dataAdmissao: "2023-01-20",
                        emFerias: false,
                        emTreinamento: true
                    },
                    {
                        id: 5,
                        nome: "Pedro Alves",
                        email: "pedro.alves@email.com",
                        departamento: "Vendas",
                        cargo: "Representante de Vendas",
                        status: "Ativo",
                        dataAdmissao: "2022-08-12",
                        emFerias: false,
                        emTreinamento: false
                    }
                ];
                // Salva os dados iniciais no localStorage
                localStorage.setItem('colaboradores', JSON.stringify(dadosIniciais));
                return dadosIniciais;
            }
        }
        
        // Variável global para armazenar os colaboradores
        let colaboradores = obterColaboradores();
        
        // Função para salvar os colaboradores no localStorage
        function salvarColaboradores() {
            localStorage.setItem('colaboradores', JSON.stringify(colaboradores));
        }
        
        // Função para atualizar as estatísticas
        function atualizarEstatisticas() {
            const total = colaboradores.length;
            const ativos = colaboradores.filter(c => c.status === 'Ativo').length;
            const ferias = colaboradores.filter(c => c.status === 'Férias').length;
            const treinamento = colaboradores.filter(c => c.status === 'Em Treinamento').length;
            
            // Atualiza os valores nos cartões
            $('.total-colaboradores').text(total);
            $('.ativos').text(ativos);
            $('.ferias').text(ferias);
            $('.treinamento').text(treinamento);
            
            // Atualiza o contador de registros na tabela
            if ($.fn.DataTable.isDataTable('#colaboradoresTable')) {
                $('#colaboradoresTable').DataTable().draw(false);
            }
        }
        
        // Função para renderizar a tabela com os dados atuais
        function renderizarTabela() {
            // Limpa a tabela
            if ($.fn.DataTable.isDataTable('#colaboradoresTable')) {
                $('#colaboradoresTable').DataTable().destroy();
            }
            
            // Limpa o corpo da tabela
            $('#colaboradoresTable tbody').empty();
            
            // Adiciona as linhas dos colaboradores
            colaboradores.forEach(colaborador => {
                const statusClass = {
                    'Ativo': 'bg-success',
                    'Inativo': 'bg-secondary',
                    'Férias': 'bg-warning',
                    'Em Treinamento': 'bg-info'
                }[colaborador.status] || 'bg-secondary';
                
                const statusBadge = `<span class="badge ${statusClass}">${colaborador.status}</span>`;
                
                const row = `
                    <tr data-id="${colaborador.id}">
                        <td><img src="https://ui-avatars.com/api/?name=${encodeURIComponent(colaborador.nome)}&background=random" alt="" class="rounded-circle" width="40" height="40"></td>
                        <td>
                            <div class="fw-bold">${colaborador.nome}</div>
                            <small class="text-muted">${colaborador.email}</small>
                        </td>
                        <td>${colaborador.departamento || '-'}</td>
                        <td>${colaborador.cargo || '-'}</td>
                        <td>${statusBadge}</td>
                        <td>${new Date(colaborador.dataAdmissao).toLocaleDateString('pt-PT')}</td>
                        <td>
                            <div class="d-flex">
                                <button type="button" class="btn btn-sm btn-link text-primary p-1 me-1 btn-visualizar" title="Visualizar">
                                    <i class='bx bx-show fs-5'></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-link text-warning p-1 me-1 btn-editar" title="Editar" data-id="${colaborador.id}">
                                    <i class='bx bx-edit fs-5'></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-link text-danger p-1 btn-excluir" title="Excluir" data-id="${colaborador.id}">
                                    <i class='bx bx-trash fs-5'></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                $('#colaboradoresTable tbody').append(row);
            });
            
            // Reinicializa a DataTable
            table = $('#colaboradoresTable').DataTable({
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
            
            // Atualiza as estatísticas
            atualizarEstatisticas();
        }
        
        // Variável global para a DataTable
        let table;

        $(document).ready(function() {
            // Renderiza a tabela com os dados iniciais
            renderizarTabela();

            // Inicialização do Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Selecione uma opção',
                allowClear: true
            });

            // Máscara para o telefone
            $('#telefone').mask('(00) 00000-0000');

            // Função para carregar dados do colaborador no formulário de edição
            function carregarDadosColaborador(id) {
                // Verifica se o ID é válido
                if (isNaN(id) || id <= 0) {
                    console.error('ID do colaborador inválido:', id);
                    return;
                }
                
                // Encontra o colaborador pelo ID
                const colaborador = colaboradores.find(c => c.id === id);
                
                if (!colaborador) {
                    console.error('Colaborador não encontrado com o ID:', id);
                    return;
                }
                
                console.log('Carregando dados do colaborador:', colaborador);
                
                // Preenche o formulário com os dados do colaborador
                $('#colaboradorId').val(colaborador.id);
                $('#nome').val(colaborador.nome || '');
                $('#email').val(colaborador.email || '');
                
                // Define o departamento e dispara o evento de mudança
                if (colaborador.departamento) {
                    $('#departamento').val(colaborador.departamento).trigger('change');
                } else {
                    $('#departamento').val('').trigger('change');
                }
                
                $('#cargo').val(colaborador.cargo || '');
                $('#telefone').val(colaborador.telefone || '');
                
                // Formata a data de admissão para o formato YYYY-MM-DD
                if (colaborador.dataAdmissao) {
                    const data = new Date(colaborador.dataAdmissao);
                    const dataFormatada = data.toISOString().split('T')[0];
                    $('#dataAdmissao').val(dataFormatada);
                } else {
                    $('#dataAdmissao').val('');
                }
                
                // Define o status correto
                if (colaborador.status) {
                    $(`input[name="status"]`).prop('checked', false);
                    $(`input[name="status"][value="${colaborador.status}"]`).prop('checked', true);
                }
                
                // Atualiza o título do modal
                $('#colaboradorModalLabel').text('Editar Colaborador');
                
                // Abre o modal
                const modal = new bootstrap.Modal(document.getElementById('colaboradorModal'));
                modal.show();
            }
            
            // Abre o modal para novo colaborador
            $('.btn-novo-colaborador').on('click', function() {
                $('#colaboradorForm')[0].reset();
                $('#colaboradorId').val('');
                $('#colaboradorModalLabel').text('Novo Colaborador');
                
                // Abre o modal
                const modal = new bootstrap.Modal(document.getElementById('colaboradorModal'));
                modal.show();
            });
            
            // Validação do formulário
            $('#colaboradorForm').on('submit', function(e) {
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
                
                // Obtém os dados do formulário
                const formData = {
                    id: $('#colaboradorId').val() ? parseInt($('#colaboradorId').val()) : null,
                    nome: $('#nome').val(),
                    email: $('#email').val(),
                    departamento: $('#departamento').val(),
                    cargo: $('#cargo').val(),
                    telefone: $('#telefone').val(),
                    dataAdmissao: $('#dataAdmissao').val(),
                    status: $('input[name="status"]:checked').val()
                };
                
                if (formData.id) {
                    // Atualiza o colaborador existente
                    const index = colaboradores.findIndex(c => c.id === formData.id);
                    if (index !== -1) {
                        colaboradores[index] = {
                            ...colaboradores[index],
                            nome: formData.nome,
                            email: formData.email,
                            departamento: formData.departamento,
                            cargo: formData.cargo,
                            telefone: formData.telefone,
                            dataAdmissao: formData.dataAdmissao,
                            status: formData.status,
                            emFerias: formData.status === 'Férias',
                            emTreinamento: formData.status === 'Em Treinamento'
                        };
                    }
                } else {
                    // Adiciona um novo colaborador
                    const novoId = colaboradores.length > 0 ? Math.max(...colaboradores.map(c => c.id)) + 1 : 1;
                    colaboradores.push({
                        id: novoId,
                        nome: formData.nome,
                        email: formData.email,
                        departamento: formData.departamento,
                        cargo: formData.cargo,
                        telefone: formData.telefone,
                        dataAdmissao: formData.dataAdmissao || new Date().toISOString().split('T')[0],
                        status: formData.status,
                        emFerias: formData.status === 'Férias',
                        emTreinamento: formData.status === 'Em Treinamento'
                    });
                }
                
                // Salva no localStorage
                salvarColaboradores();
                
                // Renderiza a tabela novamente
                renderizarTabela();
                
                // Fecha o modal e limpa o formulário
                const modal = bootstrap.Modal.getInstance(document.getElementById('colaboradorModal'));
                modal.hide();
                
                // Exibe mensagem de sucesso
                const toast = new bootstrap.Toast(document.getElementById('toastSuccess'));
                $('.toast-body', '#toastSuccess').html('<i class="bx bx-check-circle me-2"></i> ' + 
                    (formData.id ? 'Colaborador atualizado com sucesso!' : 'Colaborador adicionado com sucesso!'));
                toast.show();
            });
            
            // Limpa os erros ao fechar o modal
            $('#colaboradorModal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
                $(this).find('.is-invalid').removeClass('is-invalid');
            });
            
            // Evento de clique no botão de editar
            $(document).on('click', '.btn-editar', function(e) {
                console.log('Botão editar clicado');
                console.log('Elemento clicado:', this);
                
                const id = parseInt($(this).data('id'));
                console.log('ID do colaborador:', id);
                
                if (id) {
                    console.log('Chamando carregarDadosColaborador com ID:', id);
                    carregarDadosColaborador(id);
                } else {
                    console.error('ID do colaborador não encontrado no botão');
                    console.log('Atributos do botão:', this.attributes);
                }
            });

            // Evento de clique no botão de excluir
            $(document).on('click', '.btn-excluir', function() {
                const id = parseInt($(this).data('id'));
                const row = $(this).closest('tr');
                
                if (confirm('Tem certeza que deseja excluir este colaborador?')) {
                    // Remove o colaborador do array
                    colaboradores = colaboradores.filter(c => c.id !== id);
                    
                    // Salva no localStorage
                    salvarColaboradores();
                    
                    // Remove a linha da tabela
                    row.fadeOut(400, function() {
                        $(this).remove();
                        // Atualiza as estatísticas
                        atualizarEstatisticas();
                    });
                    
                    // Exibe mensagem de sucesso
                    const toast = new bootstrap.Toast(document.getElementById('toastSuccess'));
                    $('.toast-body', '#toastSuccess').html('<i class="bx bx-check-circle me-2"></i> Colaborador excluído com sucesso!');
                    toast.show();
                }
            });

            // Inicializa a tabela
            renderizarTabela();
        });
    </script>
</body>
</html>
