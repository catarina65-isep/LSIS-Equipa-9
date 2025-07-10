<?php
// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Função auxiliar para obter a classe do badge de prioridade
function getPriorityBadgeClass($priority) {
    $priority = strtolower($priority);
    switch ($priority) {
        case 'alta':
            return 'danger';
        case 'média':
            return 'warning';
        case 'baixa':
            return 'success';
        default:
            return 'secondary';
    }
}

/**
 * Função para formatar o tempo decorrido de forma amigável
 */
function tempoDecorrido($data) {
    $agora = new DateTime();
    $data = new DateTime($data);
    $diferenca = $agora->diff($data);
    
    if ($diferenca->y > 0) {
        return $diferenca->y . ' ' . ($diferenca->y === 1 ? 'ano' : 'anos');
    } elseif ($diferenca->m > 0) {
        return $diferenca->m . ' ' . ($diferenca->m === 1 ? 'mês' : 'meses');
    } elseif ($diferenca->d > 0) {
        return $diferenca->d . ' ' . ($diferenca->d === 1 ? 'dia' : 'dias');
    } elseif ($diferenca->h > 0) {
        return $diferenca->h . ' ' . ($diferenca->h === 1 ? 'hora' : 'horas');
    } elseif ($diferenca->i > 0) {
        return $diferenca->i . ' ' . ($diferenca->i === 1 ? 'minuto' : 'minutos');
    } else {
        return 'alguns segundos';
    }
}

// Função auxiliar para obter a classe do badge de status
function getStatusBadgeClass($status) {
    if (empty($status)) return 'secondary';
    $status = strtolower($status);
    switch ($status) {
        case 'ativo':
        case 'success':
            return 'success';
        case 'pendente':
        case 'warning':
            return 'warning';
        case 'resolvido':
        case 'info':
            return 'info';
        case 'cancelado':
        case 'danger':
        case 'erro':
            return 'danger';
        case 'login':
            return 'success';
        case 'logout':
            return 'secondary';
        default:
            return 'primary';
    }
}

// Verifica se o usuário está logado e é administrador
if (!isset($_SESSION['utilizador_id']) || $_SESSION['id_perfilacesso'] != 1) {
    header('Location: /LSIS-Equipa-9/UI/login.php');
    exit;
}

// Inclui os arquivos necessários
require_once __DIR__ . '/../BLL/UtilizadorBLL.php';
require_once __DIR__ . '/../BLL/AlertaBLL.php';
require_once __DIR__ . '/../DAL/config.php';

$utilizadorBLL = new UtilizadorBLL();
$alertaBLL = new AlertaBLL();

// Obtém a conexão PDO
$db = Database::getInstance();
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Painel do Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="/LSIS-Equipa-9/UI/assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3f37c9;
            --success-color: #4cc9f0;
            --warning-color: #f8961e;
            --danger-color: #f72585;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Estilos para a barra lateral */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #2c3e50;
            color: #fff;
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }
        
        /* Estilo para o botão de alternar a sidebar em dispositivos móveis */
        .sidebar-toggle {
            font-size: 1.5rem;
            color: var(--primary-color);
            padding: 0.5rem;
            margin-left: -0.5rem;
        }
        
        .sidebar-toggle:hover {
            color: var(--primary-hover);
        }
        
        /* Estilo para quando a sidebar está aberta em dispositivos móveis */
        @media (max-width: 1199.98px) {
            .main-content {
                padding: 1.5rem;
            }
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1040;
            }
            
            body.sidebar-open .sidebar {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1rem;
                margin-right: 0;
            }
            
            .sidebar-overlay {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1039;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s;
            }
            
            body.sidebar-open .sidebar-overlay {
                opacity: 1;
                visibility: visible;
            }
        }

        /* Conteúdo principal */
        .main-content {
            padding: 2rem;
            margin-left: 250px; /* Largura da sidebar */
            transition: all 0.3s;
            min-height: 100vh;
            background-color: #f8f9fa;
            width: calc(100% - 250px); /* Ajusta a largura considerando a sidebar */
            max-width: 100%;
            box-sizing: border-box;
        }

        /* Estilos para os cabeçalhos */
        h1, h2, h3, h4, h5, h6 {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        h2 {
            font-size: 1.5rem;
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
            margin-top: 2rem;
        }

        /* Estilos para as tabelas */
        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #5a6a85;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }
        
        /* Melhorias na responsividade das tabelas */
        .table-responsive {
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        /* Ajustes para dispositivos móveis */
        @media (max-width: 767.98px) {
            .main-header {
                padding: 0.75rem 1rem;
            }
            
            .main-content {
                padding: 0.5rem;
            }
            
            section {
                padding: 1.5rem 0;
            }
            
            .card {
                border-radius: 0;
                margin-left: -0.5rem;
                margin-right: -0.5rem;
            }
        }

        /* Estilos para os cards */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background-color: #fff;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-color);
            display: flex;
            align-items: center;
        }

        .card-header h5 i {
            margin-right: 0.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Estilos para as tabelas */
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
            border-top: 1px solid #e9ecef;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #e9ecef;
            background-color: #f8f9fa;
            color: #5a6a85;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .table tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.03);
        }

        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        /* Estilos para mensagens e feedback */
        .no-data {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
            font-style: italic;
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .text-warning {
            color: #ffc107 !important;
        }

        /* Estilos para os badges */
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            border-radius: 50px;
            text-transform: capitalize;
        }

        .badge.bg-primary {
            background-color: rgba(67, 97, 238, 0.1) !important;
            color: var(--primary-color) !important;
        }

        .badge.bg-success {
            background-color: rgba(40, 167, 69, 0.1) !important;
            color: #28a745 !important;
        }

        .badge.bg-warning {
            background-color: rgba(255, 193, 7, 0.1) !important;
            color: #ffc107 !important;
        }

        .badge.bg-danger {
            background-color: rgba(220, 53, 69, 0.1) !important;
            color: #dc3545 !important;
        }

        .badge.bg-info {
            background-color: rgba(23, 162, 184, 0.1) !important;
            color: #17a2b8 !important;
        }
        
        /* Melhorias na responsividade */
        @media (max-width: 767.98px) {
            .main-header {
                padding: 0.75rem 1rem;
            }
            
            .main-content {
                padding: 0.5rem;
            }
            
            section {
                padding: 1.5rem 0;
            }
            
            .card {
                border-radius: 0;
                margin-left: -0.5rem;
                margin-right: -0.5rem;
                box-shadow: none;
                border: 1px solid rgba(0, 0, 0, 0.05);
            }
            
            .table-responsive {
                border-radius: 0;
                margin-left: -0.5rem;
                margin-right: -0.5rem;
                width: calc(100% + 1rem);
            }
        }
        
        /* Melhorias visuais */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .btn i {
            font-size: 1.1em;
            line-height: 1;
        }
        
        /* Melhorias na acessibilidade */
        :focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
        
        /* Animações suaves */
        a, button, .btn, .card, .badge, .table tr {
            transition: all 0.2s ease;
        }
        
        /* Melhorias no hover dos botões */
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        /* Melhorias na tabela */
        .table tbody tr {
            cursor: pointer;
        }
        
        .table tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.05) !important;
        }
        
        /* Melhorias nos cards */
        .card {
            overflow: hidden;
        }
        
        .card-header {
            position: relative;
        }
        
        .card-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-hover));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }
        
        .card:hover .card-header::after {
            transform: scaleX(1);
        }
        
        /* Melhorias na barra de rolagem */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Melhorias nos formulários */
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.1);
        }
        
        /* Efeito de loading */
        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }
        
        .loading {
            animation: pulse 1.5s infinite;
        }
        
        /* Melhorias na acessibilidade */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        
        /* Melhorias nos tooltips */
        [data-bs-toggle="tooltip"] {
            cursor: help;
            border-bottom: 1px dotted #666;
        }
    </style>
</head>
<body>
    <!-- Barra Lateral -->
    <nav class="sidebar">
        <div class="sidebar-header text-center py-4 border-bottom">
            <h3 class="mb-0">Painel de Administração</h3>
        </div>
        <ul class="sidebar-menu nav flex-column mt-4">
            <li class="nav-item active">

            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center" href="#atividades">
                    <i class='bx bxs-time me-2'></i>
                    <span>Gestão de Atividade</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center" href="administrador/atividades.php">
                    <i class='bx bx-history me-2'></i>
                    <span>Atividades de Acesso</span>
                </a>
            </li>
        </ul>
        <div class="sidebar-footer text-center mt-auto py-3 border-top">
            <a href="/LSIS-Equipa-9/UI/processa_logout.php" class="btn btn-danger w-100">
                <i class='bx bx-log-out'></i> Sair
            </a>
        </div>
    </nav>
    <div class="sidebar-overlay"></div>

    <!-- Conteúdo Principal -->
    <div class="main-content">
        <!-- Cabeçalho -->
        <header class="main-header d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div class="d-flex align-items-center">
                <button class="btn btn-link d-lg-none me-3 sidebar-toggle" aria-label="Abrir menu">
                    <i class='bx bx-menu'></i>
                </button>
                <h1 class="mb-0 h3 fw-bold">Painel do Administrador</h1>
            </div>
            <a href="/LSIS-Equipa-9/UI/processa_logout.php" class="btn btn-danger d-none d-lg-inline-flex align-items-center">
                <i class='bx bx-log-out me-1'></i> Sair
            </a>
        </header>

        <!-- Conteúdo Principal -->
        <main class="container-fluid px-0 px-md-4">


            <!-- Seção de Atividades -->
            <section id="atividades" class="py-4">
                <div class="row justify-content-center">
                    <div class="col-12 col-xl-11">
                        <h2 class="mb-4 d-flex align-items-center">
                            <i class='bx bxs-time me-2'></i>Gestão de Atividade
                        </h2>
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                                    <h5 class="mb-2 mb-md-0"><i class='bx bx-history me-2'></i>Histórico de Acessos</h5>
                                    <div class="d-flex">
                                        <a href="administrador/atividades.php" class="btn btn-sm btn-primary ms-2">
                                            <i class='bx bx-list-ul me-1'></i> Ver Todas as Atividades
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0" id="tabelaAtividades">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Data/Hora</th>
                                                <th>Utilizador</th>
                                                <th>Email</th>
                                                <th>Tipo</th>
                                                <th>IP</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            try {
                                                error_log("Carregando AtividadeDAL...");
                                                require_once __DIR__ . '/../DAL/AtividadeDAL.php';
                                                $atividadeDAL = new AtividadeDAL();
                                                error_log("Obtendo atividades recentes...");
                                                $atividades = $atividadeDAL->obterAtividadesRecentes(10);
                                                error_log("Total de atividades encontradas: " . count($atividades));
                                                error_log("Dados das atividades: " . print_r($atividades, true));
                                                
                                                if (empty($atividades)) {
                                                    error_log("Nenhuma atividade encontrada no banco de dados");
                                                    echo "<tr><td colspan='6' class='text-center py-4 text-muted'>
                                                        <i class='bx bx-time-five fs-1 d-block mb-2'></i>
                                                        Nenhuma atividade registrada ainda.
                                                    </td></tr>";
                                                } else {
                                                    foreach ($atividades as $atividade) {
                                                        $badgeClass = $atividade['tipo_atividade'] === 'login' ? 'success' : 'secondary';
                                                        $statusClass = ($atividade['status'] ?? 'sucesso') === 'sucesso' ? 'success' : 'danger';
                                                        
                                                        echo "<tr>";
                                                        echo "<td>" . date('d/m/Y H:i', strtotime($atividade['data_hora'])) . "</td>";
                                                        echo "<td>" . htmlspecialchars($atividade['nome_utilizador'] ?? 'N/A') . "</td>";
                                                        echo "<td>" . htmlspecialchars($atividade['email']) . "</td>";
                                                        echo "<td><span class='badge bg-{$badgeClass}'>" . ucfirst($atividade['tipo_atividade']) . "</span></td>";
                                                        echo "<td>" . htmlspecialchars($atividade['ip_address'] ?? 'N/A') . "</td>";
                                                        echo "<td><span class='badge bg-{$statusClass}'>" . ucfirst($atividade['status'] ?? 'sucesso') . "</span></td>";
                                                        echo "</tr>";
                                                    }
                                                }
                                            } catch (Exception $e) {
                                                echo "<tr><td colspan='6' class='text-center py-4 text-danger'>
                                                    <i class='bx bx-error-alt fs-1 d-block mb-2'></i>
                                                    Erro ao carregar atividades.<br>
                                                    <small class='text-muted'>" . htmlspecialchars($e->getMessage()) . "</small>
                                                </td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top-0 pt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small">
                                        Mostrando as últimas 10 atividades
                                    </div>
                                    <a href="administrador/atividades.php" class="btn btn-sm btn-outline-primary">
                                        Ver todas as atividades <i class='bx bx-chevron-right ms-1'></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

    <!-- Modal de Detalhes do Acesso -->
    <div class="modal fade" id="accessDetailsModal" tabindex="-1" aria-labelledby="accessDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="accessDetailsModalLabel"><i class='bx bx-detail me-2'></i>Detalhes do Acesso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3"><i class='bx bx-user me-2'></i>Informações do Usuário</h6>
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar-sm bg-light rounded-circle me-3 d-flex align-items-center justify-content-center">
                                        <i class='bx bx-user text-primary'></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0" id="detail-username">-</h5>
                                        <small class="text-muted" id="detail-email">-</small>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="small text-muted mb-1 d-block">ID do Usuário</label>
                                <span class="badge bg-light text-dark" id="detail-userid">-</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2 mb-3"><i class='bx bx-time me-2'></i>Detalhes do Acesso</h6>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="small text-muted mb-1 d-block">Ação</label>
                                    <span class="badge" id="detail-action">-</span>
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted mb-1 d-block">Módulo</label>
                                    <span id="detail-module">-</span>
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted mb-1 d-block">Data/Hora</label>
                                    <div id="detail-datetime">-</div>
                                    <small class="text-muted" id="detail-timeago">-</small>
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted mb-1 d-block">Endereço IP</label>
                                    <span class="font-monospace" id="detail-ip">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3"><i class='bx bx-info-circle me-2'></i>Informações Adicionais</h6>
                            <div class="bg-light p-3 rounded" id="detail-additional">
                                Nenhuma informação adicional disponível.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3"><i class='bx bx-laptop me-2'></i>Informações de Navegador</h6>
                            <div class="bg-light p-3 rounded" id="detail-useragent">
                                Nenhuma informação de navegador disponível.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class='bx bx-x me-1'></i> Fechar
                    </button>
                    <button type="button" class="btn btn-primary" id="copyDetails" data-bs-toggle="tooltip" title="Copiar detalhes">
                        <i class='bx bx-copy me-1'></i> Copiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Estilos para a tabela de logs */
        #accessLogsTable tbody tr {
            transition: all 0.2s ease;
        }
        #accessLogsTable tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }
        .avatar-sm {
            width: 32px;
            height: 32px;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .filter-active {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
            border-left: 3px solid var(--bs-primary);
        }
        @media (max-width: 767.98px) {
            .table-responsive {
                border-radius: 0.5rem;
                border: 1px solid rgba(0,0,0,.125);
            }
        }
    </style>
    
    <script>
    // Função para formatar a diferença de tempo
    function formatTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        if (diffInSeconds < 60) {
            return 'agora';
        }
        
        const diffInMinutes = Math.floor(diffInSeconds / 60);
        if (diffInMinutes < 60) {
            return `há ${diffInMinutes} min` + (diffInMinutes > 1 ? 's' : '');
        }
        
        const diffInHours = Math.floor(diffInMinutes / 60);
        if (diffInHours < 24) {
            return `há ${diffInHours} hora` + (diffInHours > 1 ? 's' : '');
        }
        
        const diffInDays = Math.floor(diffInHours / 24);
        return `há ${diffInDays} dia` + (diffInDays > 1 ? 's' : '');
    }
    
    // Filtra os logs na tabela
    function filterLogs() {
        const userFilter = $('#filterUser').val().toLowerCase();
        const actionFilter = $('#filterAction').val().toLowerCase();
        const moduleFilter = $('#filterModule').val().toLowerCase();
        
        let visibleCount = 0;
        
        $('#accessLogsTable tbody tr').each(function() {
            const userText = $(this).data('user') || '';
            const actionText = $(this).data('action') || '';
            const moduleText = $(this).data('module') || '';
            
            const userMatch = userText.includes(userFilter);
            const actionMatch = !actionFilter || actionText === actionFilter.toLowerCase();
            const moduleMatch = moduleText.includes(moduleFilter);
            
            const isVisible = userMatch && actionMatch && moduleMatch;
            $(this).toggle(isVisible);
            
            if (isVisible) {
                visibleCount++;
            }
        });
        
        // Atualiza contador
        updateVisibleCount(visibleCount);
    }
    
    // Atualiza o contador de registros visíveis
    function updateVisibleCount(visibleCount = null) {
        if (visibleCount === null) {
            visibleCount = $('#accessLogsTable tbody tr:visible').length;
        }
        const totalCount = $('#accessLogsTable tbody tr').length;
        $('#logCount').html(`<i class='bx bx-filter-alt'></i> Mostrando <strong>${visibleCount}</strong> de <strong>${totalCount}</strong> registros`);
        
        // Atualiza o título da página com a contagem
        document.title = `(${visibleCount}/${totalCount}) Histórico de Acessos - Painel Administrativo`;
    }
    
    // Atualiza os tempos decorridos periodicamente
    function updateTimeAgo() {
        $('.time-ago').each(function() {
            const dateString = $(this).data('time');
            $(this).text(formatTimeAgo(dateString));
        });
    }
    
    // Inicialização quando o documento estiver pronto
    $(document).ready(function() {
        // Inicializa tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Atualiza os tempos iniciais
        updateTimeAgo();
        updateVisibleCount();
        
        // Configura os filtros
        let filterTimeout;
        $('.form-control, .form-select').on('input change', function() {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(filterLogs, 300);
        });
        
        // Botão limpar filtros
        $('#clearFilters').on('click', function() {
            $('#filterUser, #filterModule').val('');
            $('#filterAction').val('');
            filterLogs();
        });
        
        // Botão de atualizar
        $('#refreshLogs').on('click', function() {
            const $btn = $(this);
            const originalHtml = $btn.html();
            
            $btn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Atualizando...'
            );
            
            // Simula um atraso para mostrar o loading
            setTimeout(function() {
                location.reload();
            }, 500);
            
            // Restaura o botão após 5 segundos (caso a página não recarregue)
            setTimeout(function() {
                $btn.prop('disabled', false).html(originalHtml);
            }, 5000);
        });
        
        // Inicializa DataTable com configurações personalizadas
        const table = $('#accessLogsTable').DataTable({
            // Desativa a pesquisa padrão do DataTables para usar nossa implementação
            searching: false,
            responsive: true,
            order: [[4, 'desc']], // Ordena pela coluna de data (índice 4) de forma decrescente
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
            },
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            columnDefs: [
                { responsivePriority: 1, targets: 0 }, // Utilizador
                { responsivePriority: 2, targets: 1 }, // Ação
                { responsivePriority: 4, targets: 2 }, // Módulo
                { responsivePriority: 5, targets: 3 }, // IP
                { responsivePriority: 3, targets: 4 }  // Data/Hora
            ]
        });
        
        // Aplica os filtros ao DataTable
        $('#filterUser, #filterModule').on('keyup', function() {
            table.column($(this).data('column')).search(this.value).draw();
        });
        
        $('#filterAction').on('change', function() {
            const val = $(this).val();
            table.column(1).search(val).draw();
        });
    });
    
    // Função para formatar dados adicionais
    function formatAdditionalData(data) {
        if (!data) return 'Nenhuma informação adicional disponível.';
        
        try {
            // Se for string, tenta fazer parse de JSON
            const jsonData = typeof data === 'string' ? JSON.parse(data) : data;
            
            if (typeof jsonData !== 'object') return String(data);
            
            let html = '<div class="list-group list-group-flush">';
            
            for (const [key, value] of Object.entries(jsonData)) {
                if (value === null || value === '') continue;
                
                const formattedKey = key.replace(/([A-Z])/g, ' $1')
                    .replace(/^./, str => str.toUpperCase())
                    .replace(/_/g, ' ');
                
                let formattedValue = value;
                
                // Formata valores específicos
                if (key.toLowerCase().includes('date') || key.toLowerCase().includes('hora')) {
                    try {
                        const date = new Date(value);
                        if (!isNaN(date.getTime())) {
                            formattedValue = date.toLocaleString('pt-PT');
                        }
                    } catch (e) {}
                }
                
                html += `
                    <div class="list-group-item bg-transparent px-0 py-2">
                        <div class="d-flex justify-content-between">
                            <span class="fw-medium">${formattedKey}:</span>
                            <span class="text-end">${formattedValue}</span>
                        </div>
                    </div>`;
            }
            
            return html + '</div>';
        } catch (e) {
            return String(data);
        }
    }
    
    // Abre o modal com os detalhes do acesso
    function showAccessDetails(row) {
        const $row = $(row);
        const modal = new bootstrap.Modal(document.getElementById('accessDetailsModal'));
        
        // Preenche os dados do modal
        $('#detail-username').text($row.data('username') || 'N/A');
        $('#detail-email').text($row.data('email') || 'N/A');
        $('#detail-userid').text($row.data('userid') || 'N/A');
        
        // Formata a ação
        const action = $row.data('action') || 'Desconhecida';
        const actionBadge = `<span class="badge bg-${getStatusBadgeClass(action)}">${action}</span>`;
        $('#detail-action').html(actionBadge);
        
        // Preenche os demais campos
        $('#detail-module').text($row.data('module') || 'N/A');
        $('#detail-ip').text($row.data('ip') || 'N/A');
        
        // Formata a data/hora
        const date = new Date($row.data('datetime') || new Date());
        const formattedDate = date.toLocaleString('pt-PT');
        $('#detail-datetime').text(formattedDate);
        $('#detail-timeago').text(formatTimeAgo($row.data('datetime')) + ' atrás');
        
        // Formata os dados adicionais
        try {
            const additionalData = $row.data('additional') || {};
            $('#detail-additional').html(formatAdditionalData(additionalData));
        } catch (e) {
            console.error('Erro ao processar dados adicionais:', e);
            $('#detail-additional').text('Erro ao carregar informações adicionais.');
        }
        
        // Informações do navegador
        const userAgent = $row.data('useragent') || 'N/A';
        $('#detail-useragent').text(userAgent);
        
        // Exibe o modal
        modal.show();
    }
    
    // Atualiza os tempos a cada minuto
    setInterval(updateTimeAgo, 60000);
    
    // Atualiza o contador inicial
    $(window).on('load', function() {
        updateVisibleCount();
        
        // Configura o clique nas linhas da tabela
        $('#accessLogsTable tbody').on('click', 'tr', function() {
            showAccessDetails(this);
        });
        
        // Botão de copiar detalhes
        $('#copyDetails').on('click', function() {
            const modalContent = document.getElementById('accessDetailsModal').innerText;
            navigator.clipboard.writeText(modalContent).then(() => {
                const $btn = $(this);
                const originalHtml = $btn.html();
                $btn.html('<i class="bx bx-check"></i> Copiado!');
                setTimeout(() => {
                    $btn.html(originalHtml);
                }, 2000);
            }).catch(err => {
                console.error('Erro ao copiar:', err);
            });
        });
    });
    </script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/dist/boxicons.js"></script>
    
    <script>
        $(document).ready(function() {
            // Inicializar DataTables
            $('table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-PT.json",
                    "search": "<i class='bx bx-search'></i> Pesquisar:",
                    "lengthMenu": "Mostrar _MENU_ registos por página",
                    "zeroRecords": "Nenhum registo encontrado",
                    "info": "A mostrar página _PAGE_ de _PAGES_",
                    "infoEmpty": "Nenhum registo disponível",
                    "infoFiltered": "(filtrado de _MAX_ registos no total)",
                    "paginate": {
                        "previous": "<i class='bx bx-chevron-left'></i>",
                        "next": "<i class='bx bx-chevron-right'></i>"
                    }
                },
                "order": [[0, "desc"]],
                "pageLength": 10,
                "responsive": true,
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                "initComplete": function() {
                    $('.dataTables_filter input').addClass('form-control');
                    $('.dataTables_length select').addClass('form-select');
                }
            });

            // Toggle da sidebar em dispositivos móveis
            $('.sidebar-toggle').on('click', function() {
                $('body').toggleClass('sidebar-open');
            });

            // Fechar sidebar ao clicar no overlay
            $('.sidebar-overlay').on('click', function() {
                $('body').removeClass('sidebar-open');
            });

            // Fechar sidebar ao clicar em um item do menu em dispositivos móveis
            $('.sidebar-menu a').on('click', function() {
                if ($(window).width() < 992) {
                    $('body').removeClass('sidebar-open');
                }
            });

            // Ativar item ativo na sidebar
            $('.sidebar-menu a').on('click', function() {
                $('.sidebar-menu li').removeClass('active');
                $(this).parent('li').addClass('active');
            });

            // Rolagem suave para as seções
            $('a[href^="#"]').on('click', function(e) {
                e.preventDefault();
                var target = $(this).attr('href');
                if (target === '#') return;
                
                $('html, body').animate({
                    scrollTop: $(target).offset().top - 20
                }, 500);
            });

            // Atualizar item ativo na sidebar ao rolar
            $(window).on('scroll', function() {
                var scrollPosition = $(window).scrollTop() + 100;
                
                $('section').each(function() {
                    var currentSection = $(this);
                    var sectionTop = currentSection.offset().top;
                    var sectionBottom = sectionTop + currentSection.outerHeight();
                    
                    if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
                        var sectionId = '#' + currentSection.attr('id');
                        $('.sidebar-menu li').removeClass('active');
                        $('.sidebar-menu a[href="' + sectionId + '"]').parent('li').addClass('active');
                    }
                });
            });

            // Atualizar o item ativo no carregamento da página
            $(window).trigger('scroll');
        });
    </script>

    <?php $db = null; ?>
</body>
</html>
