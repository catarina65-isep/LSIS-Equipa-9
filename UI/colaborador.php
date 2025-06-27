<?php
session_start();

// Verifica se o usuário está logado e é um colaborador
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Verifica se o perfil é de colaborador (id_perfilacesso = 4)
if ($_SESSION['id_perfilacesso'] != 4) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Tlantic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            /* Cores oficiais da Tlantic */
            --primary-color: #0066ff;
            --primary-hover: #0052cc;
            --secondary-color: #004d99;
            --success-color: #008000;
            --info-color: #0066ff;
            --warning-color: #ff9900;
            --danger-color: #ff0000;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --header-height: 60px;
            --transition: all 0.3s ease;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            --card-border: 1px solid rgba(0, 0, 0, 0.05);
            
            /* Paleta de cores complementares */
            --accent-1: #004d99;
            --accent-2: #003366;
            --accent-3: #001a33;
            --accent-4: #e6f0ff;
            --accent-5: #cce0ff;
            
            /* Cores para a sidebar */
            --sidebar-bg: #f8f9fa;
            --sidebar-header-bg: linear-gradient(135deg, #e6f0ff 0%, #cce0ff 100%);
            --sidebar-nav-bg: rgba(0, 102, 255, 0.05);
            --sidebar-footer-bg: #ffffff;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            flex: 1;
            display: flex;
            padding: 2rem;
        }

        .profile-container {
            display: flex;
            width: 100%;
            height: 100%;
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            border: var(--card-border);
            position: relative;
            padding-left: 100px;
        }

        .profile-sidebar {
            width: 80px;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
            gap: 1rem;
            position: fixed;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
            border-right: var(--card-border);
        }

        .sidebar-nav {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            color: var(--dark-color);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            width: 100%;
            text-align: left;
            font-size: 0.85rem;
        }

        .nav-link i {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }

        .nav-link span {
            display: inline;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nav-link:hover {
            background: rgba(0, 102, 255, 0.1);
            color: var(--primary-color);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }

        .nav-link i {
            font-size: 1.2rem;
        }

        .sidebar-footer {
            background: var(--sidebar-footer-bg);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
        }

        .sidebar-footer .photo-preview {
            margin-bottom: 1rem;
        }

        .profile-content {
            flex: 1;
            margin-left: 80px;
            padding: 2rem;
            overflow-y: auto;
        }

        .profile-header {
            padding: 1rem;
            background: white;
            border-radius: 1rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.5rem;
        }

        .profile-photo {
            position: relative;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        .profile-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        #uploadPhotoBtn {
            background: var(--primary-color);
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            box-shadow: var(--card-shadow);
            margin-top: 0.5rem;
        }

        #uploadPhotoBtn i {
            font-size: 0.9rem;
            margin-right: 0.25rem;
        }

        .profile-info {
            flex: 1;
            min-width: 0;
            margin-right: 1.5rem;
        }

        .profile-info {
            flex: 1;
            min-width: 0;
        }

        .profile-info h1 {
            font-size: 1.25rem;
            margin: 0 0 0.5rem;
            color: var(--dark-color);
            font-weight: 600;
        }

        .profile-info p {
            margin: 0 0 0.5rem;
            color: var(--text-color);
            font-size: 0.95rem;
        }

        .profile-info .status {
            margin: 0 0 1rem;
        }

        .profile-info .status-badge {
            font-size: 0.9rem;
            padding: 0.3rem 0.75rem;
            background: var(--primary-color);
            color: white;
            border-radius: 20px;
        }

        #uploadPhotoBtn:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
        }

        #uploadPhotoBtn:active {
            transform: translateY(0);
        }

        #uploadPhotoBtn i {
            font-size: 1rem;
            color: white;
            margin-right: 0.35rem;
        }

        .upload-photo-btn i {
            font-size: 1.2rem;
            color: white;
            margin-right: 0.5rem;
        }

        .upload-photo-btn i {
            font-size: 1.2rem;
            color: white;
            margin-right: 0.5rem;
        }

        .upload-photo-btn:hover {
            background: rgba(0, 102, 255, 1);
        }

        .upload-photo-btn i {
            font-size: 1.2rem;
            color: white;
            margin-right: 0.5rem;
        }

        .upload-photo-btn:hover {
            background: rgba(0, 102, 255, 1);
        }

        .upload-photo-btn i {
            font-size: 1.2rem;
            color: white;
        }

.profile-info .bg-primary {
    min-width: 120px;
    text-align: center;
}

.profile-info .bg-primary h3 {
    font-size: 1.25rem;
    margin: 0;
}

.profile-info .bg-primary p {
    font-size: 0.85rem;
    margin: 0.25rem 0 0;
}

.profile-info .actions {
    display: flex;
    gap: 1rem;
}
        }

        .profile-info .status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .status-badge {
            background: var(--accent-1);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        .profile-info .actions {
            display: flex;
            gap: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-hover) 0%, var(--secondary-color) 100%);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #cc0000 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
        }

        @media (max-width: 1200px) {
            .profile-sidebar {
                width: 200px;
            }
            
            .profile-content {
                margin-left: 200px;
            }
            
            .sidebar-footer {
                left: 2rem;
                width: 185px;
            }
        }

        @media (max-width: 992px) {
            .profile-sidebar {
                position: static;
                width: 100%;
                height: auto;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                padding: 1rem;
            }
            
            .sidebar-nav {
                display: none;
            }
            
            .profile-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            flex: 1;
            display: flex;
            padding: 2rem;
        }

        .profile-container {
            display: flex;
            width: 100%;
            height: 100%;
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            border: var(--card-border);
        }

        .profile-sidebar {
            width: 250px;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
            gap: 2rem;
        }

        .sidebar-header {
            text-align: center;
            padding: 1.5rem;
            background: var(--sidebar-header-bg);
            border-radius: 12px;
            position: relative;
        }

        .sidebar-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 102, 255, 0.1) 0%, rgba(0, 77, 153, 0.1) 100%);
            z-index: 0;
        }

        .sidebar-header h2 {
            color: var(--dark-color);
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }

        .sidebar-header p {
            color: #666;
            font-size: 0.95rem;
            position: relative;
            z-index: 1;
        }

        .sidebar-nav {
            flex: 1;
            background: var(--sidebar-nav-bg);
            border-radius: 12px;
            padding: 1.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--dark-color);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(0, 102, 255, 0.1);
            color: var(--primary-color);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }

        .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.2rem;
        }

        .sidebar-footer {
            background: var(--sidebar-footer-bg);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
        }

        .sidebar-footer .photo-preview {
            margin-bottom: 1rem;
        }

        .profile-content {
            flex: 1;
            padding: 1rem;
            overflow-y: auto;
        }

        .profile-content {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            padding: 1rem;
        }

        .profile-section {
            flex: 1;
            min-width: 300px;
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            border: var(--card-border);
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }

        .profile-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .profile-section:first-child {
            flex: 2;
        }

        .profile-section:nth-child(2) {
            flex: 2;
        }

        @media (max-width: 992px) {
            .profile-section {
                flex: 1 1 100%;
                min-width: 100%;
            }
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-header h2 {
            color: var(--dark-color);
            font-size: 1.5rem;
            font-weight: 600;
        }

        @media (max-width: 992px) {
            .profile-sidebar {
                width: 200px;
            }

            .profile-content {
                padding: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
            }

            .profile-sidebar {
                width: 100%;
                order: 2;
                margin-top: 2rem;
            }

            .profile-content {
                width: 100%;
                order: 1;
            }
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body, html {
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-color);
            color: var(--dark-color);
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            display: flex;
            min-height: 100vh;
            width: 100%;
            padding: 2rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .profile-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .profile-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            border: var(--card-border);
            padding: 2.5rem;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
            margin-top: -4rem;
            position: relative;
            z-index: 1;
        }

        .profile-header {
            text-align: center;
            padding: 2rem;
            background: linear-gradient(135deg, #e6f0ff 0%, #cce0ff 100%);
            border-radius: 16px 16px 0 0;
            position: relative;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 102, 255, 0.1) 0%, rgba(0, 77, 153, 0.1) 100%);
            z-index: 0;
        }

        .profile-header h1 {
            color: var(--dark-color);
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .profile-header p {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }

        .profile-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            position: relative;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .section-header h2 {
            color: var(--dark-color);
            font-size: 1.5rem;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .section-header .progress {
            width: 200px;
            height: 6px;
            background-color: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }

        .section-header .progress-bar {
            background-color: var(--primary-color);
            height: 100%;
            transition: width 0.3s ease;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1);
            outline: none;
        }

        .form-control-lg {
            padding: 1.25rem;
            font-size: 1.1rem;
        }

        .form-group label {
            color: #495057;
            font-weight: 500;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 102, 255, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-hover) 0%, var(--secondary-color) 100%);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color) 0%, #ff8000 100%);
            border: none;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #cc0000 100%);
            border: none;
        }

        .badge {
            padding: 0.5em 1em;
            border-radius: 12px;
            font-weight: 500;
            margin-right: 0.5rem;
        }

        .badge.bg-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }

        .badge.bg-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #006600 100%);
            color: white;
        }

        .badge.bg-info {
            background: linear-gradient(135deg, var(--info-color) 0%, #004d99 100%);
            color: white;
        }

        .document-item,
        .benefit-item {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: var(--card-border);
            transition: all 0.3s ease;
        }

        .document-item:hover,
        .benefit-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--card-shadow);
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 500;
            background: #fff3cd;
            color: #856404;
            margin-right: 0.5rem;
        }

        .status-badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-badge-expired {
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .profile-card {
                padding: 1.5rem;
                margin-top: -2rem;
            }

            .profile-header {
                padding: 1.5rem;
            }

            .profile-header h1 {
                font-size: 2rem;
            }

            .form-control {
                padding: 0.75rem;
            }

            .btn {
                padding: 0.5rem 1rem;
            }
        }

        .form-group {
            margin-bottom: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            color: #495057;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            outline: none;
        }

        .form-group input:disabled,
        .form-group select:disabled,
        .form-group textarea:disabled {
            background: #f8f9fa;
            cursor: not-allowed;
        }

        .form-group input[type="date"] {
            padding-right: 2.5rem;
        }

        .form-group input[type="file"] {
            padding: 0.5rem;
            border: none;
            background: #f8f9fa;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
            background: linear-gradient(135deg, var(--primary-hover) 0%, var(--secondary-color) 100%);
        }

        .btn-primary i {
            font-size: 1.1rem;
        }

        .photo-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto;
            overflow: hidden;
            border: 3px solid var(--primary-color);
            box-shadow: var(--card-shadow);
        }

        .photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .document-item,
        .benefit-item {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: var(--card-border);
            transition: all 0.3s ease;
        }

        .document-item:hover,
        .benefit-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--card-shadow);
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-badge-expired {
            background: #f8d7da;
            color: #721c24;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .profile-card {
                padding: 1.5rem;
            }
            
            .profile-header {
                padding: 1.5rem;
            }
            
            .profile-header h1 {
                font-size: 2rem;
            }
            
            .form-group {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-container">
        <div class="profile-sidebar">
            <nav class="sidebar-nav">
                <a class="nav-link active" href="#dados-pessoais">
                    <i class='bx bx-user'></i>
                    <span>Dados Pessoais</span>
                </a>
                <a class="nav-link" href="#info-profissional">
                    <i class='bx bx-briefcase'></i>
                    <span>Informação Profissional</span>
                </a>
                <a class="nav-link" href="#documentos">
                    <i class='bx bx-file'></i>
                    <span>Documentos</span>
                </a>
                <a class="nav-link" href="#beneficios">
                    <i class='bx bx-gift'></i>
                    <span>Benefícios</span>
                </a>
                <a class="nav-link" href="#info-adicional">
                    <i class='bx bx-info-circle'></i>
                    <span>Informação Adicional</span>
                </a>
                <a class="nav-link" href="#comunicacao">
                    <i class='bx bx-envelope'></i>
                    <span>Comunicação</span>
                </a>
                <a class="nav-link" href="#seguranca">
                    <i class='bx bx-shield'></i>
                    <span>Segurança</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <!-- Espaço vazio para manter o layout -->
            </div>
        </div>
        <div class="profile-content">
            <div class="profile-header d-flex align-items-center">
                <div class="profile-info flex-grow-1">
                    <div class="d-flex flex-column">
                        <h1 class="mb-1">Nome Completo</h1>
                        <p class="mb-1">Cargo - Departamento</p>
                        <div class="status mb-2">
                            <span class="status-badge">Status</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-sm" onclick="window.location.href='alterar_senha.php'">
                                <i class='bx bx-lock'></i>
                                Alterar Senha
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="window.location.href='login.php'">
                                <i class='bx bx-log-out'></i>
                                Sair
                            </button>
                        </div>
                    </div>
                </div>
                <div class="profile-photo">
                    <img src="assets/img/default-avatar.png" alt="Foto de Perfil" 
                         id="foto-preview">
                    <input type="file" id="foto" name="foto" accept="image/*" 
                           class="d-none" onchange="handlePhotoUpload(this)">
                    <button class="btn btn-primary btn-sm mt-2" id="uploadPhotoBtn">
                        <i class='bx bx-upload'></i>
                        <span class="ms-1">Atualizar</span>
                    </button>
                </div>
            </div>
            <div class="profile-main-info">
                <div class="main-info-header">
                    <h2>Bem-vindo(a) à sua área pessoal</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="telefone">Telefone</label>
                    <input type="tel" id="telefone" name="telefone" 
                           class="form-control form-control-lg" 
                           placeholder="Ex: 912 345 678">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nif">NIF</label>
                    <input type="text" id="nif" name="nif" maxlength="9" 
                           class="form-control form-control-lg" 
                           placeholder="9 dígitos">
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="morada">Morada</label>
                    <textarea id="morada" name="morada" 
                              class="form-control form-control-lg" 
                              placeholder="Rua, Número, Cidade"></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="data_nascimento">Data de Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" 
                           class="form-control form-control-lg">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="genero">Género</label>
                    <select id="genero" name="genero" class="form-control form-control-lg">
                        <option value="">Selecione...</option>
                        <option value="masculino">Masculino</option>
                        <option value="feminino">Feminino</option>
                        <option value="outro">Outro</option>
                    </select>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class='bx bx-save'></i>
                        Atualizar Perfil
                    </button>
                </div>
            </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="profile-section">
                    <div class="section-header">
                        <h2>Informação Profissional</h2>
                        <div class="d-flex gap-2">
                            <span class="badge bg-primary">Data de Entrada</span>
                            <span class="badge bg-success">Cargo</span>
                            <span class="badge bg-info">Departamento</span>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="data_entrada">Data de Entrada</label>
                                <input type="text" id="data_entrada" name="data_entrada" readonly 
                                       class="form-control form-control-lg">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="cargo">Cargo</label>
                                <input type="text" id="cargo" name="cargo" readonly 
                                       class="form-control form-control-lg">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="departamento">Departamento</label>
                                <input type="text" id="departamento" name="departamento" readonly 
                                       class="form-control form-control-lg">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="coordenador">Coordenador</label>
                                <input type="text" id="coordenador" name="coordenador" readonly 
                                       class="form-control form-control-lg">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="profile-section">
                    <div class="section-header">
                        <h2>Documentos</h2>
                        <button type="button" class="btn btn-primary btn-sm" id="addDocument">
                            <i class='bx bx-plus'></i>
                            Adicionar Documento
                        </button>
                    </div>
                    <div id="documentos" class="row g-3">
                        <!-- Carregados via AJAX -->
                    </div>
                </div>

                <div class="profile-section">
                    <div class="section-header">
                        <h2>Benefícios</h2>
                        <button type="button" class="btn btn-primary btn-sm" id="addBenefit">
                            <i class='bx bx-plus'></i>
                            Ver Benefícios
                        </button>
                    </div>
                    <div id="beneficios" class="row g-3">
                        <!-- Carregados via AJAX -->
                    </div>
                </div>

                <div class="profile-section">
                    <div class="section-header">
                        <h2>Informação Adicional</h2>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-group">
            </div>

            <div class="profile-section" id="dados-pessoais">
                <div class="section-header">
                    <h2>Dados Pessoais</h2>
                    <span class="status-badge status-badge-pending">Pendente</span>
                    <div class="progress progress-sm mt-2">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 60%" 
                             aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
                            60% Completo
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 60%" 
                         aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">
                        60% Completo
                    </div>
                </div>
            </div>
            <form id="profileForm" class="row g-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nome">Nome Completo</label>
                        <input type="text" id="nome" name="nome" required 
                               class="form-control form-control-lg">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email Institucional</label>
                        <input type="email" id="email" name="email" required readonly 
                               class="form-control form-control-lg">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="tel" id="telefone" name="telefone" 
                               class="form-control form-control-lg" 
                               placeholder="Ex: 912 345 678">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nif">NIF</label>
                        <input type="text" id="nif" name="nif" maxlength="9" 
                               class="form-control form-control-lg" 
                               placeholder="9 dígitos">
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="morada">Morada</label>
                        <textarea id="morada" name="morada" 
                                  class="form-control form-control-lg" 
                                  placeholder="Rua, Número, Cidade"></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="data_nascimento">Data de Nascimento</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" 
                               class="form-control form-control-lg">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="genero">Género</label>
                        <select id="genero" name="genero" class="form-control form-control-lg">
                            <option value="">Selecione...</option>
                            <option value="masculino">Masculino</option>
                            <option value="feminino">Feminino</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="foto">Atualizar Foto</label>
                        <input type="file" id="foto" name="foto" accept="image/*" 
                               class="form-control form-control-lg">
                    </div>
                </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn-primary">
                                <i class='bx bx-save'></i>
                                Atualizar Perfil
                            </button>
                        </div>
                    </form>
                </div>

                <div class="profile-section">
                    <div class="section-header">
                        <h2>Informação Profissional</h2>
                    </div>
                    <div class="form-group">
                        <label for="data_entrada">Data de Entrada</label>
                        <input type="text" id="data_entrada" name="data_entrada" readonly>
                    </div>

                    <div class="form-group">
                        <label for="cargo">Cargo</label>
                        <input type="text" id="cargo" name="cargo" readonly>
                    </div>

                    <div class="form-group">
                        <label for="departamento">Departamento</label>
                        <input type="text" id="departamento" name="departamento" readonly>
                    </div>

                    <div class="form-group">
                        <label for="coordenador">Coordenador</label>
                        <input type="text" id="coordenador" name="coordenador" readonly>
                    </div>
                </div>

                <div class="profile-section">
                    <div class="section-header">
                        <h2>Documentos</h2>
                        <button type="button" class="btn-primary" id="addDocument">
                            <i class='bx bx-plus'></i>
                            Adicionar Documento
                        </button>
                    </div>
                    <div id="documentos">
                        <!-- Carregados via AJAX -->
                    </div>
                </div>

                <div class="profile-section">
                    <div class="section-header">
                        <h2>Benefícios</h2>
                        <button type="button" class="btn-primary" id="addBenefit">
                            <i class='bx bx-plus'></i>
                            Ver Benefícios
                        </button>
                    </div>
                    <div id="beneficios">
                        <!-- Carregados via AJAX -->
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-section">
            <h2>Documentos</h2>
            <div id="documentos">
                <!-- Carregados via AJAX -->
            </div>
        </div>

        <div class="profile-section">
            <h2>Benefícios</h2>
            <div id="beneficios">
                <!-- Carregados via AJAX -->
            </div>
        </div>
    </div>

    <script src="js/colaborador.js"></script>
    <?php include 'footer.php'; ?>
</body>
</html>
