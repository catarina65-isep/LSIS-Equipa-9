<?php
session_start();

// Verifica se o utilizador está logado e é um colaborador
if (!isset($_SESSION['utilizador_id'])) {
    header('Location: /LSIS-Equipa-9/UI/login.php');
    exit;
}

// Verifica se o perfil é de colaborador ou RH (id_perfilacesso = 4 ou 2)
if ($_SESSION['id_perfilacesso'] != 4 && $_SESSION['id_perfilacesso'] != 2) {
    header('Location: /LSIS-Equipa-9/UI/index.php');
    exit;
}

require_once __DIR__ . '/../BLL/ColaboradorBLL.php';

$page_title = "Perfil do Colaborador";

// Cria uma instância do BLL para carregar os dados do colaborador
$colaboradorBLL = new ColaboradorBLL();

// Carrega os dados do colaborador logado
$colaborador = $colaboradorBLL->buscarPorId($_SESSION['utilizador_id']);
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($page_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3f37c9;
            --success-color: #4cc9f0;
            --warning-color: #f8961e;
            --danger-color: #f72585;
            --light-color: #ffffff;
            --dark-color: #212529;
            --sidebar-bg: #2c3e50;
            --card-bg: #ffffff;
            --card-border: 1px solid #dee2e6;
            --card-shadow: 0 2px 4px rgba(0,0,0,0.05);
            --spacing-sm: 0.5rem;
            --spacing-md: 1rem;
            --spacing-lg: 1.5rem;
            --border-radius-sm: 0.25rem;
            --border-radius-md: 0.5rem;
            --border-radius-lg: 0.75rem;
            --transition-normal: 0.3s ease;
            --sidebar-width: 200px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            background: #2c3e50;
            color: #fff;
            width: var(--sidebar-width);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .main-content {
            padding: 1rem 1rem 1rem 0;
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            transition: all 0.3s;
            margin-left: calc(var(--sidebar-width) - 3rem);
        }

        .logo {
            padding: 0.5rem;
            text-align: center;
            background-color: #fff;
            margin-bottom: 0.5rem;
        }

        .logo img {
            max-width: 40px;
            height: auto;
            margin: 0 auto;
            display: block;
            padding: 0;
        }

        .nav-menu {
            padding: 1rem;
        }

        .nav-menu a {
            color: #ecf0f1;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 0.5rem;
            transition: 0.3s;
            font-size: 0.95rem;
            white-space: normal;
            overflow: visible;
        }

        .sidebar-footer {
            padding: 1rem;
            margin-top: auto;
        }

        .sidebar-footer .btn {
            width: 100%;
            padding: 0.75rem;
            border-radius: 0.5rem;
            text-align: center;
            background-color: var(--danger-color);
            border: none;
            color: white;
            transition: 0.3s;
        }

        .sidebar-footer .btn:hover {
            background-color: #ff006e;
        }

        .sidebar-footer .btn i {
            margin-right: 0.75rem;
        }

        .nav-menu a i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            width: 25px;
            text-align: center;
        }

        .nav-menu a:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-menu a.active {
            background: var(--primary-color);
            color: #fff;
        }

        .container-fluid {
            padding: 0;
        }

        .row {
            margin: 0;
        }

        .col-md-3.col-lg-2 {
            padding: 0;
        }

        .col-md-9.col-lg-10 {
            padding: 0 1rem;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: var(--spacing-lg);
            border: var(--card-border);
            background-color: var(--card-bg);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: #fff;
            border-bottom: var(--card-border);
            font-weight: 600;
            padding: var(--spacing-lg);
            border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
        }
        
        .card-body {
            padding: var(--spacing-lg);
            flex: 1;
            min-height: 200px;
            display: flex;
            flex-direction: column;
        }
        
        .chart-container {
            position: relative;
            height: 100%;
            min-height: 200px;
            width: 100%;
        }
        
        #growthChart, #departmentChart {
            width: 100% !important;
            height: auto !important;
            max-height: 300px;
        }
        
        .stat-card {
            position: relative;
            overflow: hidden;
            color: #fff;
            border-radius: var(--border-radius-lg);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            box-shadow: 0 4px 20px rgba(67, 97, 238, 0.3);
        }
        
        .stat-card.bg-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            box-shadow: 0 4px 20px rgba(67, 97, 238, 0.3);
        }
        
        .stat-card.bg-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #4895ef 100%);
            box-shadow: 0 4px 20px rgba(76, 201, 240, 0.3);
        }
        
        .stat-card.bg-warning {
            background: linear-gradient(135deg, var(--warning-color) 0%, #f3722c 100%);
            box-shadow: 0 4px 20px rgba(248, 150, 30, 0.3);
        }
        
        .stat-card.bg-info {
            background: linear-gradient(135deg, #7209b7 0%, #b5179e 100%);
            box-shadow: 0 4px 20px rgba(114, 9, 183, 0.3);
        }
        
        .stat-card.bg-danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #b5179e 100%);
            box-shadow: 0 4px 20px rgba(247, 37, 133, 0.3);
        }
        
        .stat-card.bg-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            box-shadow: 0 4px 20px rgba(108, 117, 125, 0.3);
        }
        
        .stat-icon {
            position: absolute;
            right: var(--spacing-lg);
            top: var(--spacing-lg);
            font-size: 3.5rem;
            opacity: 0.2;
            width: auto;
            height: auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .stat-icon i {
            font-size: 3.5rem;
        }
        
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin: var(--spacing-md) 0 var(--spacing-sm);
        }
        
        .stat-card .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .stat-card .stat-change {
            font-size: 0.8rem;
            margin-top: var(--spacing-sm);
            display: flex;
            align-items: center;
        }
        
        .stat-card .stat-change i {
            position: relative;
            font-size: 1rem;
            opacity: 1;
            margin-right: var(--spacing-sm);
        }
        
        .activity-item {
            display: flex;
            padding: var(--spacing-md) 0;
            border-bottom: var(--card-border);
            align-items: flex-start;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: var(--spacing-md);
            flex-shrink: 0;
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-time {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .top-bar {
            background: #fff;
            padding: var(--spacing-lg);
            border-radius: var(--border-radius-lg);
            margin-bottom: var(--spacing-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .user-menu img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: var(--spacing-sm);
        }
        
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
        
        .form-group label {
            display: block;
            margin-bottom: var(--spacing-md);
            color: var(--dark-color);
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: var(--spacing-sm);
            border: var(--card-border);
            border-radius: var(--border-radius-sm);
            background-color: #ffffff;
            transition: var(--transition-normal);
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 77, 153, 0.1);
            outline: none;
        }

        /* Estilo para o estado de loading dos containers de upload */
        .upload-container.loading {
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #ccc;
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: var(--spacing-sm) var(--spacing-lg);
            border-radius: var(--border-radius-md);
            transition: var(--transition-normal);
            font-weight: 500;
            text-transform: none;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.2);
        }
        
        .form-actions {
            margin-top: var(--spacing-lg);
            display: flex;
            gap: var(--spacing-md);
        }
        
        .sidebar-header {
            padding-bottom: var(--spacing-lg);
            border-bottom: 2px solid #e9ecef;
        }
        
        .logo img {
            max-width: 180px;
            height: auto;
            margin-bottom: var(--spacing-lg);
        }
        
        .sidebar-nav {
            margin-top: var(--spacing-lg);
        }
        
        .status-badge {
            display: inline-block;
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--border-radius-md);
            font-size: var(--font-size-sm);
            font-weight: 500;
        }

        .status-badge.success {
            background-color: var(--success-color);
            color: white;
        }

        .status-badge.warning {
            background-color: var(--warning-color);
            color: white;
        }

        .status-badge.danger {
            background-color: var(--danger-color);
            color: white;
        }

        /* Documentos */
        .document-item {
            display: flex;
            align-items: center;
            padding: var(--spacing-md);
            border-radius: var(--border-radius-md);
            background-color: var(--card-bg);
            margin-bottom: var(--spacing-sm);
            transition: var(--transition-normal);
        }

        .document-item:hover {
            background-color: var(--accent-4);
        }

        .document-item .document-icon {
            width: 40px;
            height: 40px;
            margin-right: var(--spacing-md);
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--accent-4);
            border-radius: var(--border-radius-md);
        }

        .document-item .document-info {
            flex-grow: 1;
        }

        .document-item .document-actions {
            display: flex;
            gap: var(--spacing-sm);
        }

        /* Upload de foto */
        .profile-photo-container {
            position: relative;
            width: 80px;
            height: 80px;
        }

        .profile-photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        #profilePhotoLabel {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 5px;
        }

        /* Estilo dos campos de formulário */
        .form-group {
            margin-bottom: 2rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.75rem;
            cursor: not-allowed;
        }

        /* Estilo específico para textarea */
        .form-control textarea {
            min-height: 100px;
            resize: vertical;
        }

        /* Progress bars */
        .progress {
            height: 10px;
            border-radius: var(--border-radius-md);
            background-color: var(--light-color);
            margin-top: var(--spacing-sm);
        }

        .progress-bar {
            background-color: var(--primary-color);
            border-radius: var(--border-radius-md);
            transition: var(--transition-normal);
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .profile-sidebar {
                width: 100%;
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 1000;
                transform: translateX(-100%);
                transition: var(--transition-normal);
            }

            .profile-sidebar.active {
                transform: translateX(0);
            }

            .profile-content {
                margin-left: 0;
            }
        }

        .profile-details h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #212529;
            font-weight: 700;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: #212529;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .profile-sidebar.active {
            transform: translateX(0);
        }

        .profile-section {
            margin-left: -3rem !important;
            padding-left: 0 !important;
        }

        .profile-section .profile-card {
            padding-left: 0 !important;
            margin-left: -2rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar">
                    <div class="logo">
                        <img src="/LSIS-Equipa-9/UI/assets/img/logos/tlantic-logo.jpg" alt="Tlantic Logo" class="img-fluid">
                    </div>
                    <nav class="nav-menu">
                        <a href="#" class="active" onclick="event.preventDefault(); scrollToSection('top')"><i class='bx bx-user'></i> Perfil</a>
                        <a href="#" class="" onclick="event.preventDefault(); scrollToSection('dadosPessoais')"><i class='bx bx-user'></i> Dados Pessoais</a>
                        <a href="#" class="" onclick="event.preventDefault(); scrollToSection('documentos')"><i class='bx bx-file'></i> Documentos</a>
                    </nav>
                    <div class="sidebar-footer mt-auto">
                        <a href="/LSIS-Equipa-9/UI/logout.php" class="btn btn-danger w-100">
                            <i class='bx bx-log-out'></i> Sair
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    <div class="topbar">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <button class="btn btn-link d-md-none me-3" id="sidebarToggle">
                                    <i class='bx bx-menu'></i>
                                </button>
                                <h1 class="h4 mb-0">O Meu Perfil</h1>
                            </div>
                     <div class="d-flex align-items-center">
                        <button type="button" class="btn btn-primary" id="exportButton" onclick="exportarDados()">
                            <i class='bx bx-download'></i> Exportar Dados
                        </button>
                    </div>
                </div>

                <!-- Dados do Perfil -->
                <div class="profile-header mt-4 profile-section">
                    <div class="profile-card mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="profile-image me-4">
                                <div class="profile-photo">
                                <div class="rounded-circle bg-light text-center" style="width: 120px; height: 120px; display: flex; align-items: center; justify-content: center;">
                                    <i class='bx bx-user-circle' style="font-size: 48px; color: var(--primary-color);"></i>
                                </div>
                            </div>
                            </div>
                            <div class="profile-details">
                                <h1 class="mb-1" id="displayName"><?= htmlspecialchars($colaborador['nome']) ?></h1>
                                <div class="mt-3">
                                     <div class="stat-item mb-2">
                                         <i class='bx bx-envelope'></i>
                                         <span id="displayEmail" class="ms-2"><?= htmlspecialchars($colaborador['email']) ?></span>
                                     </div>
                                     <div class="stat-item">
                                         <i class='bx bx-phone'></i>
                                         <span id="displayPhone" class="ms-2"><?= htmlspecialchars($colaborador['telefone']) ?></span>
                                     </div>
                                </div>
                            </div>
                        </div>
                        <div class="profile-actions d-flex gap-2">
                            <button class="btn btn-primary" id="updatePhoto" onclick="document.getElementById('profilePhoto').click()">
                                <i class='bx bx-image'></i>
                                Atualizar Foto
                            </button>
                        </div>
                        <input type="file" id="profilePhoto" name="profilePhoto" accept="image/*" style="display: none;" onchange="handlePhotoUpload(this)">
                    </div>
                </div>

                <!-- Cards de Estatísticas -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card bg-primary">
                            <div class="stat-icon">
                                <i class='bx bx-user'></i>
                            </div>
                            <h3 class="stat-value">0</h3>
                            <p class="stat-label">Documentos Pendentes</p>
                            <div class="stat-change">
                                <i class='bx bx-up-arrow-alt text-success'></i>
                                <span>0%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card bg-success">
                            <div class="stat-icon">
                                <i class='bx bx-check'></i>
                            </div>
                            <h3 class="stat-value">0</h3>
                            <p class="stat-label">Documentos Aprovados</p>
                            <div class="stat-change">
                                <i class='bx bx-up-arrow-alt text-success'></i>
                                <span>0%</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="stat-card bg-danger">
                            <div class="stat-icon">
                                <i class='bx bx-x'></i>
                            </div>
                            <h3 class="stat-value">0</h3>
                            <p class="stat-label">Documentos Rejeitados</p>
                            <div class="stat-change">
                                <i class='bx bx-down-arrow-alt text-danger'></i>
                                <span>0%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conteúdo Principal -->
        <div class="col-md-9 col-lg-10">
            <!-- Seção de Dados Pessoais -->
        <div class="card" id="perfil">
                    <div class="card-header">
                        <h4>Os Meus Dados</h4>
                    </div>
            <div class="card-body">
                <div class="collapse show" id="dadosPessoais">
                    <form id="profileForm" action="/LSIS-Equipa-9/UI/processa_perfil.php" method="POST">
                        <input type="hidden" id="utilizador_id" name="utilizador_id" value="<?= htmlspecialchars($_SESSION['utilizador_id']); ?>">
                        
                        <!-- Campos do formulário preenchidos com dados do colaborador -->
                        <!-- Seção de Dados Pessoais -->
                        <div class="section-header mt-4 mb-4">
                            <h3 class="section-title">Dados Pessoais</h3>
                            <div class="section-divider"></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nome"><i class='bx bx-user'></i> Nome</label>
                                    <input type="text" class="form-control" id="nome" name="nome" 
                                           value="<?= htmlspecialchars($colaborador['nome'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email"><i class='bx bx-envelope'></i> Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= htmlspecialchars($colaborador['email'] ?? ''); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telefone"><i class='bx bx-phone'></i> Telefone</label>
                                    <input type="tel" class="form-control" id="telefone" name="telefone" 
                                           value="<?= htmlspecialchars($colaborador['telefone'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nif"><i class='bx bx-id-card'></i> NIF</label>
                                    <input type="text" class="form-control" id="nif" name="nif" 
                                           value="<?= htmlspecialchars($colaborador['nif'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="morada"><i class='bx bx-home'></i> Morada</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-home'></i></span>
                                        <textarea class="form-control form-control-lg" id="morada" name="morada" rows="3"><?= htmlspecialchars($colaborador['morada'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="upload-container">
                                        <label for="moradaDoc" class="form-label mt-2">Comprovativo de Morada (PDF, JPG, PNG)</label>
                                        <?php
                                        $uploadDir = __DIR__ . '/../uploads/documentos/';
                                        $moradaFiles = glob($uploadDir . 'morada_' . $_SESSION['utilizador_id'] . '_*');
                                        if (!empty($moradaFiles)) {
                                            $latestFile = array_reduce($moradaFiles, function($a, $b) {
                                                return filemtime($a) > filemtime($b) ? $a : $b;
                                            });
                                            $fileName = basename($latestFile);
                                            echo '<div class="d-flex align-items-center">
                                                <a href="../uploads/documentos/' . htmlspecialchars($fileName) . '" target="_blank" class="flex-grow-1 text-primary">
                                                    <i class="bx bx-file"></i> Ver documento atual
                                                    <small class="text-muted">(Clique para visualizar)</small>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm ms-2 btn-apagar-documento" 
                                                        data-file="' . htmlspecialchars($fileName) . '" 
                                                        data-field="moradaDoc" 
                                                        onclick="apagarDocumento(event)">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>';
                                        } else {
                                            echo '<input type="file" class="form-control" id="moradaDoc" name="moradaDoc" accept=".pdf,.jpg,.jpeg,.png">';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dataNascimento"><i class='bx bx-calendar'></i> Data de Nascimento</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-calendar'></i></span>
                                        <input type="date" id="dataNascimento" name="dataNascimento" required class="form-control form-control-lg" 
                                            value="<?= htmlspecialchars($colaborador['data_nascimento'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="genero"><i class='bx bx-gender'></i> Género</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-gender'></i></span>
                                        <select class="form-control form-control-lg" id="genero" name="genero" required>
                                            <option value="" <?= !$colaborador['genero'] ? 'selected' : ''; ?>>Selecione...</option>
                                            <option value="Masculino" <?= $colaborador['genero'] == 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                                            <option value="Feminino" <?= $colaborador['genero'] == 'Feminino' ? 'selected' : ''; ?>>Feminino</option>
                                            <option value="Outro" <?= $colaborador['genero'] == 'Outro' ? 'selected' : ''; ?>>Outro</option>
                                            <option value="Prefiro não dizer" <?= $colaborador['genero'] == 'Prefiro não dizer' ? 'selected' : ''; ?>>Prefiro não dizer</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="estadoCivil"><i class='bx bx-heart'></i> Estado Civil</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-heart'></i></span>
                                        <select class="form-control form-control-lg" id="estadoCivil" name="estadoCivil" required>
                                            <option value="" <?= !$colaborador['estado_civil'] ? 'selected' : ''; ?>>Selecione...</option>
                                            <option value="Solteiro" <?= $colaborador['estado_civil'] == 'Solteiro' ? 'selected' : ''; ?>>Solteiro</option>
                                            <option value="Casado" <?= $colaborador['estado_civil'] == 'Casado' ? 'selected' : ''; ?>>Casado</option>
                                            <option value="União de Facto" <?= $colaborador['estado_civil'] == 'União de Facto' ? 'selected' : ''; ?>>União de Facto</option>
                                            <option value="Divorciado" <?= $colaborador['estado_civil'] == 'Divorciado' ? 'selected' : ''; ?>>Divorciado</option>
                                            <option value="Viúvo" <?= $colaborador['estado_civil'] == 'Viúvo' ? 'selected' : ''; ?>>Viúvo</option>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="niss"><i class='bx bx-id-card'></i> NISS</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-id-card'></i></span>
                                        <input type="text" id="niss" name="niss" required class="form-control form-control-lg" 
                                            value="<?= htmlspecialchars($colaborador['niss'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cartaoCidadao"><i class='bx bx-id-card'></i> Número do Cartão de Cidadão</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-id-card'></i></span>
                                        <input type="text" id="cartaoCidadao" name="cartaoCidadao" class="form-control form-control-lg" 
                                            value="<?= htmlspecialchars($colaborador['numero_mecanografico'] ?? ''); ?>">
                                    </div>
                                    <div class="upload-container">
                                        <label for="cartaoCidadaoDoc" class="form-label mt-2">Comprovativo (PDF, JPG, PNG)</label>
                                        <?php
                                        $uploadDir = __DIR__ . '/../uploads/documentos/';
                                        $cartaoCidadaoFiles = glob($uploadDir . 'cartaocidadao_' . $_SESSION['utilizador_id'] . '_*');
                                        if (!empty($cartaoCidadaoFiles)) {
                                            $latestFile = array_reduce($cartaoCidadaoFiles, function($a, $b) {
                                                return filemtime($a) > filemtime($b) ? $a : $b;
                                            });
                                            $fileName = basename($latestFile);
                                            echo '<div class="d-flex align-items-center">
                                                <a href="../uploads/documentos/' . htmlspecialchars($fileName) . '" target="_blank" class="flex-grow-1 text-primary">
                                                    <i class="bx bx-file"></i> Ver documento atual
                                                    <small class="text-muted">(Clique para visualizar)</small>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm ms-2 btn-apagar-documento" 
                                                        data-file="' . htmlspecialchars($fileName) . '" 
                                                        data-field="moradaDoc" 
                                                        onclick="apagarDocumento(event)">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>';
                                        } else {
                                            echo '<input type="file" class="form-control" id="cartaoCidadaoDoc" name="cartaoCidadaoDoc" accept=".pdf,.jpg,.jpeg,.png">';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="iban"><i class='bx bx-bank'></i> IBAN</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-bank'></i></span>
                                        <input type="text" id="iban" name="iban" class="form-control form-control-lg" 
                                            value="<?= htmlspecialchars($colaborador['nib'] ?? ''); ?>">
                                    </div>
                                    <div class="upload-container">
                                        <label for="ibanDoc" class="form-label mt-2">Comprovativo Bancário (PDF, JPG, PNG)</label>
                                        <?php
                                        $uploadDir = __DIR__ . '/../uploads/documentos/';
                                        $ibanFiles = glob($uploadDir . 'iban_' . $_SESSION['utilizador_id'] . '_*');
                                        if (!empty($ibanFiles)) {
                                            $latestFile = array_reduce($ibanFiles, function($a, $b) {
                                                return filemtime($a) > filemtime($b) ? $a : $b;
                                            });
                                            $fileName = basename($latestFile);
                                            echo '<div class="d-flex align-items-center">
                                                <a href="../uploads/documentos/' . htmlspecialchars($fileName) . '" target="_blank" class="flex-grow-1 text-primary">
                                                    <i class="bx bx-file"></i> Ver documento atual
                                                    <small class="text-muted">(Clique para visualizar)</small>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm ms-2 btn-apagar-documento" 
                                                        data-file="' . htmlspecialchars($fileName) . '" 
                                                        data-field="moradaDoc" 
                                                        onclick="apagarDocumento(event)">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>';
                                        } else {
                                            echo '<input type="file" class="form-control" id="ibanDoc" name="ibanDoc" accept=".pdf,.jpg,.jpeg,.png">';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numeroDependentes"><i class='bx bx-group'></i> Número de Dependentes</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-group'></i></span>
                                        <input type="number" id="numeroDependentes" name="numeroDependentes" min="0" class="form-control form-control-lg" 
                                            value="<?= htmlspecialchars($colaborador['numero_dependentes'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="habilitacoes"><i class='bx bx-graduation'></i> Habilitações Literárias</label>
                                     <div class="input-group">
                                         <span class="input-group-text"><i class='bx bx-graduation'></i></span>
                                         <input type="text" id="habilitacoes" name="habilitacoes" class="form-control form-control-lg" 
                                            value="<?= htmlspecialchars($colaborador['habilitacoes'] ?? ''); ?>">
                                     </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seção Dados de Emergência -->
                        <div class="section-header mt-4 mb-4">
                            <h3 class="section-title">Dados de Emergência</h3>
                            <div class="section-divider"></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contactoEmergencia"><i class='bx bx-user'></i> Contacto de Emergência</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-user'></i></span>
                                        <input type="text" id="contactoEmergencia" name="contactoEmergencia" class="form-control form-control-lg" 
                                            value="<?= htmlspecialchars($colaborador['contacto_emergencia'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="relacaoEmergencia"><i class='bx bx-link'></i> Relação com o Contacto de Emergência</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-link'></i></span>
                                        <input type="text" id="relacaoEmergencia" name="relacaoEmergencia" class="form-control form-control-lg" 
                                            value="<?= htmlspecialchars($colaborador['relacao_emergencia'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telemovelEmergencia"><i class='bx bx-phone'></i> Telemóvel do Contacto de Emergência</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-phone'></i></span>
                                        <input type="tel" id="telemovelEmergencia" name="telemovelEmergencia" class="form-control form-control-lg" 
                                            value="<?= htmlspecialchars($colaborador['telemovel_emergencia'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Botão de Envio -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div id="messageContainer" class="mb-3"></div>
                                    <div class="d-flex justify-content-end mt-4">
                                        <button type="submit" class="btn btn-primary" id="submitButton">Salvar Alterações</button>
                                        <div id="loadingOverlay" class="loading-overlay"></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('profileForm');
                const submitButton = form.querySelector('button[type="submit"]');
                


                // Adicionar estilo CSS combinado
                const style = document.createElement('style');
                style.textContent = `
                    .loading-spinner {
                        display: inline-block;
                        width: 16px;
                        height: 16px;
                        border: 2px solid #ccc;
                        border-top: 2px solid #000;
                        border-radius: 50%;
                        animation: spin 1s linear infinite;
                    }
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                    .btn-apagar-documento {
                        transition: opacity 0.2s;
                    }
                    .btn-apagar-documento:hover {
                        opacity: 0.8;
                    }
                    .loading-overlay {
                        display: none;
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background-color: rgba(255, 255, 255, 0.8);
                        z-index: 9999;
                    }
                    .loading-overlay.active {
                        display: block;
                    }
                    .upload-container {
                        margin-top: 10px;
                        padding: 15px;
                        border: 2px dashed #dee2e6;
                        border-radius: 8px;
                        background-color: #f8f9fa;
                    }
                    .upload-container:hover {
                        border-color: #0d6efd;
                    }
                    .upload-container .custom-file-label {
                        background-color: #fff;
                        border: none;
                        padding: 8px 12px;
                    }
                    .upload-container .custom-file-label::after {
                        background-color: #0d6efd;
                        color: white;
                        border-color: #0d6efd;
                    }
                `;
                document.head.appendChild(style);

                // Função para atualizar o estado do carregamento
                function updateLoadingState(isLoading) {
                    const overlay = document.getElementById('loadingOverlay');
                    if (isLoading) {
                        overlay.classList.add('active');
                    } else {
                        overlay.classList.remove('active');
                    }
                }

                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Enviar formulário via AJAX
                    fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro na requisição: ' + response.status);
                        }
                        return response.text();
                    })
                    .then(text => {
                        let result;
                        try {
                            result = JSON.parse(text);
                        } catch (e) {
                            throw new Error('Erro ao processar resposta: ' + e.message);
                        }
                        return result;
                    })
                    .then(result => {
                        if (result.success) {
                            // Remover qualquer mensagem anterior
                            const existingAlert = document.getElementById('messageContainer').querySelector('.alert');
                            if (existingAlert) {
                                existingAlert.remove();
                            }
                            
                            // Criar mensagem de sucesso
                            const successMessage = document.createElement('div');
                            successMessage.className = 'alert alert-success alert-dismissible fade show';
                            successMessage.innerHTML = `
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                Dados atualizados com sucesso!
                            `;
                            
                            // Adicionar mensagem ao container
                            const messageContainer = document.getElementById('messageContainer');
                            messageContainer.appendChild(successMessage);
                            
                            // Remover mensagem após 5 segundos
                            setTimeout(() => {
                                successMessage.remove();
                            }, 5000);

                            // Atualizar tabela de documentos
                            atualizarTabelaDocumentos();
                        } else {
                            // Remover qualquer mensagem anterior
                            const existingAlert = document.getElementById('messageContainer').querySelector('.alert');
                            if (existingAlert) {
                                existingAlert.remove();
                            }
                            
                            // Criar mensagem de erro
                            const errorMessage = document.createElement('div');
                            errorMessage.className = 'alert alert-danger alert-dismissible fade show';
                            errorMessage.innerHTML = `
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                ${result.message}
                            `;
                            
                            // Se houver erros específicos, mostrar cada um
                            if (result.erros && result.erros.length > 0) {
                                errorMessage.innerHTML += `
                                    <ul class="mt-2">
                                        ${result.erros.map(erro => `<li>${erro}</li>`).join('')}
                                    </ul>
                                `;
                            }
                            
                            // Adicionar mensagem ao container
                            const messageContainer = document.getElementById('messageContainer');
                            messageContainer.appendChild(errorMessage);
                            
                            // Remover mensagem após 5 segundos
                            setTimeout(() => {
                                errorMessage.remove();
                            }, 5000);
                        }
                    })
                    .catch(error => {
                        console.error('Erro na requisição:', error);
                        
                        // Remover qualquer mensagem anterior
                        const existingAlert = document.getElementById('messageContainer').querySelector('.alert');
                        if (existingAlert) {
                            existingAlert.remove();
                        }
                        
                        // Criar mensagem de erro
                        const errorMessage = document.createElement('div');
                        errorMessage.className = 'alert alert-danger alert-dismissible fade show';
                        errorMessage.innerHTML = `
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            Erro ao salvar dados: ${error.message}
                        `;
                        
                        // Adicionar mensagem ao container
                        const messageContainer = document.getElementById('messageContainer');
                        messageContainer.appendChild(errorMessage);
                        
                        // Remover mensagem após 5 segundos
                        setTimeout(() => {
                            errorMessage.remove();
                        }, 5000);
                        
                        throw error; // Re-lança o erro para o finally
                    })
                    .finally(() => {
                        // Remover o overlay de carregamento
                        updateLoadingState(false);
                        
                        // Garantir que o botão esteja disponível novamente
                        submitButton.disabled = false;
                    });
                });
            });
        </script>

        <!-- Seção de Documentos -->
        <div class="card" id="documentos">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>Documentos</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="documentosTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Nome</th>
                                <th>Data de Upload</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="documentosTableBody">
                            <?php
                            // Diretório de uploads
                            $uploadDir = __DIR__ . '/../uploads/documentos/';
                              
                            // Tipos de documentos e seus prefixos
                            $documentos = [
                                'morada' => 'Comprovativo de Morada',
                                'cartaocidadao' => 'Cartão de Cidadão',
                                'iban' => 'IBAN'
                            ];
                              
                            foreach ($documentos as $prefix => $descricao) {
                                $files = glob($uploadDir . $prefix . '_' . $_SESSION['utilizador_id'] . '_*');
                                if (!empty($files)) {
                                    $latestFile = array_reduce($files, function($a, $b) {
                                        return filemtime($a) > filemtime($b) ? $a : $b;
                                    });
                                    $fileName = basename($latestFile);
                                    $uploadDate = date('Y-m-d H:i:s', filemtime($latestFile));
                                    $status = 'Pendente'; // Pode adicionar lógica para verificar status
                                    
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($descricao) . "</td>";
                                    echo "<td>" . htmlspecialchars($fileName) . "</td>";
                                    echo "<td>" . htmlspecialchars($uploadDate) . "</td>";
                                    echo "<td class='text-warning'>" . htmlspecialchars($status) . "</td>";
                                    echo "<td>";
                                    echo "<a href='" . htmlspecialchars("../uploads/documentos/" . $fileName) . "' target='_blank' class='btn btn-primary btn-sm'>";
                                    echo "<i class='bx bx-download'></i> Download";
                                    echo "</a>";
                                    echo "<button type='button' class='btn btn-danger btn-sm ms-2 btn-apagar-documento' ";
                                    echo "data-file='" . htmlspecialchars($fileName) . "' ";
                                    echo "data-field='" . htmlspecialchars($prefix) . "' ";
                                    echo "onclick='apagarDocumento(event)'>";
                                    echo "<i class='bx bx-trash'></i>";
                                    echo "</button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        <?php include 'footer.php'; ?>

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script>
            // Função para navegação suave entre seções
            function scrollToSection(sectionId) {
                // Verifica se o link já está ativo
                const currentLink = event.currentTarget;
                if (currentLink.classList.contains('active')) {
                    return; // Se já está ativo, não faz nada
                }

                // Remove a classe active de todos os links
                const navLinks = document.querySelectorAll('.nav-menu a');
                navLinks.forEach(link => {
                    link.classList.remove('active');
                });
                
                // Adiciona a classe active ao link clicado
                currentLink.classList.add('active');

                // Realiza a navegação apenas se não estiver no link ativo
                if (sectionId === 'top') {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    const element = document.getElementById(sectionId);
                    if (element) {
                        element.scrollIntoView({ behavior: 'smooth' });
                    }
                }
            }

            // Inicialização de componentes
            document.addEventListener('DOMContentLoaded', function() {
                // Destacar o item ativo na sidebar
                const currentPath = window.location.pathname;
                const navLinks = document.querySelectorAll('.nav-menu a');
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (currentPath.includes('colaborador.php')) {
                        if (link.textContent.trim() === 'Perfil') {
                            link.classList.add('active');
                        }
                    }
                });
                // Toggle sidebar em dispositivos móveis
                document.getElementById('sidebarToggle').addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('show');
                    document.querySelector('.main-content').classList.toggle('sidebar-hidden');
                });

                // Inicializar tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // Inicializar popovers
                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
                var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl);
                });

                // Inicializar Select2
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    width: '100%'
                });
            });
        </script>
        <script>
            // Função para atualizar o formulário
            function atualizarFormulario() {
                const form = document.getElementById('profileForm');
                if (!form) {
                    console.error('Formulário não encontrado');
                    return;
                }

                // Tipos de documentos e seus IDs
                const documentos = {
                    'morada': 'moradaDoc',
                    'cartaocidadao': 'cartaocidadaoDoc',
                    'iban': 'ibanDoc'
                };

                // Para cada tipo de documento
                Object.entries(documentos).forEach(([prefix, field]) => {
                    // Encontrar o container de upload específico para este campo
                    const container = form.querySelector(`.form-group:has(#${field}) .upload-container`);
                    if (!container) {
                        console.error(`Container de upload para ${field} não encontrado`);
                        return;
                    }

                    // Remover qualquer link de visualização existente
                    const linkContainer = container.querySelector('.d-flex.align-items-center');
                    if (linkContainer) {
                        linkContainer.remove();
                    }

                    // Limpar o container
                    container.innerHTML = '';

                    // Adicionar o input de upload
                    const input = document.createElement('input');
                    input.type = 'file';
                    input.className = 'form-control';
                    input.id = field;
                    input.name = field;
                    input.accept = '.pdf,.jpg,.jpeg,.png';
                    container.appendChild(input);
                });
            }

            // Função para atualizar a seção de documentos
            function atualizarSecaoDocumentos() {
                const documentosSection = document.getElementById('documentos');
                if (!documentosSection) {
                    console.error('Seção de documentos não encontrada');
                    return;
                }

                // Obter o ID do utilizador do campo hidden do formulário
                const utilizadorId = document.getElementById('utilizador_id').value;
                if (!utilizadorId) {
                    console.error('ID do utilizador não encontrado');
                    return;
                }

                fetch('/LSIS-Equipa-9/UI/documentos.php?id_utilizador=' + encodeURIComponent(utilizadorId))
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro ao carregar documentos: ' + response.status);
                        }
                        return response.text();
                    })
                    .then(html => {
                        // Atualizar apenas o conteúdo interno do card
                        const cardBody = documentosSection.querySelector('.card-body');
                        if (cardBody) {
                            cardBody.innerHTML = html;
                        } else {
                            console.error('Card body não encontrado');
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao atualizar documentos:', error);
                        showAlert('danger', 'Erro ao atualizar documentos');
                    });
            }

            // Função para atualizar a tabela de documentos (apenas quando necessário)
            function atualizarTabelaDocumentos() {
                // Tipos de documentos e seus prefixos
                const documentos = {
                    'morada': 'Comprovativo de Morada',
                    'cartaocidadao': 'Cartão de Cidadão',
                    'nif': 'NIF',
                    'niss': 'NISS',
                    'iban': 'IBAN'
                };

                // Para cada tipo de documento
                Object.entries(documentos).forEach(([prefix, descricao]) => {
                    // Buscar arquivos mais recentes
                    fetch(`/LSIS-Equipa-9/UI/busca_documentos.php?prefix=${prefix}&utilizador_id=${$_SESSION['utilizador_id']}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.file) {
                                // Verificar se já existe um elemento com este arquivo
                                const existingRow = document.querySelector(`tr[data-file='${data.file}']`);
                                if (!existingRow) {
                                    // Criar nova linha apenas se não existir
                                    const tbody = document.getElementById('documentosTableBody');
                                    const tr = document.createElement('tr');
                                    tr.dataset.file = data.file; // Adicionar atributo data-file
                                    tr.innerHTML = `
                                        <td>${descricao}</td>
                                        <td>${data.file}</td>
                                        <td>${data.uploadDate}</td>
                                        <td class='text-warning'>Pendente</td>
                                        <td>
                                            <a href='../uploads/documentos/${data.file}' target='_blank' class='btn btn-primary btn-sm'>
                                                <i class='bx bx-download'></i> Download
                                            </a>
                                            <button type='button' class='btn btn-danger btn-sm ms-2 btn-apagar-documento' 
                                                    data-file='${data.file}' 
                                                    data-field='${prefix}' 
                                                    onclick='apagarDocumento(event)'>
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </td>
                                    `;
                                    tbody.appendChild(tr);
                                }
                            }
                        })
                        .catch(error => console.error('Erro ao buscar documentos:', error));
                });
            }

            // Inicializar tabela de documentos quando a página carrega
            document.addEventListener('DOMContentLoaded', function() {
                // Não faz nada na inicialização, a tabela já vem do PHP
            });

        </script>
        <script>
            // Função para inicializar máscaras
            function initializeMasks() {
                $('#telefone, #telemovel_emergencia').mask('000000000');
                $('#nif').mask('000000000');
                $('#niss').mask('00000000000');
            }

            // Função para inicializar Select2
            function initializeSelect2() {
                $('.select2').select2();
            }



            // Função para inicializar daterangepicker
            function initializeDateRangePicker() {
                $('.daterangepicker').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                });
            }

            // Função para inicializar o layout
            function initializeLayout() {
                // Toggle sidebar em dispositivos móveis
                const sidebarToggle = document.getElementById('sidebarToggle');
                if (sidebarToggle) {
                    sidebarToggle.addEventListener('click', function() {
                        document.querySelector('.sidebar').classList.toggle('show');
                    });
                }

                // Inicializar tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // Inicializar popovers
                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
                var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl);
                });
            }

            // Função para mostrar alertas
            function showAlert(type, message) {
                // Verifica se já existe uma mensagem de sucesso
                const existingAlert = document.querySelector('.alert');
                if (existingAlert) {
                    existingAlert.remove();
                }
                
                const messageContainer = document.getElementById('messageContainer');
                const alert = document.createElement('div');
                alert.className = `alert alert-${type} alert-dismissible fade show`;
                alert.innerHTML = `
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    ${message}
                `;
                messageContainer.appendChild(alert);
                
                // Remove a mensagem após 5 segundos
                setTimeout(() => {
                    alert.remove();
                }, 5000);
            }

            // Função para exportar dados
            async function exportarDados() {
                try {
                    // Redirecionar para o arquivo de exportação
                    window.location.href = '/LSIS-Equipa-9/UI/processa_exportar.php';
                } catch (error) {
                    console.error('Erro ao exportar:', error);
                    showAlert('danger', 'Erro ao exportar dados');
                }
            }

            // Função para inicializar o tema
            function initializeTheme() {
                const themeToggle = document.getElementById('themeToggle');
                if (themeToggle) {
                    const savedTheme = localStorage.getItem('theme') || 'light';
                    document.documentElement.setAttribute('data-bs-theme', savedTheme);
                    
                    // Atualiza o ícone inicial
                    const icon = themeToggle.querySelector('i');
                    icon.className = savedTheme === 'dark' ? 'bx bx-sun' : 'bx bx-moon';

                    themeToggle.addEventListener('click', function() {
                        const html = document.documentElement;
                        const currentTheme = html.getAttribute('data-bs-theme');
                        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                        
                        html.setAttribute('data-bs-theme', newTheme);
                        localStorage.setItem('theme', newTheme);
                        
                        // Atualiza o ícone
                        const icon = this.querySelector('i');
                        icon.className = newTheme === 'dark' ? 'bx bx-sun' : 'bx bx-moon';
                    });
                }
            }

            // Função para atualizar o perfil
            function updateProfile() {
                const form = document.getElementById('profileForm');
                const submitButton = document.getElementById('submitButton');
                // Função global para apagar documento
                window.apagarDocumento = function(event) {
                    const button = event.currentTarget;
                    const fileName = button.dataset.file;
                    const field = button.dataset.field;

                    if (!confirm('Tem a certeza que deseja apagar este documento?')) {
                        return;
                    }

                    // Desabilitar botão e mostrar loading
                    button.disabled = true;
                    button.innerHTML = '<span class="loading-spinner"></span>';

                    // Adicionar loading ao container de upload
                    const uploadContainer = button.closest('.upload-container');
                    if (uploadContainer) {
                        uploadContainer.classList.add('loading');
                    }

                    // Enviar requisição para apagar o documento
                    fetch('apaga_documento.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            fileName: fileName,
                            field: field
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro no servidor: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Remover loading
                            if (uploadContainer) {
                                uploadContainer.classList.remove('loading');
                            }
                            
                            // Atualizar apenas o container específico deste documento
                            if (uploadContainer) {
                                // Remover qualquer link de visualização existente
                                const linkContainer = uploadContainer.querySelector('.d-flex.align-items-center');
                                if (linkContainer) {
                                    linkContainer.remove();
                                }
                                
                                // Limpar o container
                                uploadContainer.innerHTML = '';
                                
                                // Adicionar o input de upload
                                const input = document.createElement('input');
                                input.type = 'file';
                                input.className = 'form-control';
                                input.id = field;
                                input.name = field;
                                input.accept = '.pdf,.jpg,.jpeg,.png';
                                uploadContainer.appendChild(input);
                            }
                            
                            // Atualizar a seção de documentos
                            atualizarSecaoDocumentos();
                            
                            // Mostrar mensagem de sucesso
                            showAlert('success', data.message || 'Documento apagado com sucesso');
                            
                            // Restaurar o botão
                            button.innerHTML = '<i class="bx bx-trash"></i>';
                            button.disabled = false;
                        } else {
                            throw new Error(data.message || 'Erro ao apagar documento');
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Erro ao apagar documento: ' + error.message);
                        button.innerHTML = '<i class="bx bx-trash"></i>';
                        button.disabled = false;
                        if (uploadContainer) {
                            uploadContainer.classList.remove('loading');
                        }
                    });
                }

                if (!submitButton) {
                    console.error('Botão de submit não encontrado');
                    return;
                }
                
                submitButton.addEventListener('click', async function(e) {
                    e.preventDefault();
                    
                    // Adicionar spinner ao botão
                    const originalText = submitButton.innerHTML;
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';
                    submitButton.disabled = true;
                    submitButton.style.opacity = '0.6'; // Adicionar opacidade durante o processamento
                    
                    try {
                        const formData = new FormData(form);
                        const response = await fetch('/LSIS-Equipa-9/UI/processa_perfil.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            // Remove qualquer mensagem anterior
                            const existingAlert = document.querySelector('.alert');
                            if (existingAlert) {
                                existingAlert.remove();
                            }
                            showAlert('success', 'Dados atualizados com sucesso');
                            
                            // Atualizar a seção de documentos
                            atualizarSecaoDocumentos();
                            
                            // Função para atualizar um campo de upload
                            function atualizarCampoUpload(field, fileName) {
                                console.log('Atualizando campo:', field, 'com arquivo:', fileName);
                                
                                // Mapear campos especiais
                                const fieldMap = {
                                    'cartaocidadao': 'cartaoCidadao'
                                };
                                const mappedField = fieldMap[field] || field;
                                
                                // Encontrar o container do upload usando o ID do campo
                                const container = document.querySelector(`.form-group .upload-container:has(label[for="${mappedField}Doc"])`);
                                if (!container) {
                                    console.error('Container não encontrado para:', field);
                                    return;
                                }

                                console.log('Container encontrado:', container);

                                // Remover qualquer link existente
                                const existingLink = container.querySelector('a[href]');
                                if (existingLink) {
                                    existingLink.remove();
                                }

                                // Criar o novo link de visualização
                                const linkContainer = document.createElement('div');
                                linkContainer.className = 'd-flex align-items-center';
                                linkContainer.innerHTML = `
                                    <a href="../uploads/documentos/${fileName}" target="_blank" class="flex-grow-1 text-primary">
                                        <i class="bx bx-file"></i> Ver documento atual
                                        <small class="text-muted">(Clique para visualizar)</small>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm ms-2 btn-apagar-documento" 
                                            data-file="${fileName}" 
                                            data-field="${field}"
                                            onclick="apagarDocumento(event)">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                `;

                                // Adicionar o novo link
                                container.innerHTML = '';
                                container.appendChild(linkContainer);
                                console.log('Campo atualizado com sucesso');
                            }

                            // Atualizar todos os campos de upload
                            const uploadFields = {
                                'morada': 'moradaDoc',
                                'cartaocidadao': 'cartaocidadaoDoc',
                                'iban': 'ibanDoc'
                            };

                            console.log('Resultado do servidor:', result);

                            // Atualizar cada campo com seu respectivo arquivo
                            Object.keys(uploadFields).forEach(field => {
                                const fileName = result.documentos[field];
                                if (fileName) {
                                    console.log('Atualizando campo:', field, 'com arquivo:', fileName);
                                    atualizarCampoUpload(field, fileName);
                                } else {
                                    console.log('Nenhum arquivo encontrado para:', field);
                                }
                            });
                        } else {
                            // Se existem erros específicos, mostra-os
                            if (result.erros && result.erros.length > 0) {
                                const errorMessages = result.erros.map(error => `<li>${error}</li>`).join('');
                                showAlert('danger', `
                                    ${result.message}
                                    <ul class="mt-2">
                                        ${errorMessages}
                                    </ul>
                                `);
                            } else {
                                showAlert('danger', result.message);
                            }
                        }
                    } catch (error) {
                        console.error('Erro na requisição:', error);
                        showAlert('danger', 'Erro ao salvar dados');
                    } finally {
                        // Restaurar o botão
                        submitButton.innerHTML = originalText;
                        submitButton.disabled = false;
                        submitButton.style.opacity = '1'; // Resetar opacidade
                        submitButton.offsetHeight; // Forçar reflow
                        
                        // Adicionar um setTimeout para garantir que o botão seja atualizado
                        setTimeout(() => {
                            submitButton.innerHTML = originalText;
                            submitButton.style.opacity = '1';
                        }, 100);
                    }
                });
            }

            // Inicializar tudo quando o documento estiver pronto
            $(document).ready(function() {
                initializeMasks();
                initializeSelect2();
                initializeDateRangePicker();
                initializeLayout();
                initializeTheme();
                updateProfile();
            });
        </script>

    </body>
</html>
