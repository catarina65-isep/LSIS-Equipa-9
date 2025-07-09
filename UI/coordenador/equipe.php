<?php
// Inicia a sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclui a classe Database
require_once __DIR__ . '/../../DAL/config.php';

// Log temporário
file_put_contents('debug.log', "Iniciando equipe.php\n", FILE_APPEND);
file_put_contents('debug.log', "SESSION: " . print_r($_SESSION, true) . "\n", FILE_APPEND);

// Verifica se o usuário está logado e é um coordenador
if (!isset($_SESSION['utilizador_id'])) {
    file_put_contents('debug.log', "Erro: Usuário não logado\n", FILE_APPEND);
    header('Location: /LSIS-Equipa-9/UI/login.php');
    exit;
}

if ($_SESSION['id_perfilacesso'] != 3) {
    file_put_contents('debug.log', "Erro: Usuário não é coordenador. Perfil: " . $_SESSION['id_perfilacesso'] . "\n", FILE_APPEND);
    header('Location: /LSIS-Equipa-9/UI/login.php');
    exit;
}

// Inclui as classes necessárias
require_once __DIR__ . '/../../BLL/CoordenadorBLL.php';

// Inicializa a BLL do coordenador
$coordenadorBLL = new CoordenadorBLL();

// Habilita a exibição de erros para facilitar o debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Obtém o ID do coordenador da sessão
$idCoordenador = $_SESSION['utilizador_id'];
file_put_contents(__DIR__ . '/debug.log', "[DEBUG] ID do coordenador da sessão: " . $idCoordenador . "\n", FILE_APPEND);

// Obtém os dados do coordenador
file_put_contents(__DIR__ . '/debug.log', "[DEBUG] Obtendo dados do coordenador...\n", FILE_APPEND);
$dadosCoordenador = $coordenadorBLL->obterDadosCoordenador($idCoordenador);
file_put_contents(__DIR__ . '/debug.log', "[DEBUG] Dados do coordenador: " . print_r($dadosCoordenador, true) . "\n", FILE_APPEND);

// Verifica se o usuário tem perfil de coordenador
$sql = "SELECT id_perfilacesso FROM utilizador WHERE id_utilizador = :id_utilizador";
$pdo = Database::getInstance();
$stmt = $pdo->prepare($sql);
$stmt->execute([':id_utilizador' => $idCoordenador]);
$perfil = $stmt->fetch(PDO::FETCH_ASSOC);
file_put_contents(__DIR__ . '/debug.log', "[DEBUG] Perfil do usuário: " . print_r($perfil, true) . "\n", FILE_APPEND);

// Se não encontrar dados do coordenador, tenta criar um registro básico
if (!$dadosCoordenador) {
    file_put_contents('debug.log', "Tentando criar registro básico para o coordenador ID: " . $idCoordenador . "\n", FILE_APPEND);
    
    try {
        $pdo = Database::getInstance();
        
        // Primeiro, verifica se já existe um registro inativo para este usuário
        $sql = "SELECT id_coordenador FROM coordenador WHERE id_utilizador = :id_utilizador";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_utilizador' => $idCoordenador]);
        $coordenadorExistente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($coordenadorExistente) {
            // Se existir um registro inativo, ativa-o
            $sql = "UPDATE coordenador SET ativo = 1, data_inicio = CURRENT_DATE, data_fim = NULL, 
                    cargo = 'Coordenador', tipo_coordenacao = 'Equipe'
                    WHERE id_coordenador = :id_coordenador";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id_coordenador' => $coordenadorExistente['id_coordenador']]);
            file_put_contents('debug.log', "Registro de coordenador reativado com sucesso\n", FILE_APPEND);
        } else {
            // Se não existir, cria um novo registro
            $sql = "INSERT INTO coordenador (id_utilizador, cargo, tipo_coordenacao, ativo, data_inicio) 
                    VALUES (:id_utilizador, 'Coordenador', 'Equipe', 1, CURRENT_DATE)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id_utilizador' => $idCoordenador]);
            file_put_contents('debug.log', "Novo registro de coordenador criado com sucesso\n", FILE_APPEND);
        }
        
        // Obtém os dados atualizados do coordenador
        $dadosCoordenador = $coordenadorBLL->obterDadosCoordenador($idCoordenador);
        
        // Verifica se o usuário tem um registro na tabela colaborador
        $sql = "SELECT id_colaborador FROM colaborador WHERE id_utilizador = :id_utilizador";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_utilizador' => $idCoordenador]);
        $colaborador = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$colaborador) {
            // Se não existir, cria um registro básico na tabela colaborador
            $sql = "INSERT INTO colaborador (id_utilizador, nome_completo, data_registo, ativo) 
                    VALUES (:id_utilizador, (SELECT username FROM utilizador WHERE id_utilizador = :id_utilizador2), CURRENT_DATE, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id_utilizador' => $idCoordenador,
                ':id_utilizador2' => $idCoordenador
            ]);
            file_put_contents('debug.log', "Novo registro de colaborador criado para o coordenador\n", FILE_APPEND);
        }
        
        // Tenta obter os dados novamente após a criação
        $dadosCoordenador = $coordenadorBLL->obterDadosCoordenador($idCoordenador);
        file_put_contents('debug.log', "Dados do coordenador após criação: " . print_r($dadosCoordenador, true) . "\n", FILE_APPEND);
        
        if (!$dadosCoordenador) {
            throw new Exception("Não foi possível criar o perfil de coordenador.");
        }
        
        file_put_contents('debug.log', "Registro de coordenador criado com sucesso: " . print_r($dadosCoordenador, true) . "\n", FILE_APPEND);
    } catch (Exception $e) {
        file_put_contents('debug.log', "Erro ao criar registro de coordenador: " . $e->getMessage() . "\n", FILE_APPEND);
        $_SESSION['erro'] = "Você não tem permissão para acessar esta área. Entre em contato com o administrador do sistema.";
        header('Location: /LSIS-Equipa-9/UI/coordenador/index.php');
        exit;
    }
}

// Obtém as equipes gerenciadas pelo coordenador
$equipes = $coordenadorBLL->obterEquipesGerenciadas($dadosCoordenador['id_coordenador']);

// Verifica se o parâmetro de equipe foi passado
$idEquipaSelecionada = isset($_GET['equipa']) ? intval($_GET['equipa']) : null;
$membrosEquipe = [];
$equipeAtual = null;

// Se uma equipe específica foi selecionada, obtém seus membros
if ($idEquipaSelecionada) {
    try {
        $membrosEquipe = $coordenadorBLL->obterMembrosEquipe($idEquipaSelecionada, $dadosCoordenador['id_coordenador']);
        
        // Verifica se a equipe atual existe na lista de equipes do coordenador
        foreach ($equipes as $equipa) {
            if ($equipa['id_equipa'] == $idEquipaSelecionada) {
                $equipeAtual = $equipa;
                break;
            }
        }
        
        // Se a equipe não for encontrada, redireciona para a lista de equipes
        if (!$equipeAtual) {
            header('Location: equipe.php');
            exit;
        }
        
    } catch (Exception $e) {
        $_SESSION['erro'] = $e->getMessage();
        header('Location: equipe.php');
        exit;
    }
}

// Define o título da página
$page_title = "Minha Equipa" . ($equipeAtual ? ": " . htmlspecialchars($equipeAtual['nome']) : "") . " - Coordenador - Tlantic";
$page_heading = "Minha Equipa" . ($equipeAtual ? ": <small class='text-muted'>" . htmlspecialchars($equipeAtual['nome']) . "</small>" : "");

// Inclui o template base
include_once __DIR__ . '/includes/base_template.php';

// Mensagens de sucesso/erro
if (isset($_SESSION['sucesso'])) {
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            " . htmlspecialchars($_SESSION['sucesso']) . "
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
    unset($_SESSION['sucesso']);
}

if (isset($_SESSION['erro'])) {
    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
            " . htmlspecialchars($_SESSION['erro']) . "
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
    unset($_SESSION['erro']);
}
?>

<!-- Conteúdo da página -->
<div class="container-fluid">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent p-0 mb-3">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="equipe.php" class="text-decoration-none">Minha Equipe</a></li>
            <?php if ($equipeAtual): ?>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($equipeAtual['nome']) ?></li>
            <?php endif; ?>
        </ol>
    </nav>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><?php echo $page_heading; ?></h1>
        <div>
            <?php if ($equipeAtual): ?>
            <a href="equipe.php" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Voltar para todas as equipas
            </a>
            <?php endif; ?>
            
            <button type="button" class="btn btn-outline-secondary me-2" id="btnExportar" <?php echo empty($equipes) ? 'disabled' : ''; ?>>
                <i class="fas fa-download me-1"></i> Exportar
            </button>
            
            <?php if ($equipeAtual): ?>
            <a href="adicionar_colaborador.php?equipa=<?php echo $equipeAtual['id_equipa']; ?>" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Adicionar Colaborador
            </a>
            <?php endif; ?>
        </div>
    </div>

            <?php if (!$equipeAtual): ?>
    <!-- Lista de Equipes -->
    <div class="row">
        <?php if (empty($equipes)): ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> Você não está gerenciando nenhuma equipa no momento.
            </div>
        </div>
        <?php else: ?>
            <?php foreach ($equipes as $equipa): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($equipa['nome']); ?></h5>
                            <span class="badge bg-primary"><?php echo $equipa['total_membros'] ?? 0; ?> membros</span>
                        </div>
                        
                        <?php if (!empty($equipa['descricao'])): ?>
                        <p class="card-text text-muted small mb-3"><?php echo htmlspecialchars($equipa['descricao']); ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($equipa['nome_departamento'])): ?>
                        <p class="card-text small mb-2">
                            <i class="fas fa-building me-1 text-muted"></i>
                            <strong>Departamento:</strong> <?php echo htmlspecialchars($equipa['nome_departamento']); ?>
                        </p>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <a href="equipe.php?equipa=<?php echo $equipa['id_equipa']; ?>" class="btn btn-sm btn-outline-primary">
                                Ver Equipa
                            </a>
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Criada em <?php echo date('d/m/Y', strtotime($equipa['data_criacao'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <!-- Detalhes da Equipe e Lista de Membros -->
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Informações da Equipe</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($equipeAtual['descricao'])): ?>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($equipeAtual['descricao'])); ?></p>
                    <hr>
                    <?php endif; ?>
                    
                    <ul class="list-unstyled">
                        <?php if (!empty($equipeAtual['nome_departamento'])): ?>
                        <li class="mb-2">
                            <i class="fas fa-building me-2 text-muted"></i>
                            <strong>Departamento:</strong> <?php echo htmlspecialchars($equipeAtual['nome_departamento']); ?>
                        </li>
                        <?php endif; ?>
                        
                        <li class="mb-2">
                            <i class="fas fa-users me-2 text-muted"></i>
                            <strong>Total de Membros:</strong> <?php echo count($membrosEquipe); ?>
                        </li>
                        
                        <li class="mb-2">
                            <i class="fas fa-calendar-alt me-2 text-muted"></i>
                            <strong>Criada em:</strong> <?php echo date('d/m/Y', strtotime($equipeAtual['data_criacao'])); ?>
                        </li>
                        
                        <?php if (!empty($equipeAtual['id_equipa_pai'])): ?>
                        <li class="mb-2">
                            <i class="fas fa-sitemap me-2 text-muted"></i>
                            <strong>Equipe Pai:</strong> 
                            <a href="equipe.php?equipa=<?php echo $equipeAtual['id_equipa_pai']; ?>">
                                Ver equipe superior
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                    
                    <div class="mt-4">
                        <a href="editar_equipe.php?id=<?php echo $equipeAtual['id_equipa']; ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i> Editar Equipe
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Estatísticas Rápidas -->
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Estatísticas</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Em breve: Estatísticas detalhadas da equipe.
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form id="formFiltros" class="row g-3">
                        <div class="col-md-5">
                            <label for="busca" class="form-label">Buscar</label>
                            <input type="text" class="form-control" id="busca" name="busca" 
                                   placeholder="Nome, matrícula ou cargo" 
                                   value="<?php echo isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-select">
                                <option value="" <?php echo !isset($_GET['status']) ? 'selected' : ''; ?>>Todos os status</option>
                                <option value="Ativo" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Ativo') ? 'selected' : ''; ?>>Ativo</option>
                                <option value="Férias" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Férias') ? 'selected' : ''; ?>>Férias</option>
                                <option value="Afastado" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Afastado') ? 'selected' : ''; ?>>Afastado</option>
                                <option value="Desligado" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Desligado') ? 'selected' : ''; ?>>Desligado</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
    <?php endif; ?>

            <?php if ($equipeAtual): ?>
            <!-- Lista de Colaboradores -->
            <div class="card">
                <div class="card-body">
                    <?php if (empty($membrosEquipe)): ?>
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-users-slash fa-3x text-muted"></i>
                        </div>
                        <h5>Nenhum membro encontrado</h5>
                        <p class="text-muted">Esta equipe ainda não possui membros ou não há resultados para os filtros aplicados.</p>
                        <a href="adicionar_colaborador.php?equipa=<?php echo $equipeAtual['id_equipa']; ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Adicionar Colaborador
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Cargo</th>
                                    <th>E-mail</th>
                                    <th>Status</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($membrosEquipe as $membro): 
                                    // Determina a classe do badge com base no status
                                    $badgeClass = 'bg-success';
                                    if (isset($membro['estado'])) {
                                        switch (strtolower($membro['estado'])) {
                                            case 'férias':
                                                $badgeClass = 'bg-warning text-dark';
                                                break;
                                            case 'afastado':
                                                $badgeClass = 'bg-danger';
                                                break;
                                            case 'desligado':
                                                $badgeClass = 'bg-secondary';
                                                break;
                                        }
                                    }
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <?php if (!empty($membro['foto'])): ?>
                                                <img src="<?php echo htmlspecialchars($membro['foto']); ?>" class="rounded-circle" alt="<?php echo htmlspecialchars($membro['nome']); ?>" style="width: 36px; height: 36px; object-fit: cover;">
                                                <?php else: ?>
                                                <div class="avatar-sm bg-soft-primary rounded-circle d-flex align-items-center justify-content-center">
                                                    <span class="text-primary fw-bold">
                                                        <?php echo strtoupper(substr($membro['nome'], 0, 1)); ?>
                                                    </span>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold">
                                                    <a href="ver_colaborador.php?id=<?php echo $membro['id_colaborador']; ?>" class="text-decoration-none">
                                                        <?php echo htmlspecialchars($membro['nome'] . (!empty($membro['apelido']) ? ' ' . $membro['apelido'] : '')); ?>
                                                    </a>
                                                </div>
                                                <?php if (!empty($membro['numero_mecanografico'])): ?>
                                                <div class="text-muted small"><?php echo htmlspecialchars($membro['numero_mecanografico']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($membro['titulo'])): ?>
                                            <?php echo htmlspecialchars($membro['titulo']); ?>
                                        <?php else: ?>
                                            <span class="text-muted">Não definido</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($membro['email'])): ?>
                                            <a href="mailto:<?php echo htmlspecialchars($membro['email']); ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($membro['email']); ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($membro['estado'])): ?>
                                            <span class="badge <?php echo $badgeClass; ?>">
                                                <?php echo htmlspecialchars($membro['estado']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Não definido</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="ver_colaborador.php?id=<?php echo $membro['id_colaborador']; ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Visualizar"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="editar_colaborador.php?id=<?php echo $membro['id_colaborador']; ?>" 
                                               class="btn btn-sm btn-outline-secondary" 
                                               title="Editar"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger btn-remover-membro" 
                                                    title="Remover da equipe"
                                                    data-bs-toggle="tooltip"
                                                    data-id="<?php echo $membro['id_colaborador']; ?>"
                                                    data-nome="<?php echo htmlspecialchars($membro['nome'] . (!empty($membro['apelido']) ? ' ' . $membro['apelido'] : '')); ?>">
                                                <i class="fas fa-user-minus"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginação -->
                    <?php if ($totalPaginas > 1): ?>
                    <nav aria-label="Navegação de páginas" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $paginaAtual <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?equipa=<?php echo $idEquipaSelecionada; ?>&pagina=<?php echo $paginaAtual - 1; ?>" <?php echo $paginaAtual <= 1 ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                            
                            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                <li class="page-item <?php echo $i == $paginaAtual ? 'active' : ''; ?>">
                                    <a class="page-link" href="?equipa=<?php echo $idEquipaSelecionada; ?>&pagina=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <li class="page-item <?php echo $paginaAtual >= $totalPaginas ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?equipa=<?php echo $idEquipaSelecionada; ?>&pagina=<?php echo $paginaAtual + 1; ?>" <?php echo $paginaAtual >= $totalPaginas ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
</div>

<?php
// Inclui o rodapé do template base
include_once __DIR__ . '/includes/base_footer.php';
?>

<?php if ($equipeAtual && !empty($membrosEquipe)): ?>
<!-- Modal de confirmação de remoção -->
<div class="modal fade" id="modalRemoverMembro" tabindex="-1" aria-labelledby="modalRemoverMembroLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRemoverMembroLabel">Confirmar Remoção</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja remover <span id="nomeMembro" class="fw-bold"></span> da equipe?</p>
                <p class="small text-muted">O colaborador não será excluído do sistema, apenas removido desta equipe.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarRemocao">
                    <i class="fas fa-user-minus me-1"></i> Remover
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializa os tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Variáveis para controle da remoção
    var idMembroARemover = null;
    var modalRemoverMembro = new bootstrap.Modal(document.getElementById('modalRemoverMembro'));
    
    // Abre o modal de confirmação ao clicar no botão de remover
    $('.btn-remover-membro').on('click', function() {
        idMembroARemover = $(this).data('id');
        var nomeMembro = $(this).data('nome');
        
        $('#nomeMembro').text(nomeMembro);
        modalRemoverMembro.show();
    });
    
    // Confirma a remoção do membro
    $('#btnConfirmarRemocao').on('click', function() {
        if (!idMembroARemover) return;
        
        var $btn = $(this);
        var $btnOriginal = $btn.html();
        
        // Desabilita o botão e mostra o spinner
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Removendo...');
        
        // Faz a requisição para remover o membro
        $.ajax({
            url: '../acoes/remover_membro_equipe.php',
            type: 'POST',
            data: {
                id_colaborador: idMembroARemover,
                id_equipa: <?php echo $equipeAtual['id_equipa']; ?>
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Mostra mensagem de sucesso e recarrega a página
                    showToast('Sucesso!', response.message, 'success');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    // Mostra mensagem de erro
                    showToast('Erro!', response.message || 'Ocorreu um erro ao remover o membro.', 'error');
                    $btn.html($btnOriginal).prop('disabled', false);
                }
            },
            error: function() {
                showToast('Erro!', 'Não foi possível conectar ao servidor. Tente novamente mais tarde.', 'error');
                $btn.html($btnOriginal).prop('disabled', false);
            }
        });
        
        // Fecha o modal
        modalRemoverMembro.hide();
    });
    
    // Função para exibir notificações toast
    function showToast(title, message, type) {
        var toast = `
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}</strong><br>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
                </div>
            </div>
        `;
        
        // Adiciona o toast ao container
        var $toast = $(toast).appendTo('#toastContainer');
        var bsToast = new bootstrap.Toast($toast[0]);
        bsToast.show();
        
        // Remove o toast do DOM após ser escondido
        $toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
    
    // Configura o botão de exportar
    $('#btnExportar').on('click', function() {
        // Adiciona parâmetros de filtro à URL de exportação
        var filtros = $('#formFiltros').serialize();
        var url = `exportar_equipe.php?equipa=<?php echo $equipeAtual['id_equipa']; ?>&${filtros}`;
        
        // Redireciona para a URL de exportação
        window.location.href = url;
    });
});
</script>
<?php endif; ?>
