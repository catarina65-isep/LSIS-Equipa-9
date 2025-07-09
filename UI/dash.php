<?php
session_start();

// Verifica se o utilizador está logado
if (!isset($_SESSION['utilizador_id']) || !isset($_SESSION['id_perfilacesso'])) {
    header('Location: /LSIS-Equipa-9/UI/login.php');
    exit;
}

// Define o título da página
$page_title = 'Dashboard - Tlantic';

// Dados fixos do dashboard (similares aos originais)
$data = [
    'retentionRate' => '85.5%',
    'averageAge' => '32.7 anos',
    'averageTenure' => '4.2 anos',
    'averageSalary' => '2500 €',
    'hierarquiaData' => [50, 20, 10],
    'generoData' => [45, 35],
    'funcaoData' => [30, 25, 15],
    'geografiaData' => [40, 30, 10],
    'tempoGeneroData' => [4.5, 3.8],
    'remuneracaoData' => [1000, 1400, 1200],
    'hierarquiaEtariaData' => [
        [4, 9, 4, 3],
        [3, 5, 6, 2],
        [1, 2, 3, 4]
    ]
];
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Estilos personalizados -->
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f9ff;
            color: #003366;
            overflow-x: hidden;
        }
        .sidebar {
            width: 250px;
            position: fixed;
            height: 100%;
            background-color: #003366;
            padding-top: 20px;
            transition: all 0.3s;
        }
        .sidebar .logo img {
            width: 150px;
            margin: 20px auto;
            display: block;
        }
        .nav-menu a {
            color: white;
            padding: 15px 20px;
            display: block;
            text-decoration: none;
            font-size: 1.1em;
        }
        .nav-menu a:hover, .nav-menu a.active {
            background-color: #005fa3;
        }
        .nav-menu a i {
            margin-right: 10px;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .topbar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 10px 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .user-info span {
            margin-right: 20px;
            font-size: 1.1em;
        }
        .logout-btn {
            color: #dc3545;
            text-decoration: none;
            font-size: 1.1em;
        }
        .logout-btn i {
            margin-right: 5px;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.1);
        }
        .card-body h5 {
            font-size: 1.2em;
            color: #003366;
            font-weight: 600;
        }
        .card-text {
            font-size: 1.8em;
            font-weight: 700;
        }
        .container-fluid .row.mb-4 {
            margin-bottom: 20px !important;
        }
        .table-responsive {
            max-height: 300px;
            overflow-y: auto;
        }
        .section {
            margin-bottom: 40px;
        }
        .section-title {
            background-color: #003366;
            color: white;
            padding: 8px;
            border-radius: 8px;
            text-align: center;
            font-size: 1.5em;
            margin: 20px 0 10px;
            font-weight: 600;
        }
        .container {
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 0 30px;
        }
        .container.individual {
            flex-wrap: wrap;
        }
        .container.pairs {
            flex-wrap: nowrap;
            overflow-x: auto;
            justify-content: center;
        }
        .charts-row {
            display: flex;
            flex-wrap: nowrap;
            gap: 20px;
            width: 100%;
            overflow-x: auto;
            align-self: flex-start;
        }
        .chart-box {
            background-color: white;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.1);
            width: 100%;
            max-width: 300px;
            box-sizing: border-box;
            text-align: center;
            flex: 1;
            min-width: 250px;
        }
        .container.pairs .chart-group {
            display: flex;
            flex-direction: column;
            gap: 20px;
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
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="../images/logo-tlantic.png" alt="Tlantic Logo">
        </div>
        <nav class="nav-menu">
            <a href="dashboard.php" class="active"><i class='bx bx-grid-alt'></i> Dashboard</a>
            <a href="colaboradores.php"><i class='bx bx-user'></i> Colaboradores</a>
            <a href="relatorios.php"><i class='bx bx-bar-chart-alt'></i> Relatórios</a>
            <a href="configuracoes.php"><i class='bx bx-cog'></i> Configurações</a>
        </nav>
    </div>

    <!-- Content -->
    <div class="main-content">
        <div class="topbar">
            <div class="user-info">
                <span>Bem-vindo, <?= htmlspecialchars($_SESSION['nome']) ?></span>
                <a href="../logout.php" class="logout-btn"><i class='bx bx-log-out'></i> Sair</a>
            </div>
        </div>

        <div class="container-fluid mt-4">
            <!-- Cards Principais -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Colaboradores</h5>
                            <h2 class="card-text" id="totalCollaborators">80</h2> <!-- Placeholder -->
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Aniversários</h5>
                            <h2 class="card-text" id="birthdays">5</h2> <!-- Placeholder -->
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Alertas</h5>
                            <h2 class="card-text" id="pendingAlerts">3</h2> <!-- Placeholder -->
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Novos</h5>
                            <h2 class="card-text" id="newHires">2</h2> <!-- Placeholder -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção de Indicadores Individuais -->
            <div class="section">
                <div class="section-title">Indicadores Individuais</div>
                <div class="container individual">
                    <div class="chart-box compact-box">
                        <h2>Taxa de Retenção</h2>
                        <div class="metric-value" id="retentionRate"><?php echo htmlspecialchars($data['retentionRate']); ?></div>
                        <div class="metric-legend">Percentagem de colaboradores retidos na empresa</div>
                    </div>
                    <div class="chart-box compact-box">
                        <h2>Idade Média</h2>
                        <div class="metric-value" id="averageAge"><?php echo htmlspecialchars($data['averageAge']); ?></div>
                        <div class="metric-legend">Média de idade dos colaboradores da Tlantic</div>
                    </div>
                    <div class="chart-box compact-box">
                        <h2>Tempo Médio na Tlantic</h2>
                        <div class="metric-value" id="averageTenure"><?php echo htmlspecialchars($data['averageTenure']); ?></div>
                        <div class="metric-legend">Tempo médio de permanência dos colaboradores na empresa</div>
                    </div>
                    <div class="chart-box compact-box">
                        <h2>Remuneração Média</h2>
                        <div class="metric-value" id="averageSalary"><?php echo htmlspecialchars($data['averageSalary']); ?></div>
                        <div class="metric-legend">Remuneração média mensal dos colaboradores</div>
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
                        <div class="chart-box">
                            <h2>Distribuição por Geografia</h2>
                            <canvas id="chartGeografia"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Seção de Indicadores Relacionados -->
            <div class="section">
                <div class="section-title">Indicadores Relacionados</div>
                <div class="container pairs">
                    <div class="chart-group">
                        <div class="chart-box tempo-genero">
                            <h2>Tempo Médio na Empresa por Género</h2>
                            <canvas id="chartTempoGenero"></canvas>
                        </div>
                        <div class="chart-box remuneracao">
                            <h2>Remuneração por Hierarquia</h2>
                            <canvas id="chartRemuneracao"></canvas>
                        </div>
                    </div>
                    <div class="chart-box hierarquia-etaria">
                        <h2>Nível Hierárquico por Faixa Etária</h2>
                        <canvas id="chartHierarquiaEtaria"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tabelas -->
            <div class="row">
                <!-- Aniversariantes -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Aniversariantes do Mês</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Data Nascimento</th>
                                            <th>Departamento</th>
                                        </tr>
                                    </thead>
                                    <tbody id="birthdaysTable">
                                        <tr><td>João Silva</td><td>15/07/1995</td><td>TI</td></tr>
                                        <tr><td>Maria Oliveira</td><td>20/07/1990</td><td>Recursos Humanos</td></tr>
                                        <!-- Placeholder -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alertas -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Alertas Pendentes</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Título</th>
                                            <th>Colaborador</th>
                                            <th>Tipo</th>
                                            <th>Prioridade</th>
                                            <th>Expiração</th>
                                            <th>Departamento</th>
                                        </tr>
                                    </thead>
                                    <tbody id="alertsTable">
                                        <tr><td>Aviso</td><td>Pedro Costa</td><td>Falta</td><td>Alta</td><td>10/07/2025</td><td>TI</td></tr>
                                        <tr><td>Reunião</td><td>Ana Lopes</td><td>Reunião</td><td>Média</td><td>12/07/2025</td><td>Recursos Humanos</td></tr>
                                        <!-- Placeholder -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        Chart.defaults.font.family = "'Segoe UI', sans-serif";
        Chart.defaults.font.size = 14;
        Chart.defaults.font.weight = '500';
        Chart.defaults.color = '#003366';

        // Data from PHP
        const data = <?php echo json_encode($data); ?>;

        let chartHierarquia, chartGenero, chartFuncao, chartGeografia, chartTempoGenero, chartRemuneracao, chartHierarquiaEtaria;

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
                    labels: ['Masculino', 'Feminino'],
                    datasets: [{ data: data.generoData, backgroundColor: ['#0077cc', '#80bfff'] }]
                },
                options: { plugins: { legend: { position: 'bottom' } } }
            });

            // Chart: Distribuição por Função
            chartFuncao = new Chart(document.getElementById('chartFuncao'), {
                type: 'bar',
                data: {
                    labels: ['Desenvolvedor', 'Analista', 'Gestor'],
                    datasets: [{ data: data.funcaoData, backgroundColor: ['#003f6b', '#005fa3', '#0077cc'] }]
                },
                options: { plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } }
            });

            // Chart: Distribuição por Geografia
            chartGeografia = new Chart(document.getElementById('chartGeografia'), {
                type: 'pie',
                data: {
                    labels: ['Lisboa', 'Porto', 'Coimbra'],
                    datasets: [{ data: data.geografiaData, backgroundColor: ['#004c99', '#0066cc', '#3399ff'] }]
                },
                options: { plugins: { legend: { position: 'bottom' } } }
            });

            // Chart: Tempo Médio na Empresa por Género
            chartTempoGenero = new Chart(document.getElementById('chartTempoGenero'), {
                type: 'bar',
                data: {
                    labels: ['Masculino', 'Feminino'],
                    datasets: [{ data: data.tempoGeneroData, backgroundColor: ['#004c99', '#66b2ff'], barThickness: 50 }]
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, title: { text: 'Anos' } } } }
            });

            // Chart: Remuneração por Hierarquia
            chartRemuneracao = new Chart(document.getElementById('chartRemuneracao'), {
                type: 'bar',
                data: {
                    labels: ['Colaborador', 'Coordenador', 'RH'],
                    datasets: [{ label: 'Remuneração Média (€)', data: data.remuneracaoData, backgroundColor: ['#66b2ff', '#3399cc', '#0077cc'] }]
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, title: { text: '€ Remuneração' } } } }
            });

            // Chart: Nível Hierárquico por Faixa Etária
            chartHierarquiaEtaria = new Chart(document.getElementById('chartHierarquiaEtaria'), {
                type: 'bar',
                data: {
                    labels: ['<25', '25-35', '36-45', '>45'],
                    datasets: [
                        { label: 'Colaborador', data: data.hierarquiaEtariaData[0], backgroundColor: '#0066cc' },
                        { label: 'Coordenador', data: data.hierarquiaEtariaData[1], backgroundColor: '#cc66ff' },
                        { label: 'RH', data: data.hierarquiaEtariaData[2], backgroundColor: '#3399cc' }
                    ]
                },
                options: {
                    plugins: { legend: { position: 'right' } },
                    scales: { y: { beginAtZero: true, title: { text: 'Nº de Colaboradores' } } }
                }
            });
        }

        // Render charts on page load
        renderDashboard();
    </script>
</body>
</html>