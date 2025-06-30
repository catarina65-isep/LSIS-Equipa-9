<?php
// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Habilita a exibição de erros para depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log de depuração completo
error_log('========================================');
error_log('Tentando acessar RH. Dados da sessão: ' . print_r($_SESSION, true));
error_log('Cookie da sessão: ' . print_r($_COOKIE, true));
error_log('ID da sessão: ' . session_id());

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    error_log('ERRO: Usuário não está logado. Sessão: ' . print_r($_SESSION, true));
    error_log('Redirecionando para login.php');
    header('Location: login.php');
    exit;
}

// Verifica se o perfil de acesso está definido
if (!isset($_SESSION['id_perfilacesso'])) {
    error_log('ERRO: id_perfilacesso não está definido na sessão');
    error_log('Dados completos da sessão: ' . print_r($_SESSION, true));
    session_destroy();
    header('Location: login.php?error=perfil_nao_definido');
    exit;
}

// Verifica se o usuário tem permissão de RH (ID 2)
$perfil = $_SESSION['id_perfilacesso'];
error_log("Perfil do usuário: " . $perfil . " (Esperado: 2 para RH)");

if ($perfil != 2) {
    error_log('ACESSO NEGADO: O usuário não tem permissão de RH. Perfil: ' . $perfil);
    error_log('Dados da sessão: ' . print_r($_SESSION, true));
    $_SESSION['erro'] = 'Você não tem permissão para acessar esta área. Seu perfil: ' . ($_SESSION['perfil_nome'] ?? 'Desconhecido') . ' (ID: ' . $perfil . ')';
    header('Location: ../index.php?error=acesso_negado');
    exit;
}

error_log('ACESSO PERMITIDO ao RH para o usuário ID: ' . $_SESSION['usuario_id']);
error_log('Dados da sessão: ' . print_r($_SESSION, true));
error_log('========================================');

$page_title = "Dashboard RH - Tlantic";
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link href="../css/style-rh.css" rel="stylesheet">
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
            <!-- Dashboard Cards -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Número de Colaboradores</h5>
                            <h2 class="card-text" id="totalCollaborators">Carregando...</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Aniversários do Mês</h5>
                            <h2 class="card-text" id="birthdays">Carregando...</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Alertas Pendentes</h5>
                            <h2 class="card-text" id="pendingAlerts">Carregando...</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Métricas de Equipe -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Idade Média</h5>
                            <h2 class="card-text" id="averageAge">Carregando...</h2>
                            <p class="text-muted">Anos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Tempo Médio na Empresa</h5>
                            <h2 class="card-text" id="averageTimeInCompany">Carregando...</h2>
                            <p class="text-muted">Anos</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Taxa de Retenção</h5>
                            <h2 class="card-text" id="retentionRate">Carregando...</h2>
                            <p class="text-muted">%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Distribuições -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Distribuição por Gênero</h5>
                            <canvas id="genderDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Distribuição Hierárquica</h5>
                            <canvas id="hierarchyDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aniversariantes do Mês -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Aniversariantes do Mês</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Departamento</th>
                                    <th>Data de Aniversário</th>
                                </tr>
                            </thead>
                            <tbody id="birthdaysTableBody">
                                <!-- Será preenchido via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Alertas Pendentes -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Alertas Pendentes</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Colaborador</th>
                                    <th>Tipo de Alerta</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="alertsTableBody">
                                <!-- Será preenchido via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="../js/rh.js"></script>
</body>
</html>
