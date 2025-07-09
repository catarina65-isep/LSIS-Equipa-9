<?php
// Obter o nome do arquivo atual
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<aside class="sidebar position-fixed h-100">
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
                    <a href="meu_perfil.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?php echo ($current_page == 'meu_perfil.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10'; ?>">
                        <div class="icon-wrapper <?php echo ($current_page == 'meu_perfil.php') ? 'bg-white bg-opacity-10' : 'bg-success bg-opacity-10'; ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-user <?php echo ($current_page == 'meu_perfil.php') ? 'text-white' : 'text-success'; ?>'></i>
                        </div>
                        <span>Meu Perfil</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="dash.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?php echo ($current_page == 'dash.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10'; ?>">
                        <div class="icon-wrapper <?php echo ($current_page == 'dash.php') ? 'bg-white bg-opacity-10' : 'bg-primary bg-opacity-10'; ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-dashboard <?php echo ($current_page == 'dash.php') ? 'text-white' : 'text-primary'; ?>'></i>
                        </div>
                        <span>Dashboard</span>
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
                    <a href="equipas.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?php echo ($current_page == 'equipas.php' || $current_page == 'equipa_detalhes.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10'; ?>">
                        <div class="icon-wrapper <?php echo ($current_page == 'equipas.php' || $current_page == 'equipa_detalhes.php') ? 'bg-white bg-opacity-10' : 'bg-danger bg-opacity-10'; ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-group <?php echo ($current_page == 'equipas.php' || $current_page == 'equipa_detalhes.php') ? 'text-white' : 'text-danger'; ?>'></i>
                        </div>
                        <span>Equipas</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="gerir_fichas.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?php echo ($current_page == 'gerir_fichas.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10'; ?>">
                        <div class="icon-wrapper <?php echo ($current_page == 'gerir_fichas.php') ? 'bg-white bg-opacity-10' : 'bg-info bg-opacity-10'; ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-file-doc <?php echo ($current_page == 'gerir_fichas.php') ? 'text-white' : 'text-info'; ?>'></i>
                        </div>
                        <span>Gerir Fichas</span>
                    </a>
                </li>
                

                <li class="nav-item">
                    <a href="campos_personalizados.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?php echo ($current_page == 'campos_personalizados.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10'; ?>">
                        <div class="icon-wrapper <?php echo ($current_page == 'campos_personalizados.php') ? 'bg-white bg-opacity-10' : 'bg-warning bg-opacity-10'; ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-edit-alt <?php echo ($current_page == 'campos_personalizados.php') ? 'text-white' : 'text-warning'; ?>'></i>
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
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="enviar-convite.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?php echo ($current_page == 'enviar-convite.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10'; ?>">
                        <div class="icon-wrapper <?php echo ($current_page == 'enviar-convite.php') ? 'bg-white bg-opacity-10' : 'bg-info bg-opacity-10'; ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-envelope <?php echo ($current_page == 'enviar-convite.php') ? 'text-white' : 'text-info'; ?>'></i>
                        </div>
                        <span>Enviar Convites</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="alertas.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?php echo ($current_page == 'alertas.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10'; ?>">
                        <div class="icon-wrapper <?php echo ($current_page == 'alertas.php') ? 'bg-white bg-opacity-10' : 'bg-pink bg-opacity-10'; ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-bell <?php echo ($current_page == 'alertas.php') ? 'text-white' : 'text-pink'; ?>'></i>
                        </div>
                        <span>Alertas</span>
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
        

    </div>
    
    <style>
        .bg-pink {
            background-color: #e83e8c !important;
        }
        
        .text-pink {
            color: #e83e8c !important;
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
            transition: all 0.3s !important;
            overflow-y: auto !important;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            transform: translateX(0) !important;
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
