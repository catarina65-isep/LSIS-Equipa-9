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
