<?php
// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o utilizador está autenticado e é um coordenador
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

// Obter ID do usuário logado
$idUtilizador = $_SESSION['utilizador_id'];

// Obter dados do coordenador
$coordenador = $coordenadorBLL->obterDadosCoordenador($idUtilizador);

if (!$coordenador) {
    die("Usuário não é um coordenador ativo.");
}

// Obter equipes gerenciadas pelo coordenador usando o ID do coordenador
$equipes = $coordenadorBLL->obterEquipesGerenciadas($coordenador['id_coordenador']);
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
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Bem-vindo(a), <?= htmlspecialchars($_SESSION['nome'] ?? 'Coordenador') ?></h1>
            <p class="text-muted mb-0">
                <i class="far fa-calendar-alt me-1"></i>
                <?= date('d/m/Y - H:i') ?>
            </p>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                <i class="fas fa-sync-alt me-1"></i> Atualizar
            </button>
        </div>
    </div>

    <!-- Cartões de Estatísticas -->
    <div class="row g-3 mb-4">
        <!-- Total de Equipes -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="fas fa-users text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase text-muted small mb-1">Equipas</h6>
                            <h3 class="mb-0"><?= $equipes_coordenadas ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total de Membros -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="fas fa-user-friends text-success"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase text-muted small mb-1">Membros</h6>
                            <h3 class="mb-0"><?= $total_equipe ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Aniversariantes do Mês -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                            <i class="fas fa-birthday-cake text-warning"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase text-muted small mb-1">Aniversariantes</h6>
                            <h3 class="mb-0"><?= $aniversariantes_mes ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Alertas Pendentes -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                            <i class="fas fa-bell text-danger"></i>
                        </div>
                        <div>
                            <h6 class="text-uppercase text-muted small mb-1">Alertas</h6>
                            <h3 class="mb-0"><?= $alertas_pendentes ?></h3>
                        </div>
                    </div>
                </div>
            </div>
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

    <!-- Minhas Equipes -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">As Minhas Equipas</h5>
                    <a href="equipe.php" class="btn btn-sm btn-outline-primary">Ver Todas</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($equipes)): ?>
                        <div class="p-4">
                            <div class="alert alert-info mb-0">Não está a gerir nenhuma equipa no momento.</div>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nome da Equipa</th>
                                        <th>Membros</th>
                                        <th>Ações</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Limitar a exibição a 5 equipes no dashboard
                                    $equipes_limitadas = array_slice($equipes, 0, 5);
                                    foreach ($equipes_limitadas as $equipa): 
                                        $membros = $equipaBLL->obterMembrosEquipa($equipa['id_equipa']);
                                        $total_membros = count($membros);
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-2">
                                                        <div class="avatar-sm rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                            <?= strtoupper(substr($equipa['nome'], 0, 1)) ?>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0"><?= htmlspecialchars($equipa['nome']) ?></h6>
                                                        <?php if (!empty($equipa['descricao'])): ?>
                                                            <small class="text-muted"><?= htmlspecialchars(substr($equipa['descricao'], 0, 50)) . (strlen($equipa['descricao']) > 50 ? '...' : '') ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <small class="text-muted"><?= $total_membros ?> <?= $total_membros == 1 ? 'membro' : 'membros' ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $equipa['ativo'] ? 'success' : 'secondary' ?> bg-opacity-10 text-<?= $equipa['ativo'] ? 'success' : 'secondary' ?> px-2 py-1">
                                                    <?= $equipa['ativo'] ? 'Ativa' : 'Inativa' ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="equipe_detalhes.php?id=<?= $equipa['id_equipa'] ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i> Ver
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if (count($equipes) > 5): ?>
                            <div class="p-3 border-top text-center">
                                <a href="equipe.php" class="btn btn-link">
                                    Ver todas as <?= count($equipes) ?> equipas <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Aniversariantes do Mês -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">Aniversariantes do Mês</h5>
                    <a href="aniversariantes.php" class="btn btn-sm btn-outline-primary">Ver Todos</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($aniversariantes)): ?>
                        <div class="p-4">
                            <div class="text-center py-4">
                                <div class="mb-3">
                                    <i class="fas fa-birthday-cake fa-2x text-muted opacity-25"></i>
                                </div>
                                <h5 class="text-muted">Sem aniversariantes este mês</h5>
                                <p class="text-muted mb-0">Nenhum membro da sua equipa faz anos este mês.</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php 
                            // Limitar a exibição a 5 aniversariantes no dashboard
                            $aniversariantes_limitados = array_slice($aniversariantes, 0, 5);
                            foreach ($aniversariantes_limitados as $aniversariante): 
                                $data_nascimento = new DateTime($aniversariante['data_nascimento']);
                                $data_formatada = $data_nascimento->format('d/m');
                                
                                // Verificar se o aniversário é hoje
                                $hoje = new DateTime();
                                $aniversario_hoje = ($data_nascimento->format('m-d') === $hoje->format('m-d'));
                                
                                // Obter a idade
                                $idade = $hoje->diff($data_nascimento)->y;
                                
                                // Obter iniciais para o avatar
                                $nome_completo = $aniversariante['nome'];
                                $nomes = explode(' ', $nome_completo);
                                $iniciais = strtoupper(substr($nomes[0], 0, 1) . (isset($nomes[1]) ? substr($nomes[1], 0, 1) : ''));
                            ?>
                                <div class="list-group-item border-0 py-3">
                                    <div class="d-flex align-items-center">
                                        <!-- Avatar -->
                                        <div class="position-relative me-3">
                                            <div class="avatar-sm rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <?= $iniciais ?>
                                            </div>
                                            <?php if ($aniversario_hoje): ?>
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                                    Hoje
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Informações -->
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0"><?= htmlspecialchars($aniversariante['nome']) ?></h6>
                                            <div class="text-muted small">
                                                <span class="me-2">
                                                    <i class="fas fa-birthday-cake text-warning me-1"></i>
                                                    <?= $data_formatada ?>
                                                    <?php if ($aniversario_hoje): ?>
                                                        <span class="badge bg-warning text-dark ms-1"><?= $idade ?> anos</span>
                                                    <?php endif; ?>
                                                </span>
                                                <?php if (!empty($aniversariante['cargo'])): ?>
                                                    <span class="mx-1">•</span>
                                                    <span><?= htmlspecialchars($aniversariante['cargo']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Ações -->
                                        <div class="ms-2">
                                            <?php if (isset($aniversariante['email'])): ?>
                                                <a href="mailto:<?= htmlspecialchars($aniversariante['email']) ?>" class="btn btn-sm btn-outline-primary" title="Enviar e-mail">
                                                    <i class="fas fa-envelope"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (count($aniversariantes) > 5): ?>
                            <div class="p-3 border-top text-center">
                                <a href="aniversariantes.php" class="btn btn-link">
                                    Ver todos os <?= count($aniversariantes) ?> aniversariantes <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

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
        
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }
        
        .sidebar-overlay.show {
            display: block;
        }
        
        @media (min-width: 992px) {
            .sidebar-overlay {
                display: none !important;
            }
        }
    </style>

<?php if (isset($aniversariantes) && count($aniversariantes) > 5): ?>
    <div class="p-3 border-top text-center">
        <a href="aniversariantes.php" class="btn btn-link">
            Ver todos os <?= count($aniversariantes) ?> aniversariantes <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
<?php endif; ?>

</div>
</div>
</div>

<!-- Scripts JavaScript -->
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
        document.querySelectorAll('.card').forEach(function(card, index) {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            
            // Anima os cards em sequência
            setTimeout(function() {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 * index);
        });
        
        // Toggle da barra lateral em telas pequenas
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        const sidebarOverlay = document.createElement('div');
        sidebarOverlay.className = 'sidebar-overlay';
        
        if (sidebarToggle && sidebar && mainContent) {
            // Adiciona o overlay ao body
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
            sidebarToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleSidebar();
            });
            
            // Fechar a barra lateral ao clicar no overlay
            sidebarOverlay.addEventListener('click', toggleSidebar);
            
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
                    if (sidebarOverlay) {
                        sidebarOverlay.classList.remove('show');
                    }
                    document.body.style.overflow = '';
                }
            }
            
            // Adicionar event listener para redimensionamento
            window.addEventListener('resize', handleResize);
            
            // Verificar o estado salvo
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            }
            
            // Inicializa o estado do menu
            handleResize();
        }
    });
</script>

</body>
</html>