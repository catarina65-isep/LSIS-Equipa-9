<?php
session_start();

// Verifica se o utilizador está logado e tem perfil de RH (ID 2)
if (!isset($_SESSION['utilizador_id']) || $_SESSION['id_perfilacesso'] != 2) {
    header('Location: ../login.php');
    exit;
}

$page_title = "Painel de Administração - Tlantic";

require_once __DIR__ . '/../../BLL/UtilizadorBLL.php';
require_once __DIR__ . '/../../BLL/ColaboradorBLL.php';
require_once __DIR__ . '/../../BLL/EquipaBLL.php';
require_once __DIR__ . '/../../BLL/DocumentoBLL.php';

$utilizadorBLL = new UtilizadorBLL();
$colaboradorBLL = new ColaboradorBLL();
$equipaBLL = new EquipaBLL();
$documentoBLL = new DocumentoBLL();

// Obter totais
$total_utilizadores = $utilizadorBLL->contarTotal();
$total_colaboradores = $colaboradorBLL->contarTotal();
$total_equipas = $equipaBLL->contarTotal();
$documentos_pendentes = $documentoBLL->contarPendentes();

// Obter estatísticas
$estatisticas = $colaboradorBLL->obterEstatisticas();
$equipas = $equipaBLL->listarComEstatisticas();
$documentos_vencendo = $documentoBLL->listarProximosVencimentos(5);
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($page_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --warning-color: #f8961e;
            --danger-color: #f72585;
            --light-bg: #f8f9fa;
            --dark-bg: #212529;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #1a2533 100%);
            color: #fff;
            width: var(--sidebar-width);
            position: fixed;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .sidebar .nav-link {
            color: #ecf0f1;
            margin: 5px 15px;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s;
            font-weight: 400;
            display: flex;
            align-items: center;
        }
        
        .sidebar .nav-link i {
            font-size: 1.2rem;
            margin-right: 10px;
            width: 24px;
            text-align: center;
        }
        
        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: var(--primary-color);
            font-weight: 500;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            transition: all 0.3s;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 24px;
            border: 1px solid rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            font-weight: 600;
            padding: 15px 20px;
            border-radius: 12px 12px 0 0 !important;
        }
        
        .card-body {
            padding: 20px;
            flex: 1;
            min-height: 200px;
            display: flex;
            flex-direction: column;
        }
        
        .chart-container {
            position: relative;
            height: 100%;
            min-height: 200px;
            width: 100%;
        }
        
        #growthChart, #departmentChart {
            width: 100% !important;
            height: auto !important;
            max-height: 300px;
        }
        
        .stat-card {
            position: relative;
            overflow: hidden;
            color: #fff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);
            box-shadow: 0 4px 20px rgba(67, 97, 238, 0.3);
        }
        
        .stat-card.bg-primary {
            background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);
            box-shadow: 0 4px 20px rgba(67, 97, 238, 0.3);
        }
        
        .stat-card.bg-success {
            background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%);
            box-shadow: 0 4px 20px rgba(76, 201, 240, 0.3);
        }
        
        .stat-card.bg-warning {
            background: linear-gradient(135deg, #f8961e 0%, #f3722c 100%);
            box-shadow: 0 4px 20px rgba(248, 150, 30, 0.3);
        }
        
        .stat-card.bg-info {
            background: linear-gradient(135deg, #7209b7 0%, #b5179e 100%);
            box-shadow: 0 4px 20px rgba(114, 9, 183, 0.3);
        }
        
        .stat-card.bg-danger {
            background: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
            box-shadow: 0 4px 20px rgba(247, 37, 133, 0.3);
        }
        
        .stat-card.bg-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            box-shadow: 0 4px 20px rgba(108, 117, 125, 0.3);
        }
        
        .stat-icon {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 3.5rem;
            opacity: 0.2;
            width: auto;
            height: auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .stat-icon i {
            font-size: 3.5rem;
        }
        
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 10px 0 5px;
        }
        
        .stat-card .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .stat-card .stat-change {
            font-size: 0.8rem;
            margin-top: 5px;
            display: flex;
            align-items: center;
        }
        
        .stat-card .stat-change i {
            position: relative;
            font-size: 1rem;
            opacity: 1;
            margin-right: 5px;
        }
        
        .activity-item {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            align-items: flex-start;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-time {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .top-bar {
            background: #fff;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .user-menu img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
            border-radius: 5px;
            padding: 10px 15px;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
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
        }
        .sidebar .nav-link:hover {
            background: #34495e;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
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
        }
    </style>
</head>
<body>
    <!-- Incluir a barra lateral -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="d-flex align-items-center">
                <button class="btn btn-link d-md-none me-3" id="sidebarToggle">
                    <i class='bx bx-menu'></i>
                </button>
                <h1 class="h4 mb-0">Painel de Controle</h1>
            </div>
            <div class="d-flex align-items-center">
                <div class="dropdown me-3">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="periodoDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class='bx bx-calendar me-1'></i> Últimos 30 dias
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="periodoDropdown">
                        <li><a class="dropdown-item" href="#">Hoje</a></li>
                        <li><a class="dropdown-item active" href="#">Últimos 7 dias</a></li>
                        <li><a class="dropdown-item" href="#">Últimos 30 dias</a></li>
                        <li><a class="dropdown-item" href="#">Este mês</a></li>
                        <li><a class="dropdown-item" href="#">Personalizado</a></li>
                    </ul>
                </div>
                <button class="btn btn-primary">
                    <i class='bx bx-download me-1'></i> Exportar
                </button>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="stat-card bg-primary">
                    <div class="stat-icon">
                        <i class='bx bx-user'></i>
                    </div>
                    <div class="stat-info">
                        <h3 class="stat-value"><?= number_format($total_utilizadores) ?></h3>
                        <p class="stat-label">Utilizadores Ativos</p>
                    </div>
                    <?php 
                    $variacao_usuarios = $utilizadorBLL->obterDadosParaDashboard()['novos_usuarios_ultimo_mes'];
                    $tendencia_usuarios = $variacao_usuarios >= 0 ? 'light' : 'light';
                    $icone_tendencia = $variacao_usuarios >= 0 ? 'up' : 'down';
                    ?>
                    <div class="stat-trend text-<?= $tendencia_usuarios ?>">
                        <i class='bx bx-<?= $icone_tendencia ?>-arrow-alt'></i>
                        <?= abs($variacao_usuarios) ?>% este mês
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card bg-info">
                    <div class="stat-icon">
                        <i class='bx bx-group'></i>
                    </div>
                    <div class="stat-info">
                        <h3 class="stat-value"><?= number_format($total_colaboradores) ?></h3>
                        <p class="stat-label">Colaboradores</p>
                    </div>
                    <?php 
                    $variacao_colaboradores = $estatisticas['variacao'] ?? 0;
                    $tendencia_colab = $variacao_colaboradores >= 0 ? 'light' : 'light';
                    $icone_colab = $variacao_colaboradores >= 0 ? 'up' : 'down';
                    ?>
                    <div class="stat-trend text-<?= $tendencia_colab ?>">
                        <i class='bx bx-<?= $icone_colab ?>-arrow-alt'></i>
                        <?= abs($variacao_colaboradores) ?>% este mês
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card bg-warning">
                    <div class="stat-icon">
                        <i class='bx bx-file'></i>
                    </div>
                    <div class="stat-info">
                        <h3 class="stat-value"><?= number_format($documentos_pendentes) ?></h3>
                        <p class="stat-label">Documentos Pendentes</p>
                    </div>
                    <a href="documentos.php?status=pending" class="btn btn-sm btn-light mt-2">Ver Todos</a>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card bg-success">
                    <div class="stat-icon">
                        <i class='bx bx-group'></i>
                    </div>
                    <div class="stat-info">
                        <h3 class="stat-value"><?= number_format($total_equipas) ?></h3>
                        <p class="stat-label">Equipas Ativas</p>
                    </div>
                    <a href="equipas.php" class="btn btn-sm btn-light mt-2">Ver Equipas</a>
                </div>
            </div>
        </div>



        <!-- Atividades Recentes e Status -->
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Atividades Recentes</h5>
                        <a href="#" class="btn btn-sm btn-link">Ver Tudo</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <div class="activity-item p-3">
                                <div class="activity-icon bg-primary bg-opacity-10 text-primary">
                                    <i class='bx bx-user-plus'></i>
                                </div>
                                <div class="activity-content">
                                    <div class="d-flex justify-content-between">
                                        <strong>Novo usuário cadastrado</strong>
                                        <span class="activity-time">Há 5 minutos</span>
                                    </div>
                                    <p class="mb-0">João Silva foi adicionado como novo usuário do sistema</p>
                                </div>
                            </div>
                            <div class="activity-item p-3">
                                <div class="activity-icon bg-success bg-opacity-10 text-success">
                                    <i class='bx bx-file'></i>
                                </div>
                                <div class="activity-content">
                                    <div class="d-flex justify-content-between">
                                        <strong>Documento atualizado</strong>
                                        <span class="activity-time">Há 1 hora</span>
                                    </div>
                                    <p class="mb-0">Política de Segurança da Informação foi atualizada</p>
                                </div>
                            </div>
                            <div class="activity-item p-3">
                                <div class="activity-icon bg-warning bg-opacity-10 text-warning">
                                    <i class='bx bx-calendar'></i>
                                </div>
                                <div class="activity-content">
                                    <div class="d-flex justify-content-between">
                                        <strong>Férias aprovadas</strong>
                                        <span class="activity-time">Ontem</span>
                                    </div>
                                    <p class="mb-0">Solicitação de férias de Maria Santos foi aprovada</p>
                                </div>
                            </div>
                            <div class="activity-item p-3">
                                <div class="activity-icon bg-info bg-opacity-10 text-info">
                                    <i class='bx bx-group'></i>
                                </div>
                                <div class="activity-content">
                                    <div class="d-flex justify-content-between">
                                        <strong>Novo departamento</strong>
                                        <span class="activity-time">Ontem</span>
                                    </div>
                                    <p class="mb-0">Departamento de Inovação foi criado</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Status do Sistema</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Uso de Armazenamento</span>
                                <span>25%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted">15.2 GB de 100 GB usados</small>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Uso de Memória</span>
                                <span>65%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 65%;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted">6.5 GB de 10 GB usados</small>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Uso de CPU</span>
                                <span>42%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 42%;" aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted">Médio - 42% de utilização</small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success rounded-circle me-2" style="width: 10px; height: 10px;"></div>
                                    <small>Online: 12 usuários</small>
                                </div>
                            </div>
                            <div>
                                <span class="badge bg-primary">v1.0.0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                
    <style>
        .card {
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1.25rem;
        }
        .card-body {
            padding: 1.25rem;
        }
        .sidebar {
            min-height: 100vh;
            background: #2d3748;
            color: #fff;
            transition: all 0.3s;
        }
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            .sidebar.show {
                margin-left: 0;
            }
        }
    </style>
                `;
                