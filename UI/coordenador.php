<?php
session_start();

// Verifica se o usuário está logado e é um coordenador
if (!isset($_SESSION['usuario_id'])) {
    header('Location: UI/login.php');
    exit;
}

// Verifica se o perfil é de coordenador (id_perfilacesso = 3)
if ($_SESSION['id_perfilacesso'] != 3) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Coordenador - Tlantic</title>
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0047ab;
            --primary-hover: #003d82;
            --secondary-color: #2c3e50;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-color: #dee2e6;
            --border-radius-md: 1rem;
            --spacing-sm: 0.75rem;
            --spacing-md: 1.25rem;
            --spacing-lg: 1.75rem;
            --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --transition-speed: 0.3s;
        }

        /* Sidebar Styles */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
            background-color: var(--light-color);
        }

        .sidebar {
            width: 280px;
            background-color: #f8f9fa;
            padding: var(--spacing-lg);
            border-right: 1px solid var(--border-color);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar .logo {
            text-align: center;
            margin-bottom: var(--spacing-lg);
        }

        .sidebar .logo img {
            max-width: 180px;
            height: auto;
        }

        .sidebar .user-info {
            background-color: white;
            padding: var(--spacing-md);
            border-radius: var(--border-radius-md);
            margin-bottom: var(--spacing-lg);
            box-shadow: var(--shadow-sm);
        }

        .sidebar .user-info h4 {
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }

        .sidebar .user-info p {
            color: var(--primary-color);
            font-size: 0.9rem;
            margin: 0;
        }

        .sidebar nav {
            margin-top: var(--spacing-lg);
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--secondary-color);
            text-decoration: none;
            border-radius: var(--border-radius-md);
            transition: all var(--transition-speed);
            margin-bottom: 0.5rem;
        }

        .sidebar .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.25rem;
        }

        .sidebar .nav-link:hover {
            background-color: var(--light-color);
            color: var(--primary-color);
        }

        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .sidebar .nav-link.active i {
            color: white;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: var(--spacing-lg);
            padding-top: calc(var(--spacing-lg) + 1rem);
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-lg);
            padding-bottom: var(--spacing-md);
            border-bottom: 1px solid var(--border-color);
        }

        .dashboard-title {
            color: var(--secondary-color);
            font-size: 2rem;
            font-weight: 600;
        }

        .filters {
            display: flex;
            gap: var(--spacing-md);
        }

        .filters select {
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-md);
            background-color: white;
            font-size: 0.9rem;
        }

        /* Dashboard Stats */
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-lg);
        }

        .stat-card {
            background-color: white;
            padding: var(--spacing-md);
            border-radius: var(--border-radius-md);
            box-shadow: var(--shadow-sm);
            transition: transform var(--transition-speed);
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card .stat-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: var(--spacing-sm);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--secondary-color);
            margin: 0;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: var(--spacing-sm);
        }

        /* Dashboard Cards */
        .dashboard-card {
            background-color: white;
            border-radius: var(--border-radius-md);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-speed);
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .dashboard-card .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-md);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: var(--spacing-md);
        }

        .dashboard-card h3 {
            color: var(--secondary-color);
            margin: 0;
            font-size: 1.5rem;
        }

        /* Team Table */
        .table-responsive {
            overflow-x: auto;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: var(--light-color);
            color: var(--secondary-color);
            font-weight: 600;
        }

        .table td {
            vertical-align: middle;
        }

        .table .badge {
            padding: 0.5em 1em;
            font-size: 0.875rem;
        }

        /* Action Buttons */
        .btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: var(--border-radius-md);
            transition: all var(--transition-speed);
        }

        .btn:hover {
            transform: scale(1.05);
        }

        /* Charts */
        .chart-container {
            height: 300px;
            margin-bottom: var(--spacing-lg);
        }

        /* Alert Cards */
        .alert-card {
            padding: var(--spacing-md);
            border-left: 4px solid var(--primary-color);
            margin-bottom: var(--spacing-md);
        }

        .alert-card .alert-time {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: var(--spacing-sm);
        }

        .alert-card .alert-message {
            color: var(--secondary-color);
            margin: 0;
        }

        /* Document Cards */
        .document-card {
            display: flex;
            align-items: center;
            padding: var(--spacing-md);
            border-bottom: 1px solid var(--border-color);
        }

        .document-card:last-child {
            border-bottom: none;
        }

        .document-icon {
            font-size: 2rem;
            color: var(--primary-color);
            margin-right: var(--spacing-md);
        }

        .document-info {
            flex: 1;
        }

        .document-info h6 {
            margin: 0 0 var(--spacing-sm) 0;
            color: var(--secondary-color);
        }

        .document-info p {
            margin: 0;
            color: #6c757d;
            font-size: 0.875rem;
        }

        .document-actions {
            display: flex;
            gap: var(--spacing-sm);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#dashboard">
                                <i class='bx bx-home'></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#equipe">
                                <i class='bx bx-group'></i>
                                Minha Equipe
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#alertas">
                                <i class='bx bx-bell'></i>
                                Alertas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#documentos">
                                <i class='bx bx-file'></i>
                                Documentos
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard do Coordenador</h1>
                </div>

                <!-- Dashboard Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class='bx bx-group'></i>
                            </div>
                            <div class="stat-content">
                                <h3 id="total-colaboradores">0</h3>
                                <p>Total de Colaboradores</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class='bx bx-calendar'></i>
                            </div>
                            <div class="stat-content">
                                <h3 id="media-idade">0</h3>
                                <p>Média de Idade</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class='bx bx-time'></i>
                            </div>
                            <div class="stat-content">
                                <h3 id="media-tempo">0</h3>
                                <p>Média de Tempo</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class='bx bx-bell'></i>
                            </div>
                            <div class="stat-content">
                                <h3 id="alertas-pendentes">0</h3>
                                <p>Alertas Pendentes</p>
                            </div>
                        </div>
                    <div id="documentosContainer">
                        <!-- Documentos serão carregados via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    // Inicializar Chart.js
    let idadeChart;
    let tempoChart;
    let funcaoChart;
    let generoChart;

    // Função para inicializar os gráficos
    function inicializarGraficos() {
        // Gráfico de Idade
        const ctxIdade = document.getElementById('idadeChart').getContext('2d');
        idadeChart = new Chart(ctxIdade, {
            type: 'bar',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Tempo na Empresa
        const ctxTempo = document.getElementById('tempoChart').getContext('2d');
        tempoChart = new Chart(ctxTempo, {
            type: 'line',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Função
        const ctxFuncao = document.getElementById('funcaoChart').getContext('2d');
        funcaoChart = new Chart(ctxFuncao, {
            type: 'pie',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true
            }
        });

        // Gráfico de Gênero
        const ctxGenero = document.getElementById('generoChart').getContext('2d');
        generoChart = new Chart(ctxGenero, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                responsive: true
            }
        });
    }

    // Função para atualizar os dados
    function atualizarDados() {
        fetch('services/coordenador/equipe.php?action=dashboard', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.totalColaboradores !== undefined) {
                // Atualizar estatísticas
                document.getElementById('totalColaboradores').textContent = data.totalColaboradores;
                document.getElementById('mediaIdade').textContent = data.mediaIdade;
                document.getElementById('mediaTempo').textContent = data.mediaTempo;
                document.getElementById('alertasPendentes').textContent = data.alertasPendentes;

                // Atualizar gráficos
                if (idadeChart) idadeChart.data = data.graficos.idade;
                if (tempoChart) tempoChart.data = data.graficos.tempo;
                if (funcaoChart) funcaoChart.data = data.graficos.funcao;
                if (generoChart) generoChart.data = data.graficos.genero;
                
                idadeChart.update();
                tempoChart.update();
                funcaoChart.update();
                generoChart.update();

                // Atualizar tabela de colaboradores
                const tabela = document.getElementById('equipeTableBody');
                tabela.innerHTML = '';

                data.colaboradores.forEach(colab => {
                    const row = tabela.insertRow();
                    row.innerHTML = `
                        <td>${colab.nome}</td>
                        <td>${colab.funcao}</td>
                        <td>${colab.tempoEmpresa}</td>
                        <td><span class="badge bg-${colab.status === 'ativo' ? 'success' : 'warning'}">${colab.status}</span></td>
                        <td>
                            <button class="btn btn-sm btn-info" onclick="verDetalhes(${colab.id})">
                                <i class='bx bx-detail'></i>
                            </button>
                            <button class="btn btn-sm btn-warning" onclick="verDocumentos(${colab.id})">
                                <i class='bx bx-file'></i>
                            </button>
                        </td>
                    `;
                });

                // Atualizar tabela de alertas
                const tabelaAlertas = document.getElementById('alertasContainer');
                tabelaAlertas.innerHTML = '';

                data.alertas.forEach(alerta => {
                    const row = tabelaAlertas.insertRow();
                    row.innerHTML = `
                        <div class="alert-card">
                            <div class="alert-time">${alerta.data}</div>
                            <h5 class="alert-message">${alerta.mensagem}</h5>
                            <div class="alert-actions">
                                <button class="btn btn-sm btn-primary" onclick="verAlerta(${alerta.id})">
                                    <i class='bx bx-detail'></i> Ver Detalhes
                                </button>
                            </div>
                        </div>
                    `;
                });

        // Função para atualizar gráficos
        function atualizarGraficos(graficos) {
            // Gráfico de Idade
            new Chart(document.getElementById('idadeChart'), {
                type: 'bar',
                data: graficos.idade,
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Gráfico de Tempo na Empresa
            new Chart(document.getElementById('tempoChart'), {
                type: 'line',
                data: graficos.tempo,
                options: {
                    responsive: true
                }
            });

            // Gráfico de Função
            new Chart(document.getElementById('funcaoChart'), {
                type: 'pie',
                data: graficos.funcao,
                options: {
                    responsive: true
                }
            });

            // Gráfico de Gênero
            new Chart(document.getElementById('generoChart'), {
                type: 'doughnut',
                data: graficos.genero,
                options: {
                    responsive: true
                }
            });
        }

        // Função para atualizar alertas
        function atualizarAlertas(alertas) {
            const container = document.getElementById('alertasContainer');
            container.innerHTML = alertas.map(alerta => `
                <div class="alert-card">
                    <div class="alert-time">${alerta.data}</div>
                    <h5 class="alert-message">${alerta.mensagem}</h5>
                    <div class="alert-actions">
                        <button class="btn btn-sm btn-primary" onclick="verAlerta(${alerta.id})">
                            <i class='bx bx-detail'></i> Ver Detalhes
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // Função para atualizar documentos pendentes
        function atualizarDocumentos(documentos) {
            const container = document.getElementById('documentosContainer');
            container.innerHTML = documentos.map(doc => `
                <div class="document-card">
                    <div class="document-icon">
                        <i class='bx bx-file'></i>
                    </div>
                    <div class="document-info">
                        <h6>${doc.nome}</h6>
                        <p>${doc.tipo} - ${doc.status}</p>
                        <p>${doc.data_upload}</p>
                    </div>
                    <div class="document-actions">
                        <button class="btn btn-sm btn-primary" onclick="verDocumento(${doc.id})">
                            <i class='bx bx-download'></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // Carregar dados ao iniciar
        document.addEventListener('DOMContentLoaded', carregarDadosEquipe);

        // Função para filtrar por equipe
        document.getElementById('equipeFilter').addEventListener('change', function() {
            const equipeId = this.value;
            window.location.href = `?equipe=${equipeId}`;
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Inicializar Chart.js
        let idadeChart;
        let tempoChart;
        let funcaoChart;
        let generoChart;

        // Função para inicializar os gráficos
        function inicializarGraficos() {
            // Gráfico de Idade
            const ctxIdade = document.getElementById('grafico-idade').getContext('2d');
            idadeChart = new Chart(ctxIdade, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: []
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Gráfico de Tempo na Empresa
            const ctxTempo = document.getElementById('grafico-tempo').getContext('2d');
            tempoChart = new Chart(ctxTempo, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: []
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Gráfico de Função
            const ctxFuncao = document.getElementById('grafico-funcao').getContext('2d');
            funcaoChart = new Chart(ctxFuncao, {
                type: 'pie',
                data: {
                    labels: [],
                    datasets: []
                },
                options: {
                    responsive: true
                }
            });

            // Gráfico de Gênero
            const ctxGenero = document.getElementById('grafico-genero').getContext('2d');
            generoChart = new Chart(ctxGenero, {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: []
                },
                options: {
                    responsive: true
                }
            });
        }

        // Função para atualizar os dados
        function atualizarDados() {
            fetch('services/coordenador/equipe.php?action=dashboard', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.totalColaboradores !== undefined) {
                    // Atualizar estatísticas
                    document.getElementById('total-colaboradores').textContent = data.totalColaboradores;
                    document.getElementById('media-idade').textContent = data.mediaIdade;
                    document.getElementById('media-tempo').textContent = data.mediaTempo;
                    document.getElementById('alertas-pendentes').textContent = data.alertasPendentes;

                    // Atualizar gráficos
                    if (idadeChart) idadeChart.data = data.graficos.idade;
                    if (tempoChart) tempoChart.data = data.graficos.tempo;
                    if (funcaoChart) funcaoChart.data = data.graficos.funcao;
                    if (generoChart) generoChart.data = data.graficos.genero;
                    
                    idadeChart.update();
                    tempoChart.update();
                    funcaoChart.update();
                    generoChart.update();

                    // Atualizar tabela de colaboradores
                    const tabela = document.getElementById('tabela-colaboradores');
                    const tbody = tabela.querySelector('tbody');
                    tbody.innerHTML = '';

                    data.colaboradores.forEach(colab => {
                        const row = tbody.insertRow();
                        row.innerHTML = `
                            <td>${colab.nome}</td>
                            <td>${colab.funcao}</td>
                            <td>${colab.status}</td>
                            <td>${colab.equipe_nome}</td>
                            <td>${dateDiff(colab.data_entrada)}</td>
                            <td>${dateDiff(colab.data_nascimento)}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="verAlertas(${colab.id})">
                                    <i class='bx bx-detail'></i> Ver Alertas
                                </button>
                            </td>
                        `;
                    });

                    // Atualizar tabela de alertas
                    const tabelaAlertas = document.getElementById('tabela-alertas');
                    const tbodyAlertas = tabelaAlertas.querySelector('tbody');
                    tbodyAlertas.innerHTML = '';

                    data.alertas.forEach(alerta => {
                        const row = tbodyAlertas.insertRow();
                        row.innerHTML = `
                            <td>${alerta.titulo}</td>
                            <td>${alerta.descricao}</td>
                            <td>${alerta.tipo}</td>
                            <td>${alerta.data_criacao}</td>
                            <td>
                                <button class="btn btn-sm btn-success" onclick="marcarComoLido(${alerta.id}, ${alerta.id_colaborador})">
                                    <i class='bx bx-check'></i> Marcar como Lido
                                </button>
                            </td>
                        `;
                    });

                    // Atualizar tabela de documentos
                    const tabelaDocumentos = document.getElementById('tabela-documentos');
                    const tbodyDocumentos = tabelaDocumentos.querySelector('tbody');
                    tbodyDocumentos.innerHTML = '';

                    data.documentos.forEach(doc => {
                        const row = tbodyDocumentos.insertRow();
                        row.innerHTML = `
                            <td>${doc.nome}</td>
                            <td>${doc.tipo}</td>
                            <td>${doc.data_upload}</td>
                            <td>${doc.status}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="verDocumento(${doc.id})">
                                    <i class='bx bx-file'></i> Ver Documento
                                </button>
                            </td>
                        `;
                    });
                } else {
                    console.error('Erro ao carregar dados:', data.error);
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
            });
        }

        // Função para marcar alerta como lido
        function marcarComoLido(alertaId, colaboradorId) {
            fetch('services/coordenador/equipe.php?action=marcar-alerta-como-lido', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    alertaId: alertaId,
                    colaboradorId: colaboradorId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    atualizarDados(); // Atualizar lista de alertas
                } else {
                    alert('Erro ao marcar alerta como lido');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao marcar alerta como lido');
            });
        }

        // Função para ver detalhes do alerta
        function verAlertas(colaboradorId) {
            fetch('services/coordenador/equipe.php?action=ver-alerta', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    colaboradorId: colaboradorId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data) {
                    // Exibir modal com detalhes do alerta
                    const modal = new bootstrap.Modal(document.getElementById('modalAlerta'));
                    document.getElementById('alerta-titulo').textContent = data.titulo;
                    document.getElementById('alerta-descricao').textContent = data.descricao;
                    document.getElementById('alerta-data').textContent = data.data_criacao;
                    document.getElementById('alerta-colaborador').textContent = data.colaborador_nome;
                    modal.show();
                } else {
                    alert('Alerta não encontrado');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao carregar detalhes do alerta');
            });
        }

        // Função para ver detalhes do documento
        function verDocumento(documentoId) {
            fetch('services/coordenador/equipe.php?action=ver-documento', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    documentoId: documentoId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data) {
                    // Exibir modal com detalhes do documento
                    const modal = new bootstrap.Modal(document.getElementById('modalDocumento'));
                    document.getElementById('documento-titulo').textContent = data.nome;
                    document.getElementById('documento-descricao').textContent = data.descricao;
                    document.getElementById('documento-data').textContent = data.data_upload;
                    document.getElementById('documento-colaborador').textContent = data.colaborador_nome;
                    modal.show();
                } else {
                    alert('Documento não encontrado');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao carregar detalhes do documento');
            });
        }

        // Função para calcular diferença de datas
        function dateDiff(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;
            const years = Math.floor(diff / (1000 * 60 * 60 * 24 * 365));
            const months = Math.floor((diff % (1000 * 60 * 60 * 24 * 365)) / (1000 * 60 * 60 * 24 * 30));
            return `${years} anos ${months} meses`;
        }

        // Inicializar gráficos e carregar dados quando a página carregar
        document.addEventListener('DOMContentLoaded', () => {
            inicializarGraficos();
            atualizarDados();
        });
    </script>
</body>
</html>
