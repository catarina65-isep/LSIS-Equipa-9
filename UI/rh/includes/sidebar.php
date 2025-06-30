<?php
// Obter o nome do arquivo atual
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<aside class="sidebar">
    <div class="text-center mb-4">
        <h4>Painel RH</h4>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <i class='bx bxs-dashboard'></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="dashboard_colaboradores.php" class="nav-link <?php echo ($current_page == 'dashboard_colaboradores.php') ? 'active' : ''; ?>">
                <i class='bx bxs-user-detail'></i> Dashboard Colaboradores
            </a>
        </li>
        <li class="nav-item">
            <a href="colaboradores.php" class="nav-link <?php echo ($current_page == 'colaboradores.php') ? 'active' : ''; ?>">
                <i class='bx bxs-user-detail'></i> Colaboradores
            </a>
        </li>
        <li class="nav-item">
            <a href="documentos.php" class="nav-link <?php echo ($current_page == 'documentos.php') ? 'active' : ''; ?>">
                <i class='bx bxs-file-doc'></i> Documentos
            </a>
        </li>
        <li class="nav-item">
            <a href="relatorios.php" class="nav-link <?php echo ($current_page == 'relatorios.php') ? 'active' : ''; ?>">
                <i class='bx bxs-report'></i> Relatórios
            </a>
        </li>
        <li class="nav-item">
            <a href="configuracoes.php" class="nav-link <?php echo ($current_page == 'configuracoes.php') ? 'active' : ''; ?>">
                <i class='bx bxs-cog'></i> Configurações
            </a>
        </li>
        <li class="nav-item mt-4">
            <a href="../logout.php" class="nav-link text-danger">
                <i class='bx bx-log-out'></i> Sair
            </a>
        </li>
    </ul>
</aside>
