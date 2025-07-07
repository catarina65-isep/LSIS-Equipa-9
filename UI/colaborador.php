<?php
session_start();

// Verifica se o utilizador está logado e é um colaborador
if (!isset($_SESSION['utilizador_id'])) {
    header('Location: /LSIS-Equipa-9/UI/login.php');
    exit;
}

// Verifica se o perfil é de colaborador (id_perfilacesso = 4)
if ($_SESSION['id_perfilacesso'] != 4) {
    header('Location: /LSIS-Equipa-9/UI/index.php');
    exit;
}

$page_title = "Perfil do Colaborador";
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
                                <h1 class="h4 mb-0">Perfil do Colaborador</h1>
                            </div>
                    <div class="d-flex align-items-center">
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="periodoDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class='bx bx-calendar me-1'></i> Últimos 30 dias
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="periodoDropdown">
                                <li><a class="dropdown-item" href="#">Hoje</a></li>
                                <li><a class="dropdown-item active" href="#">Últimos 7 dias</a></li>
                                <li><a class="dropdown-item" href="#">Últimos 30 dias</a></li>
                                <li><a class="dropdown-item" href="#">Este mês</a></li>
                                <li><a class="dropdown-item" href="#">Personalizado</a></li>
                            </ul>
                        </div>
                        <button class="btn btn-primary">
                            <i class='bx bx-download me-1'></i> Exportar Dados
                        </button>
                    </div>
                </div>

                <!-- Dados do Perfil -->
                <div class="profile-header mt-4 profile-section">
                    <div class="profile-card mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="profile-image me-4">
                                <div class="profile-photo-container">
                                    <img src="../assets/placeholder.jpg" alt="Foto de Perfil" id="foto-preview" class="rounded-circle">
                                </div>
                            </div>
                            <div class="profile-details">
                                <h1 class="mb-1" id="displayName">Nome Completo</h1>
                                <div class="mt-3">
                                    <div class="stat-item mb-2">
                                        <i class='bx bx-envelope'></i>
                                        <span id="displayEmail" class="ms-2"></span>
                                    </div>
                                    <div class="stat-item">
                                        <i class='bx bx-phone'></i>
                                        <span id="displayPhone" class="ms-2"></span>
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
                        <div class="stat-card bg-warning">
                            <div class="stat-icon">
                                <i class='bx bx-time'></i>
                            </div>
                            <h3 class="stat-value">0</h3>
                            <p class="stat-label">Documentos Expirados</p>
                            <div class="stat-change">
                                <i class='bx bx-down-arrow-alt text-danger'></i>
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

        <!-- Seção de Dados Pessoais -->
        <div class="card" id="perfil">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>Dados Pessoais</h4>
                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#dadosPessoais">
                        <i class='bx bx-edit'></i> Editar
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="collapse show" id="dadosPessoais">
                    <form id="profileForm">
                        <input type="hidden" id="usuario_id" name="usuario_id" value="<?php echo htmlspecialchars($_SESSION['utilizador_id']); ?>">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nome"><i class='bx bx-user'></i> Nome Completo</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-user'></i></span>
                                        <input type="text" id="nome" name="nome" required class="form-control form-control-lg">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dataNascimento"><i class='bx bx-calendar'></i> Data de Nascimento</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-calendar'></i></span>
                                        <input type="date" id="dataNascimento" name="dataNascimento" required class="form-control form-control-lg">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nif"><i class='bx bx-id-card'></i> NIF (Número de Identificação Fiscal)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-id-card'></i></span>
                                        <input type="text" id="nif" name="nif" required class="form-control form-control-lg">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="niss"><i class='bx bx-id-card'></i> NISS</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-id-card'></i></span>
                                        <input type="text" id="niss" name="niss" required class="form-control form-control-lg">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seção Contactos -->
                        <div class="section-header mt-4 mb-4">
                            <h3 class="section-title">Contactos</h3>
                            <div class="section-divider"></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telefone"><i class='bx bx-phone'></i> Contacto Telefónico</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-phone'></i></span>
                                        <input type="tel" id="telefone" name="telefone" required class="form-control form-control-lg">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email"><i class='bx bx-envelope'></i> Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                                        <input type="email" id="email" name="email" required class="form-control form-control-lg">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seção Dados Pessoais -->
                        <div class="section-header mt-4 mb-4">
                            <h3 class="section-title">Dados Pessoais</h3>
                            <div class="section-divider"></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="estadoCivil"><i class='bx bx-heart'></i> Estado Civil</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-heart'></i></span>
                                        <select id="estadoCivil" name="estadoCivil" required class="form-control form-control-lg">
                                            <option value="">Selecione...</option>
                                            <option value="solteiro">Solteiro(a)</option>
                                            <option value="casado">Casado(a)</option>
                                            <option value="divorciado">Divorciado(a)</option>
                                            <option value="viuvo">Viúvo(a)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numeroDependentes"><i class='bx bx-group'></i> Número de Dependentes</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-group'></i></span>
                                        <input type="number" id="numeroDependentes" name="numeroDependentes" min="0" class="form-control form-control-lg">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="habilitacoes"><i class='bx bx-graduation'></i> Habilitações Literárias</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-graduation'></i></span>
                                        <select id="habilitacoes" name="habilitacoes" class="form-control form-control-lg">
                                            <option value="">Selecione...</option>
                                            <option value="ensino_basico">Ensino Básico</option>
                                            <option value="ensino_secundario">Ensino Secundário</option>
                                            <option value="ensino_superior">Ensino Superior</option>
                                            <option value="outro">Outro</option>
                                        </select>
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
                                        <input type="text" id="contactoEmergencia" name="contactoEmergencia" class="form-control form-control-lg">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="relacaoEmergencia"><i class='bx bx-link'></i> Relação com o Contacto de Emergência</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-link'></i></span>
                                        <input type="text" id="relacaoEmergencia" name="relacaoEmergencia" class="form-control form-control-lg">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telemovelEmergencia"><i class='bx bx-phone'></i> Telemóvel do Contacto de Emergência</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-phone'></i></span>
                                        <input type="tel" id="telemovelEmergencia" name="telemovelEmergencia" class="form-control form-control-lg">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seção Observações -->
                        <div class="section-header mt-4 mb-4">
                            <h3 class="section-title">Observações</h3>
                            <div class="section-divider"></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="observacoes"><i class='bx bx-comment-detail'></i> Observações</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-comment-detail'></i></span>
                                        <textarea id="observacoes" name="observacoes" class="form-control form-control-lg" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botão de Envio -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="form-actions text-center">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">Salvar Alterações</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Seção de Documentos -->
        <div class="card" id="documentos">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4>Documentos</h4>
                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class='bx bx-upload'></i> Upload de Documento
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
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
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal para Upload de Documentos -->
        <div class="modal fade" id="uploadModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload de Documento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="uploadForm">
                            <div class="mb-3">
                                <label for="tipoDocumento" class="form-label">Tipo de Documento</label>
                                <select class="form-select" id="tipoDocumento" name="tipoDocumento" required>
                                    <option value="">Selecione...</option>
                                    <option value="cartao_cidadao">Cartão de Cidadão</option>
                                    <option value="nif">NIF</option>
                                    <option value="niss">NISS</option>
                                    <option value="outro">Outro</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="arquivo" class="form-label">Arquivo</label>
                                <input type="file" class="form-control" id="arquivo" name="arquivo" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="mb-3">
                                <label for="dataValidade" class="form-label">Data de Validade</label>
                                <input type="date" class="form-control" id="dataValidade" name="dataValidade">
                            </div>
                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="uploadDocumento()">Upload</button>
                    </div>
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
        <script src="js/colaborador.js"></script>

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
    </body>
</html>
