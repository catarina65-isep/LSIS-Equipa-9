<?php
require_once __DIR__ . '/includes/session.php';
$page_title = $page_title ?? 'Tlantic - Gestão de Colaboradores';

// Verifica se o usuário está logado para páginas que requerem autenticação
$public_pages = ['login', 'recuperar_senha', '404'];
$current_page = basename($_SERVER['PHP_SELF'], '.php');

if (!in_array($current_page, $public_pages) && !isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Define o nível de acesso do usuário
$nivel_acesso = $_SESSION['nivel_acesso'] ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-PT" data-bs-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title><?= htmlspecialchars($page_title) ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/LSIS-Equipa-9/UI/assets/img/logos/tlantic-logo.jpg">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet">
    <link rel="stylesheet" href="/LSIS-Equipa-9/UI/assets/css/style.css">
    
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3a56d4;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --info-color: #4895ef;
            --warning-color: #f8961e;
            --danger-color: #f72585;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --header-height: 60px;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f5f7fb;
            color: #2c3e50;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        #main-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        /* Alertas */
        .alert {
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.9375rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            position: relative;
            overflow: hidden;
        }

        .alert:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
        }

        .alert-danger {
            color: #7f1d1d;
            background-color: #fee2e2;
        }

        .alert-danger:before {
            background-color: #dc2626;
        }

        .alert-success {
            color: #14532d;
            background-color: #dcfce7;
        }

        .alert-success:before {
            background-color: #16a34a;
        }

        .alert-warning {
            color: #78350f;
            background-color: #fef3c7;
        }

        .alert-warning:before {
            background-color: #d97706;
        }

        .alert-info {
            color: #1e40af;
            background-color: #dbeafe;
        }

        .alert-info:before {
            background-color: #2563eb;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f5f7fb;
            color: #2c3e50;
            line-height: 1.6;
        }
        
        /* Alertas */
        .alert {
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.9375rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            position: relative;
            overflow: hidden;
        }
        
        .alert:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
        }
        
        .alert-danger {
            color: #7f1d1d;
            background-color: #fee2e2;
        }
        
        .alert-danger:before {
            background-color: #dc2626;
        }
        
        .alert-success {
            color: #14532d;
            background-color: #dcfce7;
        }
        
        .alert-success:before {
            background-color: #16a34a;
        }
        
        .alert-warning {
            color: #78350f;
            background-color: #fef3c7;
        }
        
        .alert-warning:before {
            background-color: #d97706;
        }
        
        .alert-info {
            color: #1e40af;
            background-color: #dbeafe;
        }
        
        .alert-info:before {
            background-color: #2563eb;
        }
        
        /* Layout */
        #main-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        #main-content {
            flex: 1;
            overflow-x: hidden;
            padding: 2rem;
            transition: all 0.3s ease;
        }
        
        @media (max-width: 991.98px) {
            #main-content {
                padding: 1rem;
            }
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin-bottom: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
            border-top-left-radius: 0.75rem !important;
            border-top-right-radius: 0.75rem !important;
        }
        
        /* Buttons */
        .btn {
            border-radius: 0.5rem;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.8125rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        /* Tables */
        .table {
            --bs-table-bg: transparent;
            --bs-table-striped-bg: rgba(0, 0, 0, 0.02);
            --bs-table-hover-bg: rgba(0, 0, 0, 0.025);
        }
        
        .table > :not(caption) > * > * {
            padding: 1rem 1.25rem;
        }
        
        /* Forms */
        .form-control, .form-select, .form-control:focus, .form-select:focus {
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        }
        
        /* Badges */
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
            border-radius: 0.375rem;
        }
        
        /* Avatar */
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #e9ecef;
            color: #6c757d;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .avatar-sm {
            width: 32px;
            height: 32px;
            font-size: 0.75rem;
        }
        
        .avatar-lg {
            width: 72px;
            height: 72px;
            font-size: 1.5rem;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>
<body>
    <div id="main-wrapper">
        <?php if ($nivel_acesso >= 2): // Apenas para administrador e RH ?>
            <!-- Sidebar -->
            <?php include_once 'admin/includes/sidebar.php'; ?>
        <?php endif; ?>
        
        <!-- Main Content -->
        <div id="main-content">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class='bx bx-check-circle me-2'></i>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class='bx bx-error-circle me-2'></i>
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['warning'])): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class='bx bx-error me-2'></i>
                    <?= htmlspecialchars($_SESSION['warning']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['warning']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['info'])): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class='bx bx-info-circle me-2'></i>
                    <?= htmlspecialchars($_SESSION['info']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['info']); ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.js"></script>
    <script src="/LSIS-Equipa-9/UI/assets/js/main.js"></script>

    <!-- Fecha a sessão -->
    <?php
    session_write_close();
    ?>
</body>
</html>
?>