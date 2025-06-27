<?php
session_start();

// Verifica se o usuário está logado e tem permissão de RH
if (!isset($_SESSION['usuario_id']) || $_SESSION['id_perfilacesso'] != 3) {
    header('Location: ../login.php');
    exit;
}

$page_title = "Dashboard RH - Tlantic";
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Estilos personalizados -->
    <link href="css/style-rh.css" rel="stylesheet">
    
    <!-- Canvas.js -->
    <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
    
    <!-- SimpleStats.js -->
    <script src="https://cdn.jsdelivr.net/npm/simplestats@latest/dist/simplestats.min.js"></script>
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
                <span>Bem-vindo, <?= $_SESSION['nome'] ?></span>
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
                            <h2 class="card-text" id="totalCollaborators">Carregando...</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Aniversários</h5>
                            <h2 class="card-text" id="birthdays">Carregando...</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Alertas</h5>
                            <h2 class="card-text" id="pendingAlerts">Carregando...</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Novos</h5>
                            <h2 class="card-text" id="newHires">Carregando...</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="row mb-4">
                <!-- Distribuição por Gênero -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Distribuição por Gênero</h5>
                            <div id="genderChart"></div>
                        </div>
                    </div>
                </div>

                <!-- Evolução de Colaboradores -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Evolução de Colaboradores</h5>
                            <div id="evolutionChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mais Gráficos -->
            <div class="row mb-4">
                <!-- Distribuição por Departamento -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Departamentos</h5>
                            <div id="departmentChart"></div>
                        </div>
                    </div>
                </div>

                <!-- Distribuição Hierárquica -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Hierarquia</h5>
                            <div id="hierarchyChart"></div>
                        </div>
                    </div>
                </div>

                <!-- Taxa de Rotatividade -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Taxa de Rotatividade</h5>
                            <div id="turnoverChart"></div>
                        </div>
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
                                        <!-- Preenchido via JavaScript -->
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
                                        <!-- Preenchido via JavaScript -->
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
    <script src="js/dashboard.js"></script>
</body>
</html>
