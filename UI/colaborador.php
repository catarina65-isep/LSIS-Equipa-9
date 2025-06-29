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
    <title>Meu Perfil - Tlantic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            /* Cores principais */
            --primary-color: #0066ff;
            --primary-hover: #0052cc;
            --secondary-color: #2c3e50;
            --success-color: #4CAF50;
            --info-color: #2196F3;
            --warning-color: #FFC107;
            --danger-color: #F44336;
            --light-color: #f8f9fa;
            --dark-color: #2c3e50;
            
            /* Cores complementares */
            --accent-1: #0066ff;
            --accent-2: #00ccff;
            --accent-3: #0052cc;
            --accent-4: #e6f0ff;
            --accent-5: #cce0ff;
            
            /* Cores para cards e elementos */
            --card-bg: #ffffff;
            --card-border: 1px solid rgba(0, 0, 0, 0.05);
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            
            /* Tipografia */
            --font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            --font-size-sm: 0.875rem;
            --font-size-base: 1rem;
            --font-size-lg: 1.125rem;
            --font-size-xl: 1.25rem;
            
            /* Espaçamento */
            --spacing-sm: 0.5rem;
            --spacing-md: 1rem;
            --spacing-lg: 1.5rem;
            --spacing-xl: 2rem;
            
            /* Bordas */
            --border-radius-sm: 8px;
            --border-radius-md: 12px;
            --border-radius-lg: 20px;
            
            /* Transições */
            --transition-fast: 0.2s ease;
            --transition-normal: 0.3s ease;
            --transition-slow: 0.4s ease;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--light-color);
            color: var(--dark-color);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Estilos para o header do perfil */
        .profile-header {
            padding: 2rem;
            background: linear-gradient(135deg, #e6f0ff 0%, #cce0ff 100%);
            border-bottom: 2px solid #e9ecef;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .profile-header .profile-card {
            background: white;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--card-shadow);
            padding: var(--spacing-lg);
        }

        .profile-image {
            width: 120px;
            height: 120px;
        }

        .profile-photo-container {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .profile-photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-item {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            color: var(--secondary-color);
            font-size: var(--font-size-sm);
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid #e9ecef;
        }

        .sidebar-footer .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .sidebar-footer .btn i {
            font-size: 1.1rem;
        }

        .stat-item i {
            color: var(--primary-color);
            font-size: 1.5em;
            width: 32px;
        }

        /* Estilos para os cards */
        .profile-card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius-lg);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
            box-shadow: var(--card-shadow);
            border: var(--card-border);
            transition: transform var(--transition-normal);
        }

        .profile-card:hover {
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
            font-weight: 600;
            color: #212529;
            font-size: 1.1rem;
        }

        .form-control {
            width: 100%;
            padding: 1rem 1.25rem;
            border: 2px solid #ced4da;
            border-radius: 0.75rem;
            background-color: #ffffff;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            color: #212529;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .form-control:focus {
            border-color: #0056b3;
            box-shadow: 0 0 0 0.25rem rgba(0,86,179,0.25);
            background-color: #ffffff;
            outline: none;
        }

        .form-control::placeholder {
            color: #6c757d;
            opacity: 1;
            font-size: 1rem;
        }

        .form-control:disabled {
            background-color: #e9ecef;
            opacity: 1;
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
                    <form id="profileForm" class="row g-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nome">Nome Completo</label>
                                <input type="text" id="nome" name="nome" required 
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" required 
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="morada">Morada</label>
                                <input type="text" id="morada" name="morada" required 
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="codigoPostal">Código Postal</label>
                                <input type="text" id="codigoPostal" name="codigoPostal" required 
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="observacoes">Observações</label>
                                <textarea id="observacoes" name="observacoes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-actions mt-4">
                                <button type="submit" class="btn btn-primary w-100">Salvar Alterações</button>
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
