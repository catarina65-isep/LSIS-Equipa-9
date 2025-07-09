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

// Mês atual
$mes_atual = date('n');
$meses = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

// Define o título da página
$page_title = "Aniversariantes - Coordenador - Tlantic";
$page_heading = "Aniversariantes";

// Inclui o template base
include_once __DIR__ . '/includes/base_template.php';
?>

<!-- Conteúdo da página -->
<div class="container-fluid">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent p-0 mb-3">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Aniversariantes</li>
        </ol>
    </nav>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Aniversariantes</h1>
        <div>
            <button type="button" class="btn btn-outline-secondary">
                <i class="fas fa-download me-1"></i> Exportar
            </button>
        </div>
    </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form class="row g-3">
                        <div class="col-md-6">
                            <label for="mes" class="form-label">Mês</label>
                            <select id="mes" class="form-select">
                                <?php foreach ($meses as $numero => $nome): ?>
                                    <option value="<?= $numero ?>" <?= $numero == $mes_atual ? 'selected' : '' ?>>
                                        <?= $nome ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="equipa" class="form-label">Equipa</label>
                            <select id="equipa" class="form-select">
                                <option selected>Todas as Equipas</option>
                                <option>Desenvolvimento</option>
                                <option>Design</option>
                                <option>Marketing</option>
                                <option>Vendas</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                            <button type="reset" class="btn btn-outline-secondary">Limpar</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Aniversariantes -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Aniversariantes de <?= $meses[$mes_atual] ?></h5>
                    
                    <div class="row">
                        <!-- Colaborador 1 -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="position-relative d-inline-block mb-3">
                                        <img src="https://via.placeholder.com/100" class="rounded-circle" alt="..." width="100" height="100">
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            15/07
                                        </span>
                                    </div>
                                    <h5 class="card-title mb-1">João Silva</h5>
                                    <p class="text-muted mb-2">Desenvolvedor Sénior</p>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="ver_colaborador.php?id=1" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-person"></i> Ver Perfil
                                        </a>
                                        <button class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-envelope"></i> Parabenizar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Colaborador 2 -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="position-relative d-inline-block mb-3">
                                        <img src="https://via.placeholder.com/100" class="rounded-circle" alt="..." width="100" height="100">
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            22/07
                                        </span>
                                    </div>
                                    <h5 class="card-title mb-1">Maria Santos</h5>
                                    <p class="text-muted mb-2">Designer UX/UI</p>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="ver_colaborador.php?id=2" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-person"></i> Ver Perfil
                                        </a>
                                        <button class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-envelope"></i> Parabenizar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Colaborador 3 -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="position-relative d-inline-block mb-3">
                                        <img src="https://via.placeholder.com/100" class="rounded-circle" alt="..." width="100" height="100">
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            28/07
                                        </span>
                                    </div>
                                    <h5 class="card-title mb-1">Carlos Oliveira</h5>
                                    <p class="text-muted mb-2">Analista de Sistemas</p>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="ver_colaborador.php?id=3" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-person"></i> Ver Perfil
                                        </a>
                                        <button class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-envelope"></i> Parabenizar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mensagem quando não houver aniversariantes -->
                    <div class="text-center py-5 d-none" id="nenhum-aniversariante">
                        <i class="bi bi-gift display-1 text-muted mb-3"></i>
                        <h4>Nenhum aniversariante neste mês</h4>
                        <p class="text-muted">Não há colaboradores fazendo aniversário no mês selecionado.</p>
                    </div>
                </div>
            </div>
            
            <!-- Próximos Aniversariantes -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Próximos Aniversariantes</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Colaborador</th>
                                    <th>Cargo</th>
                                    <th>Equipa</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>15/07/2025</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/40" class="rounded-circle me-2" width="32" height="32">
                                            João Silva
                                        </div>
                                    </td>
                                    <td>Desenvolvedor Sênior</td>
                                    <td>Desenvolvimento</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-envelope"></i> Parabenizar
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>22/07/2025</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/40" class="rounded-circle me-2" width="32" height="32">
                                            Maria Santos
                                        </div>
                                    </td>
                                    <td>Designer UX/UI</td>
                                    <td>Design</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-envelope"></i> Parabenizar
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>28/07/2025</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://via.placeholder.com/40" class="rounded-circle me-2" width="32" height="32">
                                            Carlos Oliveira
                                        </div>
                                    </td>
                                    <td>Analista de Sistemas</td>
                                    <td>Desenvolvimento</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-envelope"></i> Parabenizar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </div>
</div>

<?php
// Inclui o rodapé do template base
include_once __DIR__ . '/includes/base_footer.php';
?>
