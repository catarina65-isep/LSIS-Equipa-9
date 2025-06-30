<?php
session_start();
$_SESSION['usuario_id'] = $usuario['id'];
$_SESSION['id_perfilacesso'] = $usuario['id_perfilacesso'];
header('Location: colaborador.php');
exit;
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

<?php
session_start();

// Verifica se o usuário está logado e é um colaborador
if (!isset($_SESSION['usuario_id'])) {
    header('Location: UI/login.php');
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
    <title>Perfil do Colaborador</title>
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            /* Cores principais */
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

        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #212529;
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 3rem 1.5rem;
        }

        .profile-container {
            display: flex;
            gap: 3rem;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 2rem;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .profile-sidebar {
            flex: 0 0 280px;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 0 2rem 2rem 0;
            padding: 2.5rem;
            position: relative;
            z-index: 1;
        }

        .profile-sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-color) 50%, transparent 50%);
            z-index: 2;
        }

        .profile-content {
            flex: 1;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 2rem 0 0 2rem;
            padding: 3rem;
            position: relative;
            z-index: 1;
        }

        .profile-header {
            margin-bottom: 3rem;
            text-align: center;
            position: relative;
        }

        .profile-card {
            background: white;
            border-radius: 1.5rem;
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            box-shadow: var(--shadow-md);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            margin-bottom: var(--spacing-md);
            border-bottom: 2px solid #e9ecef;
            padding-bottom: var(--spacing-sm);
        }

        .card-header h2 {
            color: var(--dark-color);
            font-weight: 600;
        }

        /* Estilos para formulário */
        .form-group {
            margin-bottom: var(--spacing-md);
        }

        .form-group label {
            display: block;
            margin-bottom: var(--spacing-sm);
            color: var(--dark-color);
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: var(--spacing-sm);
            border: var(--card-border);
            border-radius: var(--border-radius-md);
            background-color: #f8f9fa;
            transition: all var(--transition-normal);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1);
            background-color: white;
            outline: none;
        }

        /* Estilos para botões */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: var(--spacing-sm) var(--spacing-lg);
            border-radius: var(--border-radius-md);
            transition: var(--transition-normal);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 102, 255, 0.2);
        }

        /* Estilos para ação de form */
        .form-actions {
            margin-top: var(--spacing-lg);
        }

        /* Estilos para a sidebar */
        .profile-sidebar {
            width: 280px;
            background-color: white;
            border-right: var(--card-border);
            padding: var(--spacing-lg);
            transition: var(--transition-normal);
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

        .logo img {
            max-width: 150px;
            height: auto;
        }

        .sidebar-nav {
            margin-top: var(--spacing-lg);
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            padding: var(--spacing-sm) var(--spacing-lg);
            border-radius: var(--border-radius-md);
            transition: var(--transition-normal);
            color: var(--dark-color);
            text-decoration: none;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: #f8f9fa;
            color: var(--primary-color);
        }

        .nav-link i {
            font-size: 1.2em;
        }

        /* Layout principal */
        .profile-container {
            display: flex;
            min-height: 100vh;
        }

        .profile-sidebar {
            width: 280px;
            background-color: var(--sidebar-bg);
            border-right: var(--card-border);
            padding: var(--spacing-lg);
            transition: var(--transition-normal);
        }

        .profile-content {
            flex-grow: 1;
            padding: var(--spacing-lg);
            background-color: var(--light-color);
        }

        /* Cabeçalho do perfil */
        .profile-header {
            padding: 2rem;
            background-color: #e6f0ff;
            border-bottom: 2px solid #e9ecef;
        }

        .profile-header h1 {
            font-size: var(--font-size-xl);
            margin: 0;
            font-weight: 600;
        }

        .profile-header .profile-info {
            margin-bottom: 2rem;
        }

        /* Cards */
        .profile-card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius-lg);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
            box-shadow: var(--card-shadow);
            border: var(--card-border);
        }

        .profile-card h2 {
            color: var(--dark-color);
            margin-bottom: var(--spacing-md);
            font-size: var(--font-size-xl);
        }

        /* Formulário */
        .form-group {
            margin-bottom: var(--spacing-md);
        }

        .form-group label {
            display: block;
            margin-bottom: var(--spacing-sm);
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

        /* Botões */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: var(--spacing-sm) var(--spacing-lg);
            border-radius: var(--border-radius-md);
            transition: var(--transition-normal);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        /* Status badges */
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
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-container">
                <div class="profile-sidebar">
                    <div class="sidebar-header">
                        <div class="logo">
                            <img src="/LSIS-Equipa-9/UI/assets/img/logos/tlantic-logo.jpg" alt="Tlantic Logo" class="mb-3">
                        </div>
                        <h2 class="mb-4">Meu Perfil</h2>
                    </div>
                <nav class="sidebar-nav">
                    <a class="nav-link active" href="#dados-pessoais">
                        <i class='bx bx-user'></i>
                        <span>Dados Pessoais</span>
                    </a>
                    <a class="nav-link" href="#documentos">
                        <i class='bx bx-file'></i>
                        <span>Documentos</span>
                    </a>
                </nav>
                <div class="sidebar-footer mt-auto">
                    <a href="logout.php" class="btn btn-outline-danger w-100">
                        <i class='bx bx-log-out'></i>
                        <span>Sair</span>
                    </a>
                </div>
            </div>

            <div class="profile-content">
                <!-- Header do Perfil -->
                <div class="profile-header">
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

                <!-- Seção de Dados Pessoais -->
                <div class="profile-card">
                    <div class="card-header">
                        <h2>Dados Pessoais</h2>
                    </div>
                    <form id="profileForm">
                        <!-- Seção Identificação -->
                        <div class="section-header mb-4">
                            <h3 class="section-title">Identificação</h3>
                            <div class="section-divider"></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nome"><i class='bx bx-user'></i> Nome Completo</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-user'></i></span>
                                        <input type="text" id="nome" name="nome" required 
                                            class="form-control form-control-lg">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dataNascimento"><i class='bx bx-calendar'></i> Data de Nascimento</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-calendar'></i></span>
                                        <input type="date" id="dataNascimento" name="dataNascimento" required 
                                            class="form-control form-control-lg">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seção Documentação -->
                        <div class="section-header mt-4 mb-4">
                            <h3 class="section-title">Documentação</h3>
                            <div class="section-divider"></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nif"><i class='bx bx-id-card'></i> NIF (Número de Identificação Fiscal)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-id-card'></i></span>
                                        <input type="text" id="nif" name="nif" required 
                                            class="form-control form-control-lg">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="niss"><i class='bx bx-shield-quarter'></i> NISS (Número de Identificação da Segurança Social)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-shield-quarter'></i></span>
                                        <input type="text" id="niss" name="niss" required 
                                            class="form-control form-control-lg">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numeroCartaoCidadao"><i class='bx bx-id-card'></i> Número do Cartão de Cidadão</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-id-card'></i></span>
                                        <input type="text" id="numeroCartaoCidadao" name="numeroCartaoCidadao" required 
                                            class="form-control form-control-lg">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dataValidadeCartao"><i class='bx bx-calendar-check'></i> Data de Validade do Cartão de Cidadão</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-calendar-check'></i></span>
                                        <input type="date" id="dataValidadeCartao" name="dataValidadeCartao" required 
                                            class="form-control form-control-lg">
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
                                        <input type="tel" id="telefone" name="telefone" required 
                                            class="form-control form-control-lg">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email"><i class='bx bx-envelope'></i> Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                                        <input type="email" id="email" name="email" required 
                                            class="form-control form-control-lg">
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
                                        <input type="number" id="numeroDependentes" name="numeroDependentes" min="0" 
                                            class="form-control form-control-lg">
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
                                        <input type="text" id="contactoEmergencia" name="contactoEmergencia" 
                                            class="form-control form-control-lg">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="relacaoEmergencia"><i class='bx bx-link'></i> Relação com o Contacto de Emergência</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-link'></i></span>
                                        <input type="text" id="relacaoEmergencia" name="relacaoEmergencia" 
                                            class="form-control form-control-lg">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telemovelEmergencia"><i class='bx bx-phone'></i> Telemóvel do Contacto de Emergência</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class='bx bx-phone'></i></span>
                                        <input type="tel" id="telemovelEmergencia" name="telemovelEmergencia" 
                                            class="form-control form-control-lg">
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

                <!-- Seção de Documentos -->
                <div class="profile-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Documentos</h2>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#documentModal">
                            <i class='bx bx-plus'></i>
                            Adicionar Documento
                        </button>
                    </div>
                    <div id="documentList">
                        <!-- Documentos serão adicionados aqui via JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Upload de Documento -->
        <div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="documentModalLabel">Adicionar Documento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="documentForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="documentName" class="form-label">Nome do Documento</label>
                                <input type="text" class="form-control" id="documentName" name="nome" required>
                            </div>
                            <div class="mb-3">
                                <label for="documentType" class="form-label">Tipo de Documento</label>
                                <select class="form-select" id="documentType" name="tipo" required>
                                    <option value="">Selecione...</option>
                                    <option value="contrato">Contrato</option>
                                    <option value="identificacao">Identificação</option>
                                    <option value="recibo">Recibo</option>
                                    <option value="outro">Outro</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="documentFile" class="form-label">Arquivo</label>
                                <input type="file" class="form-control" id="documentFile" name="arquivo" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="mb-3">
                                <label for="documentExpiry" class="form-label">Data de Validade (opcional)</label>
                                <input type="date" class="form-control" id="documentExpiry" name="data_validade">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="uploadDocument">Upload</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="js/colaborador.js"></script>
        <?php include 'footer.php'; ?>
    </body>
</body>
</html>
