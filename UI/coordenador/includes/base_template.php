<?php
/**
 * Template base para as páginas do painel do coordenador
 */

// Define o tipo de conteúdo como HTML com UTF-8
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Painel do Coordenador - Tlantic') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Box Icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- CSS Personalizado -->
    <style>
        :root {
            --sidebar-width: 250px;
            --topbar-height: 60px;
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --warning-color: #f4a261;
            --danger-color: #e76f51;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }

        /* Reset de margens e padding */
        html, body {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            overflow-x: hidden;
        }

        /* Layout principal */
        .wrapper {
            display: flex;
            min-height: 100vh;
            padding-left: var(--sidebar-width);
            width: 100%;
        }

        /* Conteúdo principal */
        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #f5f7fb;
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
        }

        /* Ajuste para telas menores */
        @media (max-width: 992px) {
            .wrapper {
                padding-left: 0;
            }
            
            .main-content {
                width: 100%;
                margin-left: 0;
            }
            
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
        }

        /* Cabeçalho fixo */
        .top-bar {
            background: #fff;
            padding: 15px 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
        }

        /* Remove margens extras */
        .container, .container-fluid {
            padding: 0 15px !important;
            margin: 0 auto !important;
            max-width: 100% !important;
        }

        /* Ajuste para cards */
        .card {
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: none;
            transition: all 0.3s;
        }

        .card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 15px 20px;
            border-radius: 8px 8px 0 0 !important;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 600;
            color: #2c3e50;
        }

        .card-body {
            padding: 20px;
        }

        /* Estilo para os cards de estatísticas */
        .stat-card {
            padding: 20px;
            border-radius: 8px;
            color: white;
            margin-bottom: 20px;
            transition: transform 0.3s;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 2.5rem;
            opacity: 0.8;
            margin-bottom: 15px;
        }

        .stat-card h3 {
            font-size: 1.8rem;
            margin: 10px 0 5px;
            font-weight: 700;
        }

        .stat-card p {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Cores dos cards */
        .bg-primary { background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%); }
        .bg-success { background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%); }
        .bg-warning { background: linear-gradient(135deg, #f4a261 0%, #f8961e 100%); }
        .bg-danger { background: linear-gradient(135deg, #e76f51 0%, #f72585 100%); }

        /* Ajuste para tabelas */
        .table {
            margin-bottom: 0;
            width: 100%;
        }

        .table th {
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
            border-top: none;
            padding: 12px 15px;
        }

        .table td {
            padding: 12px 15px;
            vertical-align: middle;
        }

        /* Efeito de hover nas linhas da tabela */
        .table-hover > tbody > tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        /* Ajuste para os badges de status */
        .badge {
            font-weight: 500;
            padding: 0.4em 0.8em;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        .badge.bg-success { background-color: #4cc9f0 !important; }
        .badge.bg-warning { background-color: #f4a261 !important; color: #000; }

        /* Animações */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        /* Estilos para o breadcrumb personalizado */
        .breadcrumb {
            background-color: transparent;
            margin-bottom: 0;
            padding: 0.5rem 0;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.2s;
        }

        .breadcrumb-item a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        .breadcrumb-item.active {
            color: #6c757d;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "\203A";
            padding: 0 0.5rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- Inclui a barra lateral -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="wrapper">
        <div class="main-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link text-dark d-lg-none me-2" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4 class="mb-0"><?= htmlspecialchars($page_heading ?? 'Dashboard') ?></h4>
                </div>
                <div class="d-flex align-items-center">
                    <!-- Menu de usuário removido -->
                </div>
            </div>

            <!-- Conteúdo da página será inserido aqui -->
            <div class="container-fluid">
