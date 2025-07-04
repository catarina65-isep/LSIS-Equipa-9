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

// Dados para gráficos
$meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
$admissoes_ultimos_12_meses = $colaboradorBLL->obterAdmissoesUltimos12Meses();
$distribuicao_equipas = $equipaBLL->obterDistribuicaoPorEquipa();

// Preparar dados para os gráficos
$labels_equipas = [];
$dados_equipas = [];
foreach ($distribuicao_equipas as $equipa) {
    $labels_equipas[] = $equipa['nome_equipa'];
    $dados_equipas[] = $equipa['total_colaboradores'];
}
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
            <div class="col-6 col-md-4 col-lg-2">
                <div class="stat-card bg-danger">
                    <div class="stat-icon">
                        <i class='bx bx-file'></i>
                    </div>
                    <div class="stat-info">
                        <h3 class="stat-value">18</h3>
                        <p class="stat-label">Documentos Pendentes</p>
                    </div>
                    <div class="stat-trend">
                        <small>Por aprovar</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="stat-card bg-secondary">
                    <div class="stat-icon">
                        <i class='bx bx-calendar-event'></i>
                    </div>
                    <div class="stat-info">
                        <h3 class="stat-value">7</h3>
                        <p class="stat-label">Aniversários</p>
                    </div>
                    <div class="stat-trend">
                        <small>Este mês</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Distribuição por Função</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="chart1" class="chart-container"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Distribuição por Género</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="chart2" class="chart-container"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Evolução da Remuneração Média</h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="chart3" class="chart-container" style="min-height: 400px;"></div>
                    </div>
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
    
    <!-- Carregar Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Verificar se o Chart.js foi carregado corretamente -->
    <script>
        if (typeof Chart === 'undefined') {
            console.error('ERRO: Chart.js não foi carregado corretamente!');
            // Tenta carregar de um CDN alternativo
            document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js" integrity="sha512-ElRFoEQdI5Ht6kZvyzXhYG9NqjtkmlkfYk0wr6wHxEF9nTlK5l7l5f5q5f5q5f5q5f5q5f5q5f5q5f5q5f5q5" crossorigin="anonymous" referrerpolicy="no-referrer"><\/script>');
        } else {
            console.log('Chart.js carregado com sucesso! Versão:', Chart.version);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <style>
        /* Estilos para os contêineres dos gráficos */
        .chart-container {
            width: 100% !important;
            height: 400px !important;
            min-height: 400px !important;
            position: relative;
            margin: 0;
            padding: 0;
        }
        
        /* Garantir que os elementos de gráfico tenham dimensões explícitas */
        #chart1, #chart2, #chart3 {
            width: 100% !important;
            height: 100% !important;
            min-height: 400px !important;
            display: block;
            margin: 0;
            padding: 0;
        }
        
        /* Garantir que os cartões tenham altura suficiente */
        .card {
            min-height: 500px;
            margin-bottom: 20px;
        }
        
        /* Estilo para mensagens de erro */
        .chart-error {
            padding: 20px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            color: #721c24;
            margin: 10px 0;
        }
    </style>
    
    <script>
        // Inicialização de componentes
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle sidebar em dispositivos móveis
            document.getElementById('sidebarToggle').addEventListener('click', function() {
                document.querySelector('.sidebar').classList.toggle('show');
            });

            // Inicializar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Inicializar popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });

            // Inicializar Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });


            // Dados para os gráficos
            const meses = <?= json_encode($meses) ?>;
            const usuariosPorMes = <?= json_encode($usuarios_por_mes) ?>;
            const colaboradoresPorMes = <?= json_encode($colaboradores_por_mes) ?>;

            // Gráfico de crescimento
            const growthCtx = document.getElementById('growthChart').getContext('2d');
            const growthChart = new Chart(growthCtx, {
                type: 'line',
                data: {
                    labels: meses,
                    datasets: [
                        {
                            label: 'Usuários',
                            data: usuariosPorMes,
                            borderColor: '#4361ee',
                            backgroundColor: 'rgba(67, 97, 238, 0.1)',
                            tension: 0.3,
                            fill: true,
                            borderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5
                        },
                        {
                            label: 'Colaboradores',
                            data: colaboradoresPorMes,
                            borderColor: '#4cc9f0',
                            backgroundColor: 'rgba(76, 201, 240, 0.1)',
                            tension: 0.3,
                            fill: true,
                            borderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { // <-- corrigido aqui
                            position: 'top',
                        },
                        tooltip: { // <-- corrigido aqui
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });


            // Gráfico de departamentos
            const deptCtx = document.getElementById('departmentChart').getContext('2d');
            const departmentChart = new Chart(deptCtx, {
                type: 'doughnut',
                data: {
                    labels: ['TI', 'RH', 'Vendas', 'Marketing', 'Financeiro', 'Outros'],
                    datasets: [{
                        data: [24, 18, 32, 12, 8, 6],
                        backgroundColor: [
                            '#4361ee',
                            '#4cc9f0',
                            '#7209b7',
                            '#f72585',
                            '#4ad66d',
                            '#ff9e00'
                        ],
                        borderWidth: 0,
                        offset: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    return `${label}: ${value}%`;
                                }
                            }
                        }
                    }
                }
            });

            // Alternar entre visualizações de período
            document.querySelectorAll('[data-period]').forEach(btn => {
                btn.addEventListener('click', function() {
                    // Remover classe active de todos os botões
                    document.querySelectorAll('[data-period]').forEach(b => b.classList.remove('active'));
                    // Adicionar classe active ao botão clicado
                    this.classList.add('active');
                    
                    // Atualizar o texto do dropdown
                    const dropdownBtn = document.querySelector('#periodoDropdown');
                    const periodText = this.textContent.trim();
                    dropdownBtn.innerHTML = `<i class='bx bx-calendar me-1'></i> ${periodText}`;
                    
                    // Aqui você pode adicionar lógica para carregar dados diferentes
                    // com base no período selecionado (anual/mensal/semanal)
                    console.log('Período selecionado:', this.dataset.period);
                    
                    // Exemplo de atualização de dados (simulação)
                    fetch(`api/dashboard-data.php?period=${this.dataset.period}`)
                        .then(response => response.json())
                        .then(data => {
                            // Atualizar os cards de métricas
                            document.querySelector('.stat-card:nth-child(1) .stat-value').textContent = data.total_usuarios;
                            document.querySelector('.stat-card:nth-child(2) .stat-value').textContent = data.total_colaboradores;
                            document.querySelector('.stat-card:nth-child(3) .stat-value').textContent = data.alertas_pendentes;
                            document.querySelector('.stat-card:nth-child(4) .stat-value').textContent = data.atualizacoes_recentes;
                            
                            // Atualizar os gráficos
                            growthChart.data.labels = data.meses;
                            growthChart.data.datasets[0].data = data.usuarios_por_mes;
                            growthChart.data.datasets[1].data = data.colaboradores_por_mes;
                            growthChart.update();
                            
                            departmentChart.data.datasets[0].data = data.distribuicao_departamentos;
                            departmentChart.update();
                        })
                        .catch(error => {
                            console.error('Erro ao carregar dados:', error);
                        });
                });
            });

            // Inicializar DataTables
            $('.table').DataTable({
                pageLength: 5,
                lengthChange: false,
                searching: false,
                info: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-PT.json'
                }
            });

            // Atualizar a cada 5 minutos
            setInterval(() => {
                // Simulação de atualização de dados em tempo real
                const alertas = Math.floor(Math.random() * 5);
                document.querySelector('.stat-card:nth-child(3) .stat-value').textContent = 
                    Math.max(1, alertas); // Garante pelo menos 1 alerta
                
                // Atualizar contador de usuários online (simulação)
                const online = 10 + Math.floor(Math.random() * 5);
                document.querySelector('.bg-success + small').textContent = `Online: ${online} usuários`;
            }, 300000); // 5 minutos

            // Efeito de carregamento suave
            const cards = document.querySelectorAll('.card, .stat-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = `opacity 0.5s ease ${index * 0.1}s, transform 0.5s ease ${index * 0.1}s`;
                
                // Forçar reflow
                void card.offsetWidth;
                
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            });
        });

        // Função para verificar se um elemento existe e está visível
        function elementIsReady(selector) {
            const element = document.querySelector(selector);
            if (!element) {
                console.error('Elemento não encontrado:', selector);
                return null;
            }
            if (element.offsetParent === null) {
                console.warn('Elemento não está visível:', selector);
            }
            return element;
        }

        // Inicializar gráficos quando o DOM estiver pronto
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== INÍCIO DA INICIALIZAÇÃO DOS GRÁFICOS ===');
            console.log('Verificando se o Chart.js está disponível...');
            
            // Verificar se o Chart.js está disponível
            if (typeof Chart === 'undefined') {
                const errorMsg = 'ERRO: Chart.js não foi carregado corretamente!';
                console.error(errorMsg);
                const chart1 = elementIsReady('#chart1');
                if (chart1) {
                    chart1.innerHTML = `
                        <div class="alert alert-danger">
                            <h4>Erro ao carregar a biblioteca de gráficos</h4>
                            <p>${errorMsg}</p>
                            <p>Por favor, verifique sua conexão com a internet e atualize a página.</p>
                        </div>`;
                }
                return;
            }
            
            console.log('Chart.js está disponível. Versão:', Chart.version);
            
            try {
                // Verificar se os elementos dos gráficos existem
                const chart1El = elementIsReady('#chart1');
                const chart2El = elementIsReady('#chart2');
                const chart3El = elementIsReady('#chart3');
                
                if (!chart1El || !chart2El || !chart3El) {
                    const errorMsg = 'Um ou mais elementos de gráfico não foram encontrados na página.';
                    console.error(errorMsg);
                    if (chart1El) {
                        chart1El.innerHTML = `
                            <div class="alert alert-warning">
                                <h4>Erro ao carregar os gráficos</h4>
                                <p>${errorMsg}</p>
                                <p>Por favor, atualize a página e tente novamente.</p>
                            </div>`;
                    }
                    return;
                }
                
                // Gráfico 1 - Distribuição por Função (Barras)
                console.log('Iniciando renderização do gráfico 1...');
                const ctx1 = chart1El.getContext('2d');
                if (!ctx1) {
                    throw new Error('Não foi possível obter o contexto 2D para o gráfico 1');
                }
                
                new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels: ['Engenharia', 'Marketing', 'Vendas', 'RH', 'Financeiro'],
                        datasets: [{
                            label: 'Número de Colaboradores',
                            data: [40, 25, 30, 10, 15],
                            backgroundColor: [
                                'rgba(67, 97, 238, 0.7)',
                                'rgba(76, 201, 240, 0.7)',
                                'rgba(63, 55, 201, 0.7)',
                                'rgba(111, 66, 193, 0.7)',
                                'rgba(33, 150, 243, 0.7)'
                            ],
                            borderColor: [
                                'rgba(67, 97, 238, 1)',
                                'rgba(76, 201, 240, 1)',
                                'rgba(63, 55, 201, 1)',
                                'rgba(111, 66, 193, 1)',
                                'rgba(33, 150, 243, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Distribuição por Função',
                                font: {
                                    size: 16
                                }
                            },
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Número de Colaboradores'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Função'
                                }
                            }
                        }
                    }
                });
                
                // Gráfico 2 - Distribuição por Gênero (Pizza)
                console.log('Iniciando renderização do gráfico 2...');
                const ctx2 = chart2El.getContext('2d');
                if (!ctx2) {
                    throw new Error('Não foi possível obter o contexto 2D para o gráfico 2');
                }
                
                new Chart(ctx2, {
                    type: 'pie',
                    data: {
                        labels: ['Masculino', 'Feminino', 'Outro'],
                        datasets: [{
                            data: [55, 43, 2],
                            backgroundColor: [
                                'rgba(67, 97, 238, 0.7)',
                                'rgba(76, 201, 240, 0.7)',
                                'rgba(63, 55, 201, 0.7)'
                            ],
                            borderColor: [
                                'rgba(67, 97, 238, 1)',
                                'rgba(76, 201, 240, 1)',
                                'rgba(63, 55, 201, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Distribuição por Gênero',
                                font: {
                                    size: 16
                                }
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
                
                // Gráfico 3 - Evolução da Remuneração Média (Linha)
                console.log('Iniciando renderização do gráfico 3...');
                const ctx3 = chart3El.getContext('2d');
                if (!ctx3) {
                    throw new Error('Não foi possível obter o contexto 2D para o gráfico 3');
                }
                
                new Chart(ctx3, {
                    type: 'line',
                    data: {
                        labels: [2019, 2020, 2021, 2022, 2023],
                        datasets: [{
                            label: 'Remuneração Média (€)',
                            data: [30000, 32000, 35000, 37000, 40000],
                            fill: false,
                            borderColor: 'rgba(67, 97, 238, 1)',
                            backgroundColor: 'rgba(67, 97, 238, 0.1)',
                            tension: 0.1,
                            borderWidth: 3,
                            pointBackgroundColor: 'rgba(67, 97, 238, 1)',
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Evolução da Remuneração Média',
                                font: {
                                    size: 16
                                }
                            },
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                title: {
                                    display: true,
                                    text: 'Valor (€)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return '€' + value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Ano'
                                }
                            }
                        }
                    }
                });
                
                console.log('Gráficos renderizados com sucesso!');
                
            } catch (error) {
                console.error('Erro ao renderizar gráficos:', error);
                
                // Mostrar mensagem de erro no lugar do gráfico 1
                const chart1 = document.getElementById('chart1');
                if (chart1) {
                    chart1.innerHTML = 
                        '<div class="alert alert-danger">' +
                        '   <h4>Erro ao carregar os gráficos</h4>' +
                        '   <p>Ocorreu um erro ao carregar os gráficos. Por favor, verifique o console para mais detalhes.</p>' +
                        '   <p>Erro: ' + error.message + '</p>' +
                        '</div>';
                }
            }
        });
        
        // Ajustar tamanho dos gráficos quando a janela for redimensionada
        window.addEventListener('resize', function() {
            // O Chart.js já lida com o redimensionamento automaticamente
        });</script>
</body>
</html>