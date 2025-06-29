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
            /* Cores da Tlantic */
            --primary-color: #0066ff;
            --primary-hover: #0052cc;
            --secondary-color: #004d99;
            --header-color: #e6f0ff;
            --success-color: #4CAF50;
            --info-color: #2196F3;
            --warning-color: #FFC107;
            --danger-color: #F44336;
            --light-color: #f8f9fa;
            --dark-color: #2c3e50;
            --header-height: 70px;
            --transition: all 0.3s ease;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --card-border: 1px solid rgba(0, 0, 0, 0.05);
            
            /* Paleta de cores complementares */
            --accent-1: #003366;
            --accent-2: #00264d;
            --accent-3: #001a33;
            --accent-4: #e6f0ff;
            --accent-5: #cce0ff;
            
            /* Cores para a sidebar */
            --sidebar-bg: #e6f0ff;
            --sidebar-header-bg: linear-gradient(135deg, #cce0ff 0%, #0066ff 100%);
            
            /* Cores para cards e elementos */
            --card-bg: #ffffff;
            --card-header-bg: #f8f9fa;
            --card-border-radius: 12px;
            
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
            --border-radius-lg: 16px;
            
            /* Transições */
            --transition-fast: 0.2s ease-in-out;
            --transition-normal: 0.3s ease-in-out;
            --transition-slow: 0.4s ease-in-out;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--accent-4);
            color: var(--dark-color);
            line-height: 1.6;
            transition: var(--transition-normal);
        }

        /* Estilos para o header */
        .profile-header {
            background: linear-gradient(135deg, var(--accent-4) 0%, var(--accent-5) 100%);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
        }

        .profile-header h1 {
            color: white;
            margin-bottom: 1rem;
        }

        .profile-header .profile-info {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .profile-header .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .profile-header .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .profile-header .profile-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-header .photo-container {
            position: relative;
            width: 150px;
            height: 150px;
        }

        .profile-header .photo-container .photo-upload-btn {
            position: absolute;
            bottom: -60px;
            left: 50%;
            transform: translateX(-50%);
            padding: 0.4rem 1.2rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius-md);
            cursor: pointer;
            font-size: 0.8rem;
            transition: var(--transition-normal);
            z-index: 1;
        }

        .profile-header .profile-photo .photo-upload-btn:hover {
            background: var(--primary-hover);
            transform: translateX(-50%) translateY(-2px);
        }

        .profile-header .profile-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-header .profile-details {
            color: var(--dark-color);
        }

        .profile-header .profile-details h2 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .profile-header .profile-details p {
            margin: 0.5rem 0;
            opacity: 0.9;
        }

        /* Estilos para os cards */
        .profile-card {
            background: white;
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
            transition: var(--transition-normal);
        }

        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .profile-card h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        /* Estilos para o formulário */
        .form-control {
            border: 1px solid var(--accent-4);
            padding: 0.75rem;
            border-radius: var(--border-radius-md);
            transition: var(--transition-normal);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 102, 255, 0.25);
        }

        /* Estilos para botões */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius-md);
            transition: var(--transition-normal);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        /* Estilos para a sidebar */
        .profile-sidebar {
            background: linear-gradient(135deg, var(--accent-4) 0%, var(--accent-5) 100%);
            border-right: 1px solid var(--accent-2);
            padding: 2rem;
            transition: var(--transition-normal);
        }

        .profile-sidebar .nav-link {
            color: var(--dark-color);
            padding: 1rem;
            border-radius: var(--border-radius-md);
            transition: var(--transition-normal);
        }

        .profile-sidebar .nav-link:hover {
            background: var(--accent-4);
            color: var(--primary-color);
        }

        .profile-sidebar .nav-link.active {
            background: var(--primary-color);
            color: white;
        }

        /* Estilos para documentos e benefícios */
        .document-item, .benefit-item {
            background: var(--accent-4);
            border-radius: var(--border-radius-md);
            padding: 1rem;
            margin-bottom: 1rem;
            transition: var(--transition-normal);
        }

        .document-item:hover, .benefit-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* Estilos para o tema */
        .theme-toggle {
            background: var(--accent-4);
            border: 1px solid var(--accent-2);
            border-radius: var(--border-radius-md);
            padding: 0.5rem;
            cursor: pointer;
            transition: var(--transition-normal);
        }

        .theme-toggle:hover {
            background: var(--accent-5);
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
            background-color: #f8f9fa;
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
                    <h2 class="text-center mb-4">Meu Perfil</h2>
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
            </div>

            <div class="profile-content">
                <!-- Header do Perfil -->
                <div class="profile-header">
                    <div class="profile-info">
                        <div class="d-flex align-items-center mb-3">
                            <div class="photo-container me-4">
                                <div class="profile-photo">
                                    <img src="<?php echo htmlspecialchars($data['foto'] ?? 'img/default-profile.png'); ?>" alt="" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <button class="photo-upload-btn" onclick="document.getElementById('foto-upload').click()">
                                    <i class='bx bx-camera'></i> Atualizar Foto
                                </button>
                                <input type="file" id="foto-upload" name="foto" accept="image/*" style="display: none;" onchange="handlePhotoUpload(this)">
                            </div>
                            <div class="profile-details">
                                <h1 class="mb-1">Nome Completo</h1>
                                <div class="mt-3">
                                    <div class="stat-item mb-2">
                                        <i class='bx bx-briefcase'></i>
                                        <span>Cargo:</span>
                                    </div>
                                    <div class="stat-item">
                                        <i class='bx bx-building'></i>
                                        <span>Departamento:</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="profile-actions d-flex gap-2">
                            </button>
                        </div>
                        <input type="file" id="profilePhoto" name="profilePhoto" accept="image/*" style="display: none;" onchange="handlePhotoUpload(this)">
                    </div>
                </div>

                <!-- Seção de Dados Pessoais -->
                <div class="profile-card">
                    <h2>Dados Pessoais</h2>
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
                                <label for="nif">NIF</label>
                                <input type="text" id="nif" name="nif" required 
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dataNascimento">Data de Nascimento</label>
                                <input type="date" id="dataNascimento" name="dataNascimento" required 
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="localidade">Localidade</label>
                                <input type="text" id="localidade" name="localidade" required 
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cargo">Cargo</label>
                                <input type="text" id="cargo" name="cargo" required 
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="departamento">Departamento</label>
                                <input type="text" id="departamento" name="departamento" required 
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefone">Telefone</label>
                                <input type="tel" id="telefone" name="telefone" required 
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
                            <button type="submit" class="btn btn-primary w-100">Guardar Alterações</button>
                        </div>
                    </form>
                </div>

                <!-- Seção de Documentos -->
                <div class="profile-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Documentos</h2>
                        <button type="button" class="btn btn-primary" id="addDocument">
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

        <script src="js/colaborador.js"></script>
        <?php include 'footer.php'; ?>
    </body>
</body>
</html>
