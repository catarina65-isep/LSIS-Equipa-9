<?php
session_start();

// Verificar autenticação e perfil RH
if (!isset($_SESSION['usuario_id']) || $_SESSION['id_perfilacesso'] != 2) {
    header('Location: ../login.php');
    exit;
}

$page_title = "Dashboard RH - Tlantic";

// Dados simulados para o dashboard
$totalColaboradores = 156;
$totalAtivos = 145;
$totalPeriodoExperiencia = 12;

// Dados para gráficos
$meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
$contratacoesPorMes = [12, 15, 18, 20, 22, 25, 24, 23, 21, 19, 16, 14];
$turnoverPorMes = [2, 3, 2, 1, 2, 3, 1, 2, 3, 2, 1, 2];
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($page_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            background: var(--dark-bg);
            color: #fff;
            padding: 20px 0;
            width: var(--sidebar-width);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            transition: transform 0.3s ease;
        }
        
        .sidebar.show {
            transform: translateX(var(--sidebar-width));
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .stat-label {
            font-size: 14px;
            color: #6c757d;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            background: #fff;
            border-bottom: 1px solid #eee;
            padding: 15px 20px;
            border-radius: 12px 12px 0 0;
        }
        
        .table {
            background: #fff;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="text-center mb-4">
            <h4>Painel RH</h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link active">
                    <i class='bx bxs-dashboard'></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="colaboradores.php" class="nav-link">
                    <i class='bx bxs-user-detail'></i> Colaboradores
                </a>
            </li>
            <li class="nav-item">
                <a href="documentos.php" class="nav-link">
                    <i class='bx bxs-file-doc'></i> Documentos
                </a>
            </li>
            <li class="nav-item">
                <a href="relatorios.php" class="nav-link">
                    <i class='bx bxs-report'></i> Relatórios
                </a>
            </li>
            <li class="nav-item">
                <a href="configuracoes.php" class="nav-link">
                    <i class='bx bxs-cog'></i> Configurações
                </a>
            </li>
            <li class="nav-item mt-4">
                <a href="../logout.php" class="nav-link text-danger">
                    <i class='bx bx-log-out'></i> Sair
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
            <div>
                <a href="colaboradores.php" class="btn btn-outline-secondary">
                    <i class='bx bx-user-plus'></i> Novo Colaborador
                </a>
            </div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary text-white p-3 rounded me-3">
                            <i class='bx bxs-user-detail'></i>
                        </div>
                        <div>
                            <div class="stat-value"><?php echo $totalColaboradores; ?></div>
                            <div class="stat-label">Total de Colaboradores</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success text-white p-3 rounded me-3">
                            <i class='bx bxs-user-check'></i>
                        </div>
                        <div>
                            <div class="stat-value"><?php echo $totalAtivos; ?></div>
                            <div class="stat-label">Colaboradores Ativos</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning text-white p-3 rounded me-3">
                            <i class='bx bxs-user-detail'></i>
                        </div>
                        <div>
                            <div class="stat-value"><?php echo $totalPeriodoExperiencia; ?></div>
                            <div class="stat-label">Em Período de Experiência</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Evolução de Contratações</h5>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="contratacoesDropdown" data-bs-toggle="dropdown">
                                Anual
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="contratacoesDropdown">
                                <li><a class="dropdown-item" href="#">Mensal</a></li>
                                <li><a class="dropdown-item" href="#">Trimestral</a></li>
                                <li><a class="dropdown-item" href="#">Anual</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="contratacoesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Taxa de Turnover</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="turnoverChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gráfico de Contratações
        new Chart(document.getElementById('contratacoesChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($meses); ?>,
                datasets: [{
                    label: 'Contratações',
                    data: <?php echo json_encode($contratacoesPorMes); ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Turnover
        new Chart(document.getElementById('turnoverChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($meses); ?>,
                datasets: [{
                    label: 'Taxa de Turnover',
                    data: <?php echo json_encode($turnoverPorMes); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.5)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
