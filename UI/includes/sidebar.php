<?php
// Verifica se o usuário está logado
if (!isset($_SESSION['utilizador_id'])) {
    header('Location: /LSIS-Equipa-9/UI/login.php');
    exit;
}

// Obter o nome do arquivo atual
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-wrapper d-flex flex-column">
        <!-- Logo -->
        <div class="text-center p-4">
            <a href="index.php" class="d-inline-block">
                <img src="/LSIS-Equipa-9/UI/assets/img/logos/tlantic-logo.jpg" alt="Tlantic" class="img-fluid" style="max-height: 50px; width: auto;">
                <span class="d-block text-white-50 small mt-2">Sistema de Gestão</span>
            </a>
        </div>
        
        <!-- Menu -->
        <nav class="flex-grow-1 px-3 pb-4">
            <ul class="nav flex-column" style="gap: 0.5rem;">
                <li class="nav-item">
                    <a href="index.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?= ($current_page == 'index.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10' ?>">
                        <div class="icon-wrapper <?= ($current_page == 'index.php') ? 'bg-white bg-opacity-10' : 'bg-primary bg-opacity-10' ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-dashboard <?= ($current_page == 'index.php') ? 'text-white' : 'text-primary' ?>'></i>
                        </div>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="equipe.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?= ($current_page == 'equipe.php' || $current_page == 'ver_colaborador.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10' ?>">
                        <div class="icon-wrapper <?= ($current_page == 'equipe.php' || $current_page == 'ver_colaborador.php') ? 'bg-white bg-opacity-10' : 'bg-success bg-opacity-10' ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-group <?= ($current_page == 'equipe.php' || $current_page == 'ver_colaborador.php') ? 'text-white' : 'text-success' ?>'></i>
                        </div>
                        <span>Minha Equipe</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="aniversariantes.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?= ($current_page == 'aniversariantes.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10' ?>">
                        <div class="icon-wrapper <?= ($current_page == 'aniversariantes.php') ? 'bg-white bg-opacity-10' : 'bg-warning bg-opacity-10' ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-gift <?= ($current_page == 'aniversariantes.php') ? 'text-white' : 'text-warning' ?>'></i>
                        </div>
                        <span>Aniversariantes</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="alertas.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?= ($current_page == 'alertas.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10' ?>">
                        <div class="icon-wrapper <?= ($current_page == 'alertas.php') ? 'bg-white bg-opacity-10' : 'bg-danger bg-opacity-10' ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-bell <?= ($current_page == 'alertas.php') ? 'text-white' : 'text-danger' ?>'></i>
                        </div>
                        <span>Alertas</span>
                    </a>
                </li>
            </ul>
            
            <!-- Menu Inferior -->
            <div class="mt-4 pt-3 border-top border-dark border-opacity-25">
                <h6 class="text-uppercase text-white-50 fw-bold small px-3 mb-3">Configurações</h6>
                
                <div class="nav-item">
                    <a href="perfil.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?= ($current_page == 'perfil.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10' ?>">
                        <div class="icon-wrapper <?= ($current_page == 'perfil.php') ? 'bg-white bg-opacity-10' : 'bg-secondary bg-opacity-10' ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-user <?= ($current_page == 'perfil.php') ? 'text-white' : 'text-secondary' ?>'></i>
                        </div>
                        <span>Meu Perfil</span>
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="configuracoes.php" class="nav-link d-flex align-items-center py-2 px-3 rounded-3 <?= ($current_page == 'configuracoes.php') ? 'active text-white bg-primary bg-opacity-25' : 'text-white-80 hover-bg-dark-10' ?>">
                        <div class="icon-wrapper <?= ($current_page == 'configuracoes.php') ? 'bg-white bg-opacity-10' : 'bg-info bg-opacity-10' ?> rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 36px; height: 36px;">
                            <i class='bx bxs-cog <?= ($current_page == 'configuracoes.php') ? 'text-white' : 'text-info' ?>'></i>
                        </div>
                        <span>Configurações</span>
                    </a>
                </div>
            </div>
        </nav>
        
        <!-- Rodapé da Sidebar -->
        <div class="p-3 text-center text-white-50 small">
            <div class="mb-2">v1.0.0</div>
            <div>© <?= date('Y') ?> Tlantic. Todos os direitos reservados.</div>
        </div>
    </div>
    
    <style>
        .bg-pink {
            background-color: #e83e8c !important;
        }
        
        .text-pink {
            color: #e83e8c !important;
        }
        
        .bg-purple {
            background-color: #9c27b0;
        }
        
        .text-purple {
            color: #9c27b0;
        }
    </style>
</aside>

<!-- Script para o menu mobile -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('show');
            });
        }
    });
</script>
