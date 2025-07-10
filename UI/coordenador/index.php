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

// Incluir classes necessárias
require_once __DIR__ . '/../../BLL/CoordenadorBLL.php';
require_once __DIR__ . '/../../BLL/EquipaBLL.php';
require_once __DIR__ . '/../../BLL/ColaboradorBLL.php';

// Inicializar BLLs
$coordenadorBLL = new CoordenadorBLL();
$equipaBLL = new EquipaBLL();
$colaboradorBLL = new ColaboradorBLL();

// Obter ID do coordenador logado
$idCoordenador = $_SESSION['utilizador_id'];

// Obter dados do coordenador
$coordenador = $coordenadorBLL->obterDadosCoordenador($idCoordenador);

// Obter equipes gerenciadas pelo coordenador
$equipes = $coordenadorBLL->obterEquipesGerenciadas($idCoordenador);
$equipes_coordenadas = count($equipes);

// Calcular total de membros nas equipes
$total_equipe = 0;
$membros_unicos = [];

foreach ($equipes as $equipa) {
    $membros = $equipaBLL->obterMembrosEquipa($equipa['id_equipa']);
    foreach ($membros as $membro) {
        if (!in_array($membro['id_colaborador'], $membros_unicos)) {
            $membros_unicos[] = $membro['id_colaborador'];
            $total_equipe++;
        }
    }
}

// Obter aniversariantes do mês
$mes_atual = date('m');
$aniversariantes = $colaboradorBLL->obterAniversariantesDoMes($mes_atual);
$aniversariantes_mes = count($aniversariantes);

// Obter alertas pendentes (exemplo - implementar conforme necessário)
$alertas_pendentes = 0; // Implementar lógica de alertas

// Obter projetos ativos (exemplo - implementar conforme necessário)
$projetos_ativos = 0; // Implementar lógica de projetos

// Obter atividades recentes (exemplo - implementar conforme necessário)
$atividades_recentes = []; // Implementar lógica de atividades

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
        <div>
            <h1 class="h3 mb-1">Bem-vindo(a), <?= htmlspecialchars($_SESSION['nome'] ?? 'Coordenador') ?></h1>
            <p class="text-muted mb-0" id="current-time">
                <i class="far fa-calendar-alt me-1"></i>
                <?= ucfirst(utf8_encode(strftime('%A, %d de %B de %Y'))) ?>
                <span class="ms-2">
                    <i class="far fa-clock me-1"></i>
                    <?= date('H:i') ?>
                </span>
            </p>
        </div>
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
                        <h6 class="mb-1">Aniversariantes do Mês</h6>
                        <h3 class="mb-0"><?= $aniversariantes_mes ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="stat-card bg-warning">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-users-cog fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Equipes Gerenciadas</h6>
                        <h3 class="mb-0"><?= $equipes_coordenadas ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="stat-card bg-info">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-tasks fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Tarefas Pendentes</h6>
                        <h3 class="mb-0"><?= $tarefas_pendentes ?? 0 ?></h3>
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
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Data de Nascimento</th>
                                    <th>Cargo</th>
                                    <th>Departamento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($aniversariantes)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Nenhum aniversariante este mês.</td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($aniversariantes as $aniversariante): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($aniversariante['nome']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($aniversariante['data_nascimento'])) ?></td>
                                        <td><?= htmlspecialchars($aniversariante['cargo'] ?? 'Não informado') ?></td>
                                        <td><?= htmlspecialchars($aniversariante['departamento'] ?? 'Não informado') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
                    <?php if (empty($aniversariantes)): ?>
                        <div class="alert alert-info mb-0">Nenhum aniversariante este mês.</div>
                    <?php else: ?>
                        <?php foreach ($aniversariantes as $aniversariante): ?>
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <?= strtoupper(substr($aniversariante['nome'], 0, 1)) ?>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?= htmlspecialchars($aniversariante['nome']) ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($aniversariante['cargo'] ?? 'Sem cargo definido') ?></small>
                                </div>
                                <div class="text-muted small">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <?= date('d/m', strtotime($aniversariante['data_nascimento'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Minhas Equipes -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Minhas Equipes</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($equipes)): ?>
                        <div class="alert alert-info mb-0">Você não está gerenciando nenhuma equipe no momento.</div>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($equipes as $equipa): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h5 class="card-title mb-0">
                                                    <a href="equipe.php?id=<?= $equipa['id_equipa'] ?>" class="text-decoration-none">
                                                        <?= htmlspecialchars($equipa['nome']) ?>
                                                    </a>
                                                </h5>
                                                <span class="badge bg-primary">
                                                    <?= $equipa['total_membros'] ?? 0 ?> membros
                                                </span>
                                            </div>
                                            <?php if (!empty($equipa['descricao'])): ?>
                                                <p class="card-text text-muted small">
                                                    <?= htmlspecialchars($equipa['descricao']) ?>
                                                </p>
                                            <?php endif; ?>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <a href="equipe.php?id=<?= $equipa['id_equipa'] ?>" class="btn btn-sm btn-outline-primary">
                                                    Ver detalhes
                                                </a>
                                                <small class="text-muted">
                                                    <i class="fas fa-users me-1"></i>
                                                    <?= $equipa['total_membros'] ?? 0 ?> membros
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
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

<!-- Estilos personalizados -->
<style>
    .stat-card {
        border-radius: 0.5rem;
        padding: 1.25rem;
        color: white;
        transition: transform 0.2s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .icon-box {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .btn-outline-primary {
        border-width: 2px;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
</style>

<!-- Scripts JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Garante que a página começa no topo
    window.scrollTo(0, 0);
    
    // Inicializa tooltips
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa todos os tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Adiciona animação de carregamento suave
        document.querySelectorAll('.stat-card, .card').forEach(function(card) {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        });
        
        // Anima os cards em sequência
        setTimeout(function() {
            document.querySelectorAll('.stat-card, .card').forEach(function(card, index) {
                setTimeout(function() {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 * index);
            });
        }, 300);
        
        // Atualiza a hora atual a cada minuto
        function updateCurrentTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            document.getElementById('current-time').textContent = now.toLocaleDateString('pt-PT', options);
        }
        
        // Atualiza a hora imediatamente e a cada minuto
        updateCurrentTime();
        setInterval(updateCurrentTime, 60000);
    });
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