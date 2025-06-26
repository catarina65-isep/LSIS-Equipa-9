<?php
session_start();

// Verifica se o usuário está logado e é administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['id_perfilacesso'] != 1) {
    header('Location: ../login.php');
    exit;
}

$page_title = "Painel de Administração - Tlantic";

// Dados simulados para o dashboard
$total_usuarios = 24;
$total_colaboradores = 156;
$alertas_pendentes = 8;
$atualizacoes_recentes = 3;

// Dados para gráficos
$meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
$usuarios_por_mes = [5, 8, 12, 15, 18, 20, 22, 23, 23, 23, 24, 24];
$colaboradores_por_mes = [120, 125, 130, 135, 140, 145, 148, 150, 152, 154, 155, 156];
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
        }
        
        .stat-card i {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 4rem;
            opacity: 0.2;
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

        <!-- Cards de Métricas -->
        <div class="row g-4 mb-4">
            <div class="col-6 col-md-4 col-lg-2">
                <div class="stat-card bg-primary">
                    <i class='bx bx-group'></i>
                    <div class="stat-value"><?= $total_colaboradores ?></div>
                    <div class="stat-label">Total Colaboradores</div>
                    <div class="stat-change">
                        <i class='bx bx-line-chart-up text-success'></i> 5% no último mês
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="stat-card bg-success">
                    <i class='bx bx-user-plus'></i>
                    <div class="stat-value">12</div>
                    <div class="stat-label">Novas Contratações</div>
                    <div class="stat-change">
                        <small>Mês atual</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="stat-card bg-info">
                    <i class='bx bx-time-five'></i>
                    <div class="stat-value">8</div>
                    <div class="stat-label">Em Experiência</div>
                    <div class="stat-change">
                        <small>Período experimental</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="stat-card bg-warning">
                    <i class='bx bx-mobile-alt'></i>
                    <div class="stat-value">5</div>
                    <div class="stat-label">Vouchers a Expirar</div>
                    <div class="stat-change">
                        <small>Próximos 30 dias</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="stat-card bg-danger">
                    <i class='bx bx-file'></i>
                    <div class="stat-value">18</div>
                    <div class="stat-label">Documentos Pendentes</div>
                    <div class="stat-change">
                        <small>Por aprovar</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="stat-card bg-secondary">
                    <i class='bx bx-calendar-event'></i>
                    <div class="stat-value">7</div>
                    <div class="stat-label">Aniversários</div>
                    <div class="stat-change">
                        <small>Este mês</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Crescimento de Usuários e Colaboradores</h5>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary active" data-period="year">Anual</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-period="month">Mensal</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-period="week">Semanal</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="growthChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Distribuição por Departamento</h5>
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <div class="chart-container" style="max-width: 250px;">
                            <canvas id="departmentChart"></canvas>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary me-2">TI</span>
                                <span>25%</span>
                            </div>
                            <div>
                                <span class="badge bg-success me-2">RH</span>
                                <span>20%</span>
                            </div>
                            <div>
                                <span class="badge bg-warning me-2">Vendas</span>
                                <span>15%</span>
                            </div>
                        </div>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
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
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
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
    </script>
</body>
</html>
