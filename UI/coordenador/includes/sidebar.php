<?php
// Obter o nome do arquivo atual
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<aside class="sidebar position-fixed h-100">
    <div class="d-flex flex-column h-100">
        <!-- Logo -->
        <div class="text-center p-4">
            <a href="index.php" class="d-inline-block">
                <img src="/LSIS-Equipa-9/UI/assets/img/logos/tlantic-logo.jpg" alt="Tlantic" class="img-fluid" style="max-height: 50px; width: auto;">
                <span class="d-block text-white-50 small mt-2">Painel do Coordenador</span>
            </a>
        </div>
        
        <!-- Menu -->
        <nav class="flex-grow-1 px-3 pb-4">
            <ul class="nav flex-column" style="gap: 0.5rem;">
                <li class="nav-item">
                    <a href="index.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?php echo ($current_page == 'index.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10'; ?>">
                        <div class="icon-wrapper <?php echo ($current_page == 'index.php') ? 'bg-white bg-opacity-10' : 'bg-primary bg-opacity-10'; ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-dashboard <?php echo ($current_page == 'index.php') ? 'text-white' : 'text-primary'; ?>'></i>
                        </div>
                        <span>Página Inicial</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="dashCoordenador.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?php echo ($current_page == 'dashCoordenador.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10'; ?>">
                        <div class="icon-wrapper <?php echo ($current_page == 'dashCoordenador.php') ? 'bg-white bg-opacity-10' : 'bg-primary bg-opacity-10'; ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-dashboard <?php echo ($current_page == 'dashCoordenador.php') ? 'text-white' : 'text-primary'; ?>'></i>
                        </div>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="equipe.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?php echo ($current_page == 'equipe.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10'; ?>">
                        <div class="icon-wrapper <?php echo ($current_page == 'equipe.php') ? 'bg-white bg-opacity-10' : 'bg-success bg-opacity-10'; ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-group <?php echo ($current_page == 'equipe.php') ? 'text-white' : 'text-success'; ?>'></i>
                        </div>
                        <span>Minha Equipe</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="projetos.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?php echo ($current_page == 'projetos.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10'; ?>">
                        <div class="icon-wrapper <?php echo ($current_page == 'projetos.php') ? 'bg-white bg-opacity-10' : 'bg-warning bg-opacity-10'; ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-briefcase <?php echo ($current_page == 'projetos.php') ? 'text-white' : 'text-warning'; ?>'></i>
                        </div>
                        <span>Projetos</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="aniversariantes.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?php echo ($current_page == 'aniversariantes.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10'; ?>">
                        <div class="icon-wrapper <?php echo ($current_page == 'aniversariantes.php') ? 'bg-white bg-opacity-10' : 'bg-info bg-opacity-10'; ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-cake <?php echo ($current_page == 'aniversariantes.php') ? 'text-white' : 'text-info'; ?>'></i>
                        </div>
                        <span>Aniversariantes</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="relatorios.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?php echo ($current_page == 'relatorios.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10'; ?>">
                        <div class="icon-wrapper <?php echo ($current_page == 'relatorios.php') ? 'bg-white bg-opacity-10' : 'bg-danger bg-opacity-10'; ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-report <?php echo ($current_page == 'relatorios.php') ? 'text-white' : 'text-danger'; ?>'></i>
                        </div>
                        <span>Relatórios</span>
                    </a>
                </li>
            </ul>
            
            <!-- Menu Collapse -->
            <div class="mt-4 pt-3 border-top border-dark border-opacity-25">
                <h6 class="text-uppercase text-white-50 fw-bold small px-3 mb-3">Ferramentas</h6>
                
                <div class="nav-item">
                    <a href="ajuda.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?php echo ($current_page == 'ajuda.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10'; ?>">
                        <div class="icon-wrapper <?php echo ($current_page == 'ajuda.php') ? 'bg-white bg-opacity-10' : 'bg-pink bg-opacity-10'; ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-info-circle <?php echo ($current_page == 'ajuda.php') ? 'text-white' : 'text-pink'; ?>'></i>
                        </div>
                        <span>Ajuda</span>
                    </a>
                </div>
            </div>
        </nav>
        
        <!-- Rodapé -->
        <div class="mt-auto p-3 text-center text-white-50 small">
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center justify-content-center text-white-50 text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nome'] ?? $_SESSION['utilizador_nome'] ?? 'Usuário') ?>" alt="" width="32" height="32" class="rounded-circle me-2">
                    <span class="d-none d-md-inline"><?= htmlspecialchars($_SESSION['nome'] ?? $_SESSION['utilizador_nome'] ?? 'Usuário') ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser">
                    <li><a class="dropdown-item" href="perfil.php">Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="/LSIS-Equipa-9/UI/logout.php">Sair</a></li>
                </ul>
            </div>
            <div class="mt-2">
                <small>Versão 1.0.0</small>
            </div>
        </div>
    </div>
    
    <style>
        /* Overlay para a barra lateral em telas menores */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
            backdrop-filter: blur(3px);
        }
        
        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }
        
        @media (min-width: 992px) {
            .sidebar-overlay {
                display: none !important;
            }
        }
        
        .sidebar {
            width: 250px !important;
            min-height: 100vh;
            position: fixed !important;
            left: 0 !important;
            top: 0 !important;
            bottom: 0 !important;
            z-index: 1000 !important;
            background: linear-gradient(180deg, #1a2a3a 0%, #2c3e50 100%) !important;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1) !important;
            transition: transform 0.3s ease-in-out !important;
            overflow-y: auto !important;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
            -webkit-overflow-scrolling: touch;
            transform: translateX(-100%);
        }
        
        .sidebar.show {
            transform: translateX(0);
            box-shadow: 2px 0 20px rgba(0,0,0,0.2) !important;
        }
        
        @media (min-width: 992px) {
            .sidebar {
                transform: translateX(0) !important;
                box-shadow: 2px 0 10px rgba(0,0,0,0.1) !important;
            }
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
        
        .hover-bg-dark-10:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        .text-white-80 {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .text-white-60 {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .bg-pink {
            background-color: #e91e63;
        }
        
        .text-pink {
            color: #e91e63;
        }
        
        .nav-link {
            transition: all 0.2s;
        }
        
        .nav-link:hover {
            transform: translateX(5px);
        }
        
        .icon-wrapper {
            transition: all 0.3s;
        }
    </style>
</aside>
