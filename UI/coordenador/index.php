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

// Inclui o cabeçalho
include_once __DIR__ . '/../includes/header.php';

// Adiciona o CSS personalizado
echo '<link rel="stylesheet" href="/LSIS-Equipa-9/UI/assets/css/style-coordenador.css">';

// Define o título da página
echo '<title>Dashboard - Tlantic</title>';

// Simulação de dados (substituir por consultas reais ao banco de dados)
$total_equipe = 15;
$aniversariantes_mes = 3;
$alertas_pendentes = 2;
$projetos_ativos = 5;

// Dados de exemplo para as tabelas
$atividades_recentes = [
    ['Reunião de equipe', 'Hoje, 10:00', 'Concluído'],
    ['Revisão de projeto', 'Ontem, 14:30', 'Em andamento'],
    ['Entrevista de candidato', '05/07/2025, 11:00', 'Concluído'],
    ['Apresentação para diretoria', '04/07/2025, 15:45', 'Pendente']
];

$aniversariantes = [
    ['Ana Silva', '10/07/2025', 'Desenvolvedor Sênior'],
    ['Carlos Mendes', '15/07/2025', 'Analista de Dados'],
    ['Mariana Costa', '20/07/2025', 'Designer UX/UI']
];
?>

<!-- Conteúdo principal -->
<main class="main-content">
    <!-- Cabeçalho da Página -->
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">Visão Geral</h1>
            <p class="page-subtitle">Bem-vindo(a) de volta, <?= htmlspecialchars($_SESSION['nome'] ?? 'Usuário') ?></p>
        </div>
        <div class="header-actions">
            <button class="btn btn-primary me-2">
                <i class='bx bx-plus me-1'></i> Nova Atividade
            </button>
            <button class="btn btn-outline-secondary">
                <i class='bx bx-download me-1'></i> Exportar
            </button>
        </div>
    </div>
        
    <!-- Cards de Estatísticas -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card bg-primary">
                <div class="stat-icon">
                    <i class='bx bxs-group'></i>
                    <div class="stat-icon">
                        <i class='bx bxs-group'></i>
                    </div>
                    <div class="stat-value"><?= $total_equipe ?></div>
                    <div class="stat-label">Membros na Equipe</div>
                    <a href="equipe.php" class="stretched-link"></a>
                </div>
            </div>
            
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card bg-success">
                    <div class="stat-icon">
                        <i class='bx bxs-cake'></i>
                    </div>
                    <div class="stat-value"><?= $aniversariantes_mes ?></div>
                    <div class="stat-label">Aniversariantes do Mês</div>
                    <a href="aniversariantes.php" class="stretched-link"></a>
                </div>
            </div>
            
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card bg-warning">
                    <div class="stat-icon">
                        <i class='bx bxs-bell-ring'></i>
                    </div>
                    <div class="stat-value"><?= $alertas_pendentes ?></div>
                    <div class="stat-label">Alertas Pendentes</div>
                    <a href="#alertas" class="stretched-link"></a>
                </div>
            </div>
            
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="stat-card bg-info">
                    <div class="stat-icon">
                        <i class='bx bxs-briefcase-alt-2'></i>
                    </div>
                    <div class="stat-value"><?= $projetos_ativos ?></div>
                    <div class="stat-label">Projetos Ativos</div>
                    <a href="#projetos" class="stretched-link"></a>
                </div>
            </div>
        </div>

    <!-- Cards de Estatísticas -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card bg-primary">
                <div class="stat-icon">
                    <i class='bx bxs-group'></i>
                </div>
                <div class="stat-value"><?= $total_equipe ?></div>
                <div class="stat-label">Membros na Equipe</div>
                <a href="equipe.php" class="stretched-link"></a>
            </div>
        </div>
        
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card bg-success">
                <div class="stat-icon">
                    <i class='bx bxs-gift'></i>
                </div>
                <div class="stat-value"><?= $aniversariantes_mes ?></div>
                <div class="stat-label">Aniversariantes do Mês</div>
                <a href="aniversariantes.php" class="stretched-link"></a>
            </div>
        </div>
        
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card bg-warning">
                <div class="stat-icon">
                    <i class='bx bxs-bell-ring'></i>
                </div>
                <div class="stat-value"><?= $alertas_pendentes ?></div>
                <div class="stat-label">Alertas Pendentes</div>
                <a href="alertas.php" class="stretched-link"></a>
            </div>
        </div>
        
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card bg-info">
                <div class="stat-icon">
                    <i class='bx bxs-briefcase-alt-2'></i>
                </div>
                <div class="stat-value"><?= $projetos_ativos ?></div>
                <div class="stat-label">Projetos Ativos</div>
                <a href="#" class="stretched-link"></a>
            </div>
        </div>
    </div>
    
    <!-- Seção de Atividades Recentes e Aniversariantes -->
    <div class="row g-4">
        <!-- Atividades Recentes -->
        <div class="col-12 col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Atividades Recentes</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Últimos 7 dias
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#">Hoje</a></li>
                            <li><a class="dropdown-item active" href="#">Últimos 7 dias</a></li>
                            <li><a class="dropdown-item" href="#">Últimos 30 dias</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Atividade</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($atividades_recentes as $atividade): ?>
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                <span class="avatar-initial rounded-circle bg-primary text-white">
                                                    <?= substr($atividade[0], 0, 1) ?>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?= $atividade[0] ?></h6>
                                                <small class="text-muted"><?= $atividade[1] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= $atividade[1] ?></td>
                                    <td>
                                        <?php
                                        $badge_class = 'bg-secondary';
                                        if ($atividade[2] === 'Concluído') $badge_class = 'bg-success';
                                        elseif ($atividade[2] === 'Em andamento') $badge_class = 'bg-primary';
                                        elseif ($atividade[2] === 'Pendente') $badge_class = 'bg-warning';
                                        ?>
                                        <span class="badge <?= $badge_class ?>"><?= $atividade[2] ?></span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <button class="btn btn-sm btn-icon btn-light">
                                            <i class='bx bx-dots-vertical-rounded'></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 text-end">
                        <a href="#" class="btn btn-sm btn-outline-primary">Ver todas as atividades</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Próximos Aniversariantes -->
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Próximos Aniversariantes</h5>
                    <a href="aniversariantes.php" class="btn btn-sm btn-outline-primary">Ver todos</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($aniversariantes as $aniversariante): ?>
                        <div class="list-group-item border-0 py-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-md me-3">
                                    <span class="avatar-initial rounded-circle bg-primary bg-opacity-10 text-primary fw-bold">
                                        <?= substr($aniversariante[0], 0, 1) ?>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?= $aniversariante[0] ?></h6>
                                    <small class="text-muted"><?= $aniversariante[2] ?></small>
                                </div>
                                <div class="text-end">
                                    <div class="text-primary fw-bold">
                                        <i class='bx bx-calendar-event me-1'></i>
                                        <?= $aniversariante[1] ?>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary mt-1">
                                        <i class='bx bx-envelope me-1'></i> Parabéns
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Seção de Alertas -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Alertas e Notificações</h5>
                    <span class="badge bg-danger"><?= $alertas_pendentes ?> Novos</span>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class='bx bx-error-circle me-2 fs-4'></i>
                        <div>
                            <strong>Atenção!</strong> Reunião de equipe agendada para amanhã às 10:00.
                            <div class="text-muted small mt-1">Hoje, 09:30</div>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    
                    <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class='bx bx-info-circle me-2 fs-4'></i>
                        <div>
                            <strong>Lembrete:</strong> Enviar relatório mensal até sexta-feira.
                            <div class="text-muted small mt-1">Ontem, 15:45</div>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    
                    <div class="alert alert-light alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class='bx bx-check-circle me-2 fs-4 text-success'></i>
                        <div>
                            <strong>Sucesso!</strong> Seu relatório foi enviado com sucesso.
                            <div class="text-muted small mt-1">05/07/2025, 14:20</div>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    
                    <div class="text-end mt-3">
                        <a href="#" class="btn btn-outline-primary btn-sm">Ver histórico de notificações</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Inclui o rodapé -->
<?php include_once __DIR__ . '/../includes/footer.php'; ?>

<!-- Scripts personalizados -->
<script>
// Inicialização de tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

// Inicialização de popovers
var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl)
});

// Menu mobile
function toggleSidebar() {
    document.body.classList.toggle('sidebar-toggled');
    document.querySelector('.sidebar').classList.toggle('toggled');
    
    if (document.querySelector('.sidebar .collapse')) {
        document.querySelector('.sidebar .collapse').classList.remove('show');
    }
}

// Fechar alertas automaticamente após 5 segundos
setTimeout(function() {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        var bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);

// Garante que o conteúdo principal ocupe a altura correta
function adjustMainContentHeight() {
    const header = document.querySelector('.header');
    const mainContent = document.querySelector('.main-content');
    if (header && mainContent) {
        const headerHeight = header.offsetHeight;
        mainContent.style.marginTop = `${headerHeight}px`;
    }
}

// Ajusta o layout quando a janela é redimensionada
window.addEventListener('resize', adjustMainContentHeight);

// Ajusta o layout quando a página é carregada
document.addEventListener('DOMContentLoaded', function() {
    adjustMainContentHeight();
    
    // Atualiza a altura do conteúdo principal após um pequeno atraso
    // para garantir que todos os elementos foram carregados
    setTimeout(adjustMainContentHeight, 100);
});
</script>
