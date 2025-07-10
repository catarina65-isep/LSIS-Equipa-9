<?php
// Define o título da página
$page_title = 'Dashboard - Tlantic';

// Configuração da conexão à base de dados
$host = 'localhost';
$port = '8889'; // Porta padrão do MySQL no MAMP
$db = 'ficha_colaboradores';
$user = 'root';
$pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Consultas para obter os dados do dashboard
$total_active = $pdo->query("SELECT COUNT(*) as total FROM colaborador WHERE estado = 'Ativo'")->fetchColumn();
$retained_last_year = $pdo->query("SELECT COUNT(*) FROM colaborador WHERE data_entrada <= DATE_SUB(CURDATE(), INTERVAL 1 YEAR) AND estado = 'Ativo'")->fetchColumn();
$retentionRate = $total_active > 0 ? round(($retained_last_year / $total_active) * 100, 1) . '%' : '0%';

$avg_age = $pdo->query("SELECT AVG(TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE())) as average_age FROM colaborador WHERE estado = 'Ativo'")->fetchColumn();
$averageAge = number_format($avg_age, 1) . ' anos';

$avg_tenure = $pdo->query("SELECT AVG(TIMESTAMPDIFF(YEAR, data_entrada, CURDATE())) as average_tenure FROM colaborador WHERE estado = 'Ativo'")->fetchColumn();
$averageTenure = number_format($avg_tenure, 1) . ' anos';

$avg_salary = $pdo->query("SELECT AVG(remuneracao_bruta) as average_salary FROM colaborador WHERE estado = 'Ativo'")->fetchColumn();
$averageSalary = number_format($avg_salary, 2) . ' €';

$hierarquiaData = $pdo->query("SELECT 
    COUNT(CASE WHEN c.id_coordenador IS NULL THEN 1 END) as Colaborador,
    COUNT(CASE WHEN c.id_coordenador IS NOT NULL THEN 1 END) as Coordenador,
    COUNT(CASE WHEN c.id_rh IS NOT NULL THEN 1 END) as RH
    FROM colaborador c
    WHERE c.estado = 'Ativo'")->fetch(PDO::FETCH_ASSOC);
$hierarquiaData = array_values($hierarquiaData);

$generoData = $pdo->query("SELECT genero, COUNT(*) as count FROM colaborador WHERE estado = 'Ativo' GROUP BY genero")->fetchAll(PDO::FETCH_ASSOC);
$generoData = array_column($generoData, 'count');

$funcaoData = $pdo->query("SELECT funcao, COUNT(*) as count FROM colaborador WHERE estado = 'Ativo' GROUP BY funcao")->fetchAll(PDO::FETCH_ASSOC);
$funcaoData = array_column($funcaoData, 'count');

$geografiaData = $pdo->query("SELECT localidade, COUNT(*) as count FROM colaborador WHERE estado = 'Ativo' GROUP BY localidade")->fetchAll(PDO::FETCH_ASSOC);
$geografiaData = array_column($geografiaData, 'count');

$tempoGeneroData = $pdo->query("SELECT genero, AVG(TIMESTAMPDIFF(YEAR, data_entrada, CURDATE())) as avg_tenure 
    FROM colaborador WHERE estado = 'Ativo' GROUP BY genero")->fetchAll(PDO::FETCH_ASSOC);
$tempoGeneroData = array_column($tempoGeneroData, 'avg_tenure');

$remuneracaoData = $pdo->query("SELECT AVG(remuneracao_bruta) as avg_salary 
    FROM colaborador 
    WHERE estado = 'Ativo' 
    GROUP BY CASE 
        WHEN id_coordenador IS NULL THEN 'Colaborador'
        WHEN id_coordenador IS NOT NULL AND id_rh IS NULL THEN 'Coordenador'
        ELSE 'RH' 
    END")->fetchAll(PDO::FETCH_ASSOC);
$remuneracaoData = array_column($remuneracaoData, 'avg_salary');

$hierarquiaEtariaData = $pdo->query("SELECT 
    CASE 
        WHEN TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) < 25 THEN '<25'
        WHEN TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) BETWEEN 25 AND 35 THEN '25-35'
        WHEN TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) BETWEEN 36 AND 45 THEN '36-45'
        ELSE '>45' 
    END as age_group,
    COUNT(CASE WHEN id_coordenador IS NULL THEN 1 END) as Colaborador,
    COUNT(CASE WHEN id_coordenador IS NOT NULL AND id_rh IS NULL THEN 1 END) as Coordenador,
    COUNT(CASE WHEN id_rh IS NOT NULL THEN 1 END) as RH
    FROM colaborador 
    WHERE estado = 'Ativo'
    GROUP BY age_group")->fetchAll(PDO::FETCH_ASSOC);
$hierarquiaEtariaData = array_map(function($row) {
    return [$row['Colaborador'], $row['Coordenador'], $row['RH']];
}, $hierarquiaEtariaData);

// Combinar os dados em um array
$data = [
    'retentionRate' => $retentionRate,
    'averageAge' => $averageAge,
    'averageTenure' => $averageTenure,
    'averageSalary' => $averageSalary,
    'hierarquiaData' => $hierarquiaData,
    'generoData' => $generoData,
    'funcaoData' => $funcaoData,
    'geografiaData' => $geografiaData,
    'tempoGeneroData' => $tempoGeneroData,
    'remuneracaoData' => $remuneracaoData,
    'hierarquiaEtariaData' => $hierarquiaEtariaData
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
    <?php //include 'includes/sidebar.php'; ?>

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
                <div class="col-12 col-lg-4 d-flex">
                    <div class="chart-box hierarquia-etaria flex-fill w-100">
                        <h2>Nível Hierárquico por Faixa Etária</h2>
                        <canvas id="chartHierarquiaEtaria"></canvas>
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
        echo '<pre>';
        print_r($data);
        echo '</pre>';exit;

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