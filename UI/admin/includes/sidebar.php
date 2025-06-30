<?php
// Obter o nome do arquivo atual
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<aside class="sidebar" style="width: var(--sidebar-width); background: linear-gradient(180deg, #1a2a3a 0%, #2c3e50 100%); position: fixed; left: 0; top: 0; bottom: 0; z-index: 1000; box-shadow: 0 0 2rem 0 rgba(0, 0, 0, 0.1); transition: all 0.3s ease; overflow-y: auto;">
    <div class="d-flex flex-column h-100">
        <!-- Logo -->
        <div class="text-center p-4">
            <a href="dashboard.php" class="d-inline-block">
                <img src="/LSIS-Equipa-9/UI/assets/img/logos/tlantic-logo.jpg" alt="Tlantic" class="img-fluid" style="max-height: 50px; width: auto;">
                <span class="d-block text-white-50 small mt-2">Sistema de Gestão</span>
            </a>
        </div>
        
        <!-- Menu -->
        <nav class="flex-grow-1 px-3 pb-4">
            <ul class="nav flex-column" style="gap: 0.5rem;">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?php echo ($current_page == 'dashboard.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10'; ?>">
                        <div class="icon-wrapper <?php echo ($current_page == 'dashboard.php') ? 'bg-white bg-opacity-10' : 'bg-primary bg-opacity-10'; ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-dashboard <?php echo ($current_page == 'dashboard.php') ? 'text-white' : 'text-primary'; ?>'></i>
                        </div>
                        <span>Dashboard</span>
                        <span class="badge <?php echo ($current_page == 'dashboard.php') ? 'bg-white text-primary' : 'bg-primary'; ?> rounded-pill ms-auto">5</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="usuarios.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?php echo ($current_page == 'usuarios.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10'; ?>">
                        <div class="icon-wrapper <?php echo ($current_page == 'usuarios.php') ? 'bg-white bg-opacity-10' : 'bg-success bg-opacity-10'; ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-user <?php echo ($current_page == 'usuarios.php') ? 'text-white' : 'text-success'; ?>'></i>
                        </div>
                        <span>Usuários</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="perfis.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?php echo ($current_page == 'perfis.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10'; ?>">
                        <div class="icon-wrapper <?php echo ($current_page == 'perfis.php') ? 'bg-white bg-opacity-10' : 'bg-info bg-opacity-10'; ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-id-card <?php echo ($current_page == 'perfis.php') ? 'text-white' : 'text-info'; ?>'></i>
                        </div>
                        <span>Perfis</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="colaboradores.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?php echo ($current_page == 'colaboradores.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10'; ?>">
                        <div class="icon-wrapper <?php echo ($current_page == 'colaboradores.php') ? 'bg-white bg-opacity-10' : 'bg-warning bg-opacity-10'; ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-group <?php echo ($current_page == 'colaboradores.php') ? 'text-white' : 'text-warning'; ?>'></i>
                        </div>
                        <span>Colaboradores</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="dashboard_colaboradores.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?php echo ($current_page == 'dashboard_colaboradores.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10'; ?>">
                        <div class="icon-wrapper <?php echo ($current_page == 'dashboard_colaboradores.php') ? 'bg-white bg-opacity-10' : 'bg-danger bg-opacity-10'; ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-pie-chart-alt-2 <?php echo ($current_page == 'dashboard_colaboradores.php') ? 'text-white' : 'text-danger'; ?>'></i>
                        </div>
                        <span>Dashboard Colaboradores</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="campos_personalizados.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 text-white-80 hover-bg-dark-10">
                        <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-edit-alt text-warning'></i>
                        </div>
                        <span>Campos Personalizados</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="relatorios.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 text-white-80 hover-bg-dark-10">
                        <div class="icon-wrapper bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-report text-danger'></i>
                        </div>
                        <span>Relatórios</span>
                        <span class="badge bg-danger rounded-pill ms-auto">3</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="configuracoes.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 text-white-80 hover-bg-dark-10">
                        <div class="icon-wrapper bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-cog text-secondary'></i>
                        </div>
                        <span>Configurações</span>
                    </a>
                </li>
            </ul>
            
            <!-- Menu Collapse -->
            <div class="mt-4 pt-3 border-top border-dark border-opacity-25">
                <h6 class="text-uppercase text-white-50 fw-bold small px-3 mb-3">Ferramentas</h6>
                
                <div class="nav-item">
                    <a href="#" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 text-white-80 hover-bg-dark-10">
                        <div class="icon-wrapper bg-purple bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-message-square-dots text-purple'></i>
                        </div>
                        <span>Suporte</span>
                    </a>
                </div>
                
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
        
        <!-- User Profile -->
        <div class="p-3 border-top border-dark border-opacity-25">
            <div class="d-flex align-items-center">
                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User" class="rounded-circle me-2" width="40" height="40">
                <div class="ms-2">
                    <div class="text-white fw-medium">Admin User</div>
                    <small class="text-white-50">Administrador</small>
                </div>
                <div class="dropdown ms-auto">
                    <a href="#" class="text-white-50" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class='bx bx-dots-vertical-rounded'></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class='bx bx-user me-2'></i>Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class='bx bx-cog me-2'></i>Configurações</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#"><i class='bx bx-log-out me-2'></i>Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .sidebar {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
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
        
        .bg-purple {
            background-color: #9c27b0;
        }
        
        .text-purple {
            color: #9c27b0;
        }
        
        .bg-pink {
            background-color: #e91e63;
        }
        
        .text-pink {
            color: #e91e63;
        }
    </style>
</aside>
                    <span>Configurações</span>
                </a>
            </li>
        </ul>
        <hr>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://via.placeholder.com/30" alt="" width="30" height="30" class="rounded-circle me-2">
                <strong><?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="perfil.php">Perfil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="../logout.php">Sair</a></li>
            </ul>
        </div>
        <div class="mt-auto pt-3" style="margin-top: auto !important; border-top: 1px solid rgba(255,255,255,0.1);">
            <div class="d-flex align-items-center justify-content-center">
                <div class="text-center">
                    <div class="text-white-50 small mb-1">Desenvolvido por</div>
                    <div class="d-flex align-items-center justify-content-center">
                        <div style="background: #fff; border-radius: 4px; padding: 4px 8px; margin-right: 8px;">
                            <span style="color: #2c3e50; font-weight: 700; font-size: 0.9rem;">TLANTIC</span>
                        </div>
                        <small class="text-white-50">© 2025</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
