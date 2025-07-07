<?php
// Verifica se a sessão não está ativa e inicia uma nova
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define variáveis padrão
$page_title = isset($page_title) ? $page_title : 'Tlantic';
$pagina_atual = isset($pagina_atual) ? $pagina_atual : '';

// Inclui o arquivo de configuração do banco de dados
require_once __DIR__ . '/../../DAL/config.php';

// Inicializa a conexão com o banco de dados
$pdo = Database::getInstance();
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($page_title) ?></title>
    
    <!-- Box Icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/LSIS-Equipa-9/UI/assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="/LSIS-Equipa-9/UI/assets/img/favicon.ico" type="image/x-icon">
    
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --info-color: #4895ef;
            --warning-color: #f4a261;
            --danger-color: #e76f51;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            overflow-x: hidden;
        }
        
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width) !important;
            min-height: 100vh;
            position: fixed !important;
            left: 0 !important;
            top: 0 !important;
            bottom: 0 !important;
            z-index: 1000 !important;
            background: linear-gradient(180deg, #1a2a3a 0%, #2c3e50 100%) !important;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1) !important;
            transition: all 0.3s !important;
            overflow-y: auto !important;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s;
            background-color: #f5f7fa;
        }
        
        /* Top Bar */
        .top-bar {
            background: #fff;
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Estatísticas */
        .stat-card {
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
            border: none;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            position: absolute;
            right: 20px;
            top: 20px;
            opacity: 0.2;
            font-size: 4rem;
        }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        /* Tabelas */
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            border-top: none;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #6c757d;
        }
        
        /* Botões */
        .btn {
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.8125rem;
        }
        
        /* Cores de fundo */
        .bg-primary { background-color: var(--primary-color) !important; }
        .bg-success { background-color: var(--success-color) !important; }
        .bg-info { background-color: var(--info-color) !important; }
        .bg-warning { background-color: var(--warning-color) !important; }
        .bg-danger { background-color: var(--danger-color) !important; }
        
        /* Textos */
        .text-white-80 { color: rgba(255, 255, 255, 0.8); }
        .text-white-60 { color: rgba(255, 255, 255, 0.6); }
        
        /* Hover effects */
        .hover-bg-dark-10:hover { background-color: rgba(0, 0, 0, 0.1) !important; }
        
        /* Responsividade */
        @media (max-width: 991.98px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            
            .sidebar.show {
                margin-left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Incluir a barra lateral -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="d-flex align-items-center">
                <button class="btn btn-link d-md-none me-3" id="sidebarToggle">
                    <i class='bx bx-menu'></i>
                </button>
                <h4 class="mb-0"><?= htmlspecialchars($page_title) ?></h4>
            </div>
            
            <div class="d-flex align-items-center
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar avatar-sm">
                            <div class="avatar-initial bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class='bx bx-user'></i>
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="dropdownUser">
                        <li><a class="dropdown-item" href="#"><i class='bx bx-user me-2'></i> Meu Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class='bx bx-cog me-2'></i> Configurações</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="/LSIS-Equipa-9/UI/logout.php"><i class='bx bx-log-out me-2'></i> Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Conteúdo Principal -->
        <div class="container-fluid py-4">
