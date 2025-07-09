<?php
// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário está logado e é um coordenador
if (!isset($_SESSION['utilizador_id']) || $_SESSION['id_perfilacesso'] != 3) {
    header('Location: /LSIS-Equipa-9/UI/login.php');
    exit;
}

// Dados do dashboard
$total_equipe = 15;
$aniversariantes_mes = 3;
$alertas_pendentes = 2;
$projetos_ativos = 5;
$equipes_coordenadas = 2;

// Dados de exemplo para as tabelas
$atividades_recentes = [
    ['Reunião de equipe', 'Hoje, 10:00', 'Concluído'],
    ['Revisão de projeto', 'Ontem, 14:30', 'Em andamento'],
    ['Entrevista de candidato', '05/07/2025, 11:00', 'Concluído']
];

$aniversariantes = [
    ['Ana Silva', '10/07/2025', 'Desenvolvedor Sênior'],
    ['Carlos Mendes', '15/07/2025', 'Analista de Dados'],
    ['Mariana Costa', '20/07/2025', 'Designer UX/UI']
];

// Define o título da página
$page_title = "Dashboard do Coordenador - Tlantic";
$page_heading = "Visão Geral";

// Inclui o template base
include_once __DIR__ . '/includes/base_template.php';
?>

<!-- Conteúdo da página -->
<div class="container-fluid">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent p-0 mb-3">
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Bem-vindo(a), <?= htmlspecialchars($_SESSION['nome'] ?? 'Coordenador') ?></h1>
        <div>
            <span class="text-muted me-3"><?= date('d/m/Y') ?></span>
            <button class="btn btn-outline-secondary">
                <i class="fas fa-sync-alt me-1"></i> Atualizar
            </button>
        </div>
    </div>

    <!-- Filtros e Ações Rápidas -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-filter me-1"></i> Filtros
            </button>
            <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                <li><a class="dropdown-item" href="#">Hoje</a></li>
                <li><a class="dropdown-item" href="#">Esta Semana</a></li>
                <li><a class="dropdown-item" href="#">Este Mês</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#">Personalizado</a></li>
            </ul>
        </div>
        <button class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nova Atividade
        </button>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="stat-card bg-primary">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Total da Equipe</h6>
                        <h3 class="mb-0"><?= $total_equipe ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="stat-card bg-success">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-birthday-cake fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Aniversariantes</h6>
                        <h3 class="mb-0"><?= $aniversariantes_mes ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="stat-card bg-warning">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-bell fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Alertas</h6>
                        <h3 class="mb-0"><?= $alertas_pendentes ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="stat-card bg-danger">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-project-diagram fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Projetos Ativos</h6>
                        <h3 class="mb-0"><?= $projetos_ativos ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Atividades Recentes e Aniversariantes -->
    <div class="row">
        <div class="col-12 col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Atividades Recentes</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Atividade</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($atividades_recentes as $atividade) { ?>
                                    <tr>
                                        <td><?= $atividade[0] ?></td>
                                        <td><?= $atividade[1] ?></td>
                                        <td><span class="badge bg-success"><?= $atividade[2] ?></span></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Aniversariantes do Mês</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($aniversariantes as $aniversariante) { ?>
                        <div class="aniversariante-card">
                            <div class="aniversariante-avatar"><?= substr($aniversariante[0], 0, 1) ?></div>
                            <div class="aniversariante-info">
                                <h6><?= $aniversariante[0] ?></h6>
                                <small><?= $aniversariante[2] ?></small>
                            </div>
                            <div class="aniversariante-data">
                                <i class="fas fa-calendar-alt"></i> <?= $aniversariante[1] ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Ações Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <a href="nova-tarefa.php" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="icon-box bg-soft-primary text-primary mx-auto mb-3">
                                            <i class="fas fa-tasks"></i>
                                        </div>
                                        <h6 class="mb-0">Nova Tarefa</h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="avaliacoes.php" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="icon-box bg-soft-success text-success mx-auto mb-3">
                                            <i class="fas fa-clipboard-check"></i>
                                        </div>
                                        <h6 class="mb-0">Avaliações</h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="relatorios.php" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="icon-box bg-soft-warning text-warning mx-auto mb-3">
                                            <i class="fas fa-chart-bar"></i>
                                        </div>
                                        <h6 class="mb-0">Relatórios</h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="configuracoes.php" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="icon-box bg-soft-info text-info mx-auto mb-3">
                                            <i class="fas fa-cog"></i>
                                        </div>
                                        <h6 class="mb-0">Configurações</h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Inclui o rodapé do template base
include_once __DIR__ . '/includes/base_footer.php';
?>

<!-- Scripts JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Garante que a página começa no topo
    window.scrollTo(0, 0);
    document.documentElement.scrollTop = 0;
    document.body.scrollTop = 0;
    
    // Inicializa tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Toggle da barra lateral em telas pequenas
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        
        if (sidebarToggle && sidebar && mainContent) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                
                // Salvar o estado no localStorage
                if (sidebar.classList.contains('collapsed')) {
                    localStorage.setItem('sidebarCollapsed', 'true');
                } else {
                    localStorage.setItem('sidebarCollapsed', 'false');
                }
            });
            
            // Verificar o estado salvo
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            }
        }
        
        // Adicionar classe de carregamento para animações
        setTimeout(() => {
            document.body.classList.add('loaded');
        }, 100);
        
        // Elementos da interface
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        const sidebarOverlay = document.createElement('div');
        sidebarOverlay.className = 'sidebar-overlay';
        document.body.appendChild(sidebarOverlay);
        
        // Função para alternar a barra lateral
        function toggleSidebar() {
            const isSidebarVisible = sidebar.classList.contains('show');
            
            if (isSidebarVisible) {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';
            } else {
                sidebar.classList.add('show');
                sidebarOverlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        }
        
        // Evento de clique no botão de alternar
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleSidebar();
            });
        }
        
        // Fechar a barra lateral ao clicar no overlay
        sidebarOverlay.addEventListener('click', () => {
            toggleSidebar();
        });
        
        // Fechar a barra lateral ao clicar em um link do menu em telas menores
        const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    toggleSidebar();
                }
            });
        });
        
        // Fechar a barra lateral ao redimensionar para uma tela maior
        function handleResize() {
            if (window.innerWidth >= 992) {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        }
        
        // Adicionar event listener para redimensionamento
        window.addEventListener('resize', handleResize);
        
        // Limpar event listeners ao desmontar
        window.addEventListener('unload', function() {
            window.removeEventListener('resize', handleResize);
            if (sidebarToggle) {
                sidebarToggle.removeEventListener('click', toggleSidebar);
            }
            if (sidebarOverlay) {
                sidebarOverlay.removeEventListener('click', toggleSidebar);
            }
        });
    });
</script>

</body>
</html>