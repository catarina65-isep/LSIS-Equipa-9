<?php
session_start();

// Verificar autenticação e perfil RH
if (!isset($_SESSION['usuario_id']) || $_SESSION['id_perfilacesso'] != 2) {
    header('Location: ../login.php');
    exit;
}

$page_title = "Dashboard de Colaboradores - RH";

// Conexão com o banco de dados
require_once __DIR__ . '/../BLL/ColaboradorBLL.php';
$colaboradorBLL = new ColaboradorBLL();

// Obter dados de colaboradores ativos
$colaboradores = $colaboradorBLL->obterColaboradoresAtivos();
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
        
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card h3 {
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        .chart-container {
            height: 300px;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        select {
            width: 100%;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Incluir a barra lateral -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Colaboradores</li>
                </ol>
            </nav>
            <div>
                <a href="colaboradores.php" class="btn btn-outline-primary">
                    <i class='bx bx-user-plus'></i> Novo Colaborador
                </a>
            </div>
        </div>

        <div class="dashboard">
            <div class="card">
                <h3>Distribuição da Idade</h3>
                <canvas id="idadeChart" class="chart-container"></canvas>
            </div>

            <div class="card">
                <h3>Distribuição por Género</h3>
                <label for="filtroGenero">Filtrar por Género:</label>
                <select id="filtroGenero">
                    <option value="Todos">Todos</option>
                </select>
                <canvas id="generoChart" class="chart-container"></canvas>
            </div>

            <div class="card">
                <h3>Distribuição por País</h3>
                <label for="filtroPais">Filtrar por País:</label>
                <select id="filtroPais">
                    <option value="Todos">Todos</option>
                </select>
                <canvas id="paisChart" class="chart-container"></canvas>
            </div>
        </div>
    </div>

    <script>
        const colaboradores = <?php echo json_encode($colaboradores); ?>;

        function calcularIdade(dataNascimento) {
            const nascimento = new Date(dataNascimento);
            const hoje = new Date();
            let idade = hoje.getFullYear() - nascimento.getFullYear();
            const m = hoje.getMonth() - nascimento.getMonth();
            if (m < 0 || (m === 0 && hoje.getDate() < nascimento.getDate())) {
                idade--;
            }
            return idade;
        }

        // === Gráfico de Idades ===
        const idades = colaboradores.map(c => calcularIdade(c.data_nascimento));
        new Chart(document.getElementById('idadeChart'), {
            type: 'bar',
            data: {
                labels: idades.map((_, i) => `Colab. ${i + 1}`),
                datasets: [{
                    label: 'Idade dos Colaboradores',
                    data: idades,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { title: { display: true, text: 'Colaboradores' } },
                    y: { beginAtZero: true, title: { display: true, text: 'Idade' } }
                }
            }
        });

        // === Utilidades ===
        function contarPorCampo(array, campo) {
            const contagem = {};
            array.forEach(item => {
                contagem[item[campo]] = (contagem[item[campo]] || 0) + 1;
            });
            return contagem;
        }

        const filtroGenero = document.getElementById('filtroGenero');
        const filtroPais = document.getElementById('filtroPais');

        const generosUnicos = [...new Set(colaboradores.map(c => c.genero))];
        const paisesUnicos = [...new Set(colaboradores.map(c => c.pais))];

        generosUnicos.forEach(g => {
            const opt = document.createElement('option');
            opt.value = g;
            opt.textContent = g;
            filtroGenero.appendChild(opt);
        });

        paisesUnicos.forEach(p => {
            const opt = document.createElement('option');
            opt.value = p;
            opt.textContent = p;
            filtroPais.appendChild(opt);
        });

        let generoChart, paisChart;

        function atualizarGeneroChart(filtro) {
            const dados = filtro === "Todos"
                ? colaboradores
                : colaboradores.filter(c => c.genero === filtro);

            const contagem = contarPorCampo(dados, 'genero');

            const ctx = document.getElementById('generoChart').getContext('2d');
            if (generoChart) generoChart.destroy();
            generoChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: Object.keys(contagem),
                    datasets: [{
                        data: Object.values(contagem),
                        backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56', '#8e44ad']
                    }]
                },
                options: {
                    responsive: true
                }
            });
        }

        function atualizarPaisChart(filtro) {
            const dados = filtro === "Todos"
                ? colaboradores
                : colaboradores.filter(c => c.pais === filtro);

            const contagem = contarPorCampo(dados, 'pais');

            const ctx = document.getElementById('paisChart').getContext('2d');
            if (paisChart) paisChart.destroy();
            paisChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(contagem),
                    datasets: [{
                        label: 'Nº de Colaboradores por País',
                        data: Object.values(contagem),
                        backgroundColor: 'rgba(255, 159, 64, 0.7)'
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: { display: true, text: 'Nº de Colaboradores' }
                        },
                        y: {
                            title: { display: true, text: 'Países' }
                        }
                    }
                }
            });
        }

        filtroGenero.addEventListener('change', () => {
            atualizarGeneroChart(filtroGenero.value);
        });

        filtroPais.addEventListener('change', () => {
            atualizarPaisChart(filtroPais.value);
        });

        // Inicializar com "Todos"
        atualizarGeneroChart("Todos");
        atualizarPaisChart("Todos");
    </script>
</body>
</html>
