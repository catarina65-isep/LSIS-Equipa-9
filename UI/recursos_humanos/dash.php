<?php
session_start();

// Verifica se o utilizador está logado
if (!isset($_SESSION['utilizador_id']) || !isset($_SESSION['id_perfilacesso'])) {
    header('Location: /LSIS-Equipa-9/UI/login.php');
    exit;
}

// Define o título da página
$page_title = 'Dashboard - Tlantic';

// Inclui a classe de métricas
require_once dirname(dirname(__DIR__)) . '/DAL/metricas_rh.php';

// Cria uma instância da classe de métricas
$metricas = new MetricasRH();

// Obtém os dados reais da base de dados
$data = [
    'retentionRate' => $metricas->getTaxaRetencao(),
    'averageAge' => $metricas->getIdadeMedia(),
    'averageTenure' => $metricas->getTempoMedioPermanencia(),
    'averageSalary' => $metricas->getSalarioMedio(),
    'hierarquiaData' => $metricas->getDistribuicaoHierarquia(),
    'generoData' => $metricas->getDistribuicaoGenero(),
    'funcaoData' => $metricas->getDistribuicaoFuncao(),
    'tempoGeneroData' => $metricas->getTempoPorGenero(),
    'remuneracaoData' => $metricas->getRemuneracaoPorHierarquia()
];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons -->
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <link href="/LSIS-Equipa-9/UI/assets/css/style.css" rel="stylesheet">
    
    <!-- Estilos personalizados -->
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
            padding-left: var(--sidebar-width);
        }
        
        .main-content {
            padding: 20px;
            min-height: 100vh;
        }
        
        .top-bar {
            background: #fff;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            color: #2c3e50;
        }
        
        .user-actions {
            display: flex;
            align-items: center;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 15px 20px;
            border-top-left-radius: 10px !important;
            border-top-right-radius: 10px !important;
        }
        
        .card-header h5 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        @media (max-width: 992px) {
            body {
                padding-left: 0;
            }
            
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
        }
        .container.pairs .chart-box.tempo-genero {
            max-width: 600px;
        }
        .container.pairs .chart-box.remuneracao {
            max-width: 600px;
        }
        .container.pairs .chart-box.hierarquia-etaria {
            max-width: 550px;
        }
        .compact-box {
            padding: 10px;
            max-height: 150px;
            flex: 1 1 300px;
        }
        canvas {
            width: 100% !important;
            height: 200px !important;
        }
        .container.pairs .chart-box.tempo-genero canvas {
            height: 350px !important;
        }
        .container.pairs .chart-box.remuneracao canvas {
            height: 350px !important;
        }
        .container.pairs .chart-box.hierarquia-etaria canvas {
            height: 350px !important;
        }
        h2 {
            font-size: 1.2em;
            color: #003366;
            margin-bottom: 10px;
            text-align: center;
            font-weight: 600;
        }
        .metric-value {
            font-size: 2em;
            color: #0077cc;
            margin: 5px 0;
            font-weight: normal;
        }
        .metric-legend {
            font-size: 0.9em;
            color: #005fa3;
            margin-top: 5px;
            font-weight: normal;
        }
        .charts-row {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            justify-content: center;
            margin-top: 20px;
        }
        .charts-row .chart-box {
            flex: 1 1 220px;
            max-width: 250px;
            min-width: 180px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 10px 10px 0 10px;
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .charts-row .chart-box h2 {
            font-size: 1em;
            margin-bottom: 8px;
        }
        .charts-row .chart-box canvas {
            height: 160px !important;
            max-width: 100% !important;
        }
        @media (max-width: 900px) {
            .charts-row {
                flex-direction: column;
                align-items: stretch;
            }
            .charts-row .chart-box {
                max-width: 100%;
                margin-bottom: 16px;
            }
        }
        .container.pairs .chart-group {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            justify-content: center;
        }
        .container.pairs .chart-box.tempo-genero,
        .container.pairs .chart-box.remuneracao {
            flex: 1 1 250px;
            max-width: 300px;
            min-width: 180px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 10px 10px 0 10px;
            margin-bottom: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .container.pairs .chart-box.tempo-genero canvas,
        .container.pairs .chart-box.remuneracao canvas {
            height: 180px !important;
            max-width: 100% !important;
        }
        .container.pairs .chart-box.hierarquia-etaria {
            margin: 24px auto 0 auto;
            max-width: 350px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 10px 10px 0 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .container.pairs .chart-box.hierarquia-etaria canvas {
            height: 180px !important;
            max-width: 100% !important;
        }
        .section-title {
            font-size: 1.5em;
            color: #003366;
            font-weight: 700;
            margin-bottom: 24px;
            text-align: left;
            letter-spacing: 0.5px;
            background: linear-gradient(90deg, #e0e7ff 60%, #fff 100%);
            padding: 10px 20px 10px 0;
            border-left: 6px solid #4361ee;
            border-radius: 0 8px 8px 0;
            box-shadow: 0 2px 8px rgba(67,97,238,0.07);
            display: inline-block;
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
                <h1 class="h4 mb-0">Dashboard RH</h1>
            </div>
            <div class="user-actions">
                <a href="/LSIS-Equipa-9/UI/logout.php" class="btn btn-outline-secondary btn-sm">
                    <i class='bx bx-log-out'></i> Sair
                </a>
            </div>
        </div>

        <div class="container-fluid mt-4">
            <!-- Seção de Indicadores Individuais -->
            <div class="section">
                <div class="section-title">Indicadores Individuais</div>
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card text-center h-100">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Taxa de Retenção</h5>
                                <div class="metric-value" id="retentionRate"><?php echo htmlspecialchars($data['retentionRate']); ?></div>
                                <div class="metric-legend">Percentagem de colaboradores retidos na empresa</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card text-center h-100">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Idade Média</h5>
                                <div class="metric-value" id="averageAge"><?php echo htmlspecialchars($data['averageAge']); ?></div>
                                <div class="metric-legend">Média de idade dos colaboradores da Tlantic</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card text-center h-100">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Tempo Médio na Tlantic</h5>
                                <div class="metric-value" id="averageTenure"><?php echo htmlspecialchars($data['averageTenure']); ?></div>
                                <div class="metric-legend">Tempo médio de permanência dos colaboradores na empresa</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card text-center h-100">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h5 class="card-title">Remuneração Média</h5>
                                <div class="metric-value" id="averageSalary"><?php echo htmlspecialchars($data['averageSalary']); ?></div>
                                <div class="metric-legend">Remuneração média mensal dos colaboradores</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="charts-row">
                    <div class="chart-box">
                        <h2>Distribuição por Nível Hierárquico</h2>
                        <canvas id="chartHierarquia"></canvas>
                    </div>
                    <div class="chart-box">
                        <h2>Distribuição por Género</h2>
                        <canvas id="chartGenero"></canvas>
                    </div>
                    <div class="chart-box">
                        <h2>Distribuição por Função</h2>
                        <canvas id="chartFuncao"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção de Indicadores Relacionados -->
        <div class="section">
            <div class="section-title">Indicadores Relacionados</div>
            <div class="row g-3 mb-4">
                <div class="col-12 col-lg-4 d-flex">
                    <div class="chart-box tempo-genero flex-fill w-100">
                        <h2>Tempo Médio na Empresa por Género</h2>
                        <canvas id="chartTempoGenero"></canvas>
                    </div>
                </div>
                <div class="col-12 col-lg-4 d-flex">
                    <div class="chart-box remuneracao flex-fill w-100">
                        <h2>Remuneração por Hierarquia</h2>
                        <canvas id="chartRemuneracao"></canvas>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            if (window.innerWidth <= 992 && 
                !sidebar.contains(event.target) && 
                !sidebarToggle.contains(event.target) &&
                sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        });
    </script>
    <script>
        Chart.defaults.font.family = "'Segoe UI', sans-serif";
        Chart.defaults.font.size = 14;
        Chart.defaults.font.weight = '500';
        Chart.defaults.color = '#003366';

        // Data from PHP
        const data = <?php echo json_encode($data); ?>;

        let chartHierarquia, chartGenero, chartFuncao, chartTempoGenero, chartRemuneracao, chartHierarquiaEtaria;

        function renderDashboard() {
            // Update metrics
            document.getElementById('retentionRate').textContent = data.retentionRate;
            document.getElementById('averageAge').textContent = data.averageAge;
            document.getElementById('averageTenure').textContent = data.averageTenure;
            document.getElementById('averageSalary').textContent = data.averageSalary;

            // Chart: Distribuição por Nível Hierárquico
            chartHierarquia = new Chart(document.getElementById('chartHierarquia'), {
                type: 'line',
                data: {
                    labels: ['Colaborador', 'Coordenador', 'RH'],
                    datasets: [{
                        label: 'Total',
                        data: data.hierarquiaData,
                        fill: true,
                        backgroundColor: 'rgba(0,119,204,0.2)',
                        borderColor: '#0077cc',
                        tension: 0.3
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true }, x: { ticks: { font: { size: 14 } } } }
                }
            });

            // Chart: Distribuição por Género
            chartGenero = new Chart(document.getElementById('chartGenero'), {
                type: 'pie',
                data: {
                    labels: data.generoData.labels,
                    datasets: [{ 
                        data: data.generoData.data,
                        backgroundColor: ['#0077cc', '#80bfff', '#66b2ff']
                    }]
                },
                options: { 
                    plugins: { 
                        legend: { 
                            position: 'bottom',
                            labels: {
                                font: { size: 12 }
                            }
                        }
                    }
                }
            });

            // Chart: Distribuição por Função
            chartFuncao = new Chart(document.getElementById('chartFuncao'), {
                type: 'bar',
                data: {
                    labels: data.funcaoData.labels,
                    datasets: [{ 
                        data: data.funcaoData.data,
                        backgroundColor: ['#003f6b', '#005fa3', '#0077cc']
                    }]
                },
                options: { 
                    plugins: { legend: { display: false } }, 
                    scales: { 
                        x: { 
                            beginAtZero: true,
                            ticks: {
                                font: { size: 12 }
                            }
                        }
                    }
                }
            });

            // Chart: Tempo Médio na Empresa por Género
            chartTempoGenero = new Chart(document.getElementById('chartTempoGenero'), {
                type: 'bar',
                data: {
                    labels: data.tempoGeneroData.labels,
                    datasets: [{ 
                        data: data.tempoGeneroData.data,
                        backgroundColor: ['#004c99', '#66b2ff'],
                        barThickness: 50
                    }]
                },
                options: { 
                    plugins: { 
                        legend: { display: false }
                    }, 
                    scales: { 
                        y: { 
                            beginAtZero: true, 
                            title: { text: 'Anos' },
                            ticks: { font: { size: 12 } }
                        }
                    }
                }
            });

            // Chart: Remuneração por Função
            chartRemuneracao = new Chart(document.getElementById('chartRemuneracao'), {
                type: 'bar',
                data: {
                    labels: data.remuneracaoData.labels,
                    datasets: [{ 
                        label: 'Remuneração Média (€)', 
                        data: data.remuneracaoData.data,
                        backgroundColor: '#66b2ff'
                    }]
                },
                options: { 
                    plugins: { 
                        legend: { 
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Remuneração Média por Função'
                        }
                    },
                    scales: { 
                        y: { 
                            beginAtZero: true, 
                            title: { text: '€ Remuneração' },
                            ticks: { font: { size: 12 } }
                        }
                    }
                }
            });


        }

        // Render charts on page load
        renderDashboard();
    </script>
</body>
</html>