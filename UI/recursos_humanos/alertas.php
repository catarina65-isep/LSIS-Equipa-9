<?php
// Página de gestão de alertas e configuração de periodicidade
session_start();
require_once __DIR__ . '/../../DAL/database.php';
require_once __DIR__ . '/../../BLL/AlertManager.php';

if (!isset($_SESSION['utilizador_id']) || !in_array($_SESSION['id_perfilacesso'], [1, 2])) {
    header('Location: /LSIS-Equipa-9/UI/login.php');
    exit;
}

$alertManager = new AlertManager();
$periodicidade = $alertManager->getUpdatePeriod();

// Inicializar variáveis
$msg = '';
$error = '';

// Processar formulários
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Atualizar periodicidade
    if (isset($_POST['periodicidade'])) {
        $periodicidade = max(1, (int)$_POST['periodicidade']);
        $updated = $alertManager->updateAllUsersReminderPeriod($periodicidade);
        $msg = "Periodicidade atualizada para $periodicidade mês(es) para $updated usuário(s)!";
    }
    
    // Enviar lembretes manualmente
    if (isset($_POST['enviar_agora'])) {
        $sentCount = $alertManager->sendReminderEmails();
        $msg = "$sentCount lembrete(s) enviado(s) com sucesso!";
    }
    
    // Enviar alerta
    if (isset($_POST['enviar_alerta'])) {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $mensagem = isset($_POST['mensagem']) ? htmlspecialchars($_POST['mensagem'], ENT_QUOTES, 'UTF-8') : '';
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Por favor, insira um endereço de e-mail válido.';
        } else {
            require_once __DIR__ . '/../../DAL/PHPMailerConfig.php';
            
            if (function_exists('sendEmail')) {
                $assunto = "Alerta Importante - Recursos Humanos";
                $email_body = "
                <html>
                <head>
                    <title>Alerta Importante</title>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .footer { 
                            margin-top: 30px; 
                            font-size: 12px; 
                            color: #777; 
                            border-top: 1px solid #eee; 
                            padding-top: 10px;
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <h2>Alerta Importante</h2>
                        <p>" . nl2br(htmlspecialchars($mensagem)) . "</p>
                        <div class='footer'>
                            <p>Este é um e-mail automático, por favor não responda.</p>
                            <p>Atenciosamente,<br>Equipa de Recursos Humanos<br>Tlantic</p>
                        </div>
                    </div>
                </body>
                </html>";
                
                if (@sendEmail($email, $assunto, $email_body)) {
                    $msg = 'Alerta enviado com sucesso para ' . htmlspecialchars($email);
                } else {
                    $error = 'Ocorreu um erro ao enviar o alerta. Por favor, tente novamente.';
                }
            } else {
                $error = 'Erro de configuração do sistema de e-mail.';
            }
        }
    }
}

// Define o título da página
$page_title = 'Gestão de Alertas - Tlantic';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons -->
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/LSIS-Equipa-9/UI/assets/css/style.css" rel="stylesheet">
    
    <style>
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
            }
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 15px 20px;
            border-top-left-radius: 10px !important;
            border-top-right-radius: 10px !important;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 mb-0">Gestão de Alertas</h1>
            <div class="user-actions">
                <a href="/LSIS-Equipa-9/UI/logout.php" class="btn btn-outline-secondary btn-sm">
                    <i class='bx bx-log-out'></i> Sair
                </a>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Configurações de Alertas</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($msg): ?>
                                <div class="alert alert-success">
                                    <i class='bx bx-check-circle me-2'></i>
                                    <?= htmlspecialchars($msg) ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger">
                                    <i class='bx bx-error-circle me-2'></i>
                                    <?= htmlspecialchars($error) ?>
                                </div>
                            <?php endif; ?>
                            
                            <h6 class="mb-3">Enviar Alerta por E-mail</h6>
                            <form method="post" class="mb-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">E-mail do Destinatário</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="mensagem" class="form-label">Mensagem do Alerta</label>
                                        <textarea class="form-control" id="mensagem" name="mensagem" rows="4" required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" name="enviar_alerta" class="btn btn-primary">
                                            <i class='bx bx-send me-2'></i> Enviar Alerta
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            <hr class="my-4">
                            
                            <h6 class="mb-3">Lembretes Automáticos de Atualização</h6>
                            
                            <div class="alert alert-info mb-4">
                                <i class='bx bx-info-circle me-2'></i>
                                Configure com que frequência os usuários devem atualizar seus dados. Eles receberão um lembrete automático quando estiver na hora de atualizar.
                            </div>
                            
                            <form method="post" class="mb-4">
                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <label for="periodicidade" class="form-label">Periodicidade (meses)</label>
                                        <input type="number" class="form-control" id="periodicidade" name="periodicidade" 
                                               min="1" max="36" value="<?= $periodicidade ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" name="atualizar_periodicidade" class="btn btn-primary me-2">
                                            <i class='bx bx-save me-2'></i> Salvar Configuração
                                        </button>
                                        <button type="submit" name="enviar_agora" class="btn btn-outline-primary">
                                            <i class='bx bx-send me-2'></i> Enviar Agora
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Próximos Lembretes</h6>
                                </div>
                                <div class="card-body">
                                    <?php
                                    try {
                                        $stmt = $alertManager->getPdo()->query("
                                            SELECT 
                                                COALESCE(c.nome, u.username) as nome, 
                                                COALESCE(c.email, u.email) as email, 
                                                udu.next_reminder 
                                            FROM user_data_updates udu
                                            JOIN utilizador u ON u.id_utilizador = udu.user_id
                                            LEFT JOIN colaborador c ON u.id_utilizador = c.id_utilizador
                                            WHERE u.ativo = 1
                                            ORDER BY udu.next_reminder ASC
                                            LIMIT 10
                                        ")->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        if (count($stmt) > 0):
                                            echo '<div class="table-responsive">';
                                            echo '<table class="table table-hover">';
                                            echo '<thead><tr><th>Nome</th><th>E-mail</th><th>Próximo Lembrete</th></tr></thead>';
                                            echo '<tbody>';
                                            foreach ($stmt as $row) {
                                                echo '<tr>';
                                                echo '<td>' . htmlspecialchars($row['nome']) . '</td>';
                                                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                                                echo '<td>' . date('d/m/Y H:i', strtotime($row['next_reminder'])) . '</td>';
                                                echo '</tr>';
                                            }
                                            echo '</tbody></table>';
                                            echo '</div>';
                                        else:
                                            echo '<div class="text-muted">Nenhum lembrete agendado.</div>';
                                        endif;
                                    } catch (Exception $e) {
                                        echo '<div class="alert alert-warning">Erro ao carregar lembretes: ' . htmlspecialchars($e->getMessage()) . '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                            
                            <div class="alert alert-info mb-4">
                                <i class='bx bx-info-circle me-2'></i>
                                O sistema enviará um email de alerta aos colaboradores para atualização de dados a cada 
                                <strong><?= $periodicidade ?></strong> meses.
                            </div>
                            
                            <!-- Histórico de Alertas Enviados -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Histórico de Alertas Enviados</h6>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filtrarAlertasModal">
                                        <i class='bx bx-filter-alt me-1'></i> Filtrar
                                    </button>
                                </div>
                                <div class="card-body">
                                    <?php
                                    try {
                                        // Parâmetros de filtro
                                        $filtro_status = $_GET['status'] ?? 'todos';
                                        $filtro_data_inicio = $_GET['data_inicio'] ?? '';
                                        $filtro_data_fim = $_GET['data_fim'] ?? '';
                                        
                                        // Construir a consulta
                                        $query = "
                                            SELECT 
                                                COALESCE(c.nome, u.username) as nome, 
                                                COALESCE(c.email, u.email) as email, 
                                                la.assunto, 
                                                la.mensagem, 
                                                la.enviado_em,
                                                la.status
                                            FROM log_alertas la
                                            JOIN utilizador u ON la.enviado_por = u.id_utilizador
                                            LEFT JOIN colaborador c ON u.id_utilizador = c.id_utilizador
                                            WHERE 1=1
                                        ";
                                        
                                        $params = [];
                                        
                                        // Aplicar filtros
                                        if ($filtro_status !== 'todos') {
                                            $query .= " AND la.status = ?";
                                            $params[] = $filtro_status;
                                        }
                                        
                                        if (!empty($filtro_data_inicio)) {
                                            $query .= " AND DATE(la.enviado_em) >= ?";
                                            $params[] = $filtro_data_inicio;
                                        }
                                        
                                        if (!empty($filtro_data_fim)) {
                                            $query .= " AND DATE(la.enviado_em) <= ?";
                                            $params[] = $filtro_data_fim;
                                        }
                                        
                                        $query .= " ORDER BY la.enviado_em DESC LIMIT 50";
                                        
                                        $stmt = $alertManager->getPdo()->prepare($query);
                                        $stmt->execute($params);
                                        $alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        if (count($alertas) > 0):
                                    ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Data/Hora</th>
                                                    <th>Destinatário</th>
                                                    <th>Assunto</th>
                                                    <th>Status</th>
                                                    <th class="text-end">Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($alertas as $alerta): 
                                                    $statusClass = [
                                                        'enviado' => 'success',
                                                        'falha' => 'danger',
                                                        'pendente' => 'warning'
                                                    ][$alerta['status']] ?? 'secondary';
                                                ?>
                                                <tr>
                                                    <td><?= date('d/m/Y H:i', strtotime($alerta['enviado_em'])) ?></td>
                                                    <td>
                                                        <div class="fw-bold"><?= htmlspecialchars($alerta['nome']) ?></div>
                                                        <small class="text-muted"><?= htmlspecialchars($alerta['email']) ?></small>
                                                    </td>
                                                    <td><?= htmlspecialchars($alerta['assunto']) ?></td>
                                                    <td>
                                                        <span class="badge bg-<?= $statusClass ?>">
                                                            <?= ucfirst($alerta['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" data-bs-target="#detalhesAlertaModal"
                                                                data-assunto="<?= htmlspecialchars($alerta['assunto']) ?>"
                                                                data-mensagem="<?= htmlspecialchars($alerta['mensagem']) ?>">
                                                            <i class='bx bx-show'></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <i class='bx bx-inbox text-muted' style="font-size: 3rem;"></i>
                                            <p class="mt-2 text-muted">Nenhum alerta encontrado</p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php
                                    } catch (Exception $e) {
                                        echo '<div class="alert alert-danger">Erro ao carregar histórico: ' . 
                                             htmlspecialchars($e->getMessage()) . '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                            
                            <!-- Modal de Filtros -->
                            <div class="modal fade" id="filtrarAlertasModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="get" id="filtroForm">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Filtrar Alertas</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="todos" <?= ($_GET['status'] ?? '') === 'todos' ? 'selected' : '' ?>>Todos</option>
                                                        <option value="enviado" <?= ($_GET['status'] ?? '') === 'enviado' ? 'selected' : '' ?>>Enviados</option>
                                                        <option value="pendente" <?= ($_GET['status'] ?? '') === 'pendente' ? 'selected' : '' ?>>Pendentes</option>
                                                        <option value="falha" <?= ($_GET['status'] ?? '') === 'falha' ? 'selected' : '' ?>>Falhas</option>
                                                    </select>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Data Início</label>
                                                        <input type="date" name="data_inicio" class="form-control" 
                                                               value="<?= $_GET['data_inicio'] ?? '' ?>">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Data Fim</label>
                                                        <input type="date" name="data_fim" class="form-control" 
                                                               value="<?= $_GET['data_fim'] ?? '' ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Modal de Detalhes -->
                            <div class="modal fade" id="detalhesAlertaModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="detalhesAlertaModalLabel">Detalhes do Alerta</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h6 class="mb-3" id="detalhesAssunto"></h6>
                                            <div class="border rounded p-3 bg-light" id="detalhesMensagem"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <script>
                            // Inicializar o modal de detalhes
                            var detalhesAlertaModal = document.getElementById('detalhesAlertaModal');
                            if (detalhesAlertaModal) {
                                detalhesAlertaModal.addEventListener('show.bs.modal', function (event) {
                                    var button = event.relatedTarget;
                                    var assunto = button.getAttribute('data-assunto');
                                    var mensagem = button.getAttribute('data-mensagem');
                                    
                                    var modalTitle = detalhesAlertaModal.querySelector('.modal-title');
                                    var modalBodyAssunto = detalhesAlertaModal.querySelector('#detalhesAssunto');
                                    var modalBodyMensagem = detalhesAlertaModal.querySelector('#detalhesMensagem');
                                    
                                    modalTitle.textContent = assunto;
                                    modalBodyAssunto.textContent = assunto;
                                    modalBodyMensagem.innerHTML = mensagem.replace(/\n/g, '<br>');
                                });
                            }
                            
                            // Limpar filtros
                            document.querySelector('.btn-limpar-filtros')?.addEventListener('click', function() {
                                window.location.href = window.location.pathname;
                            });
                            </script>
                            
                           
                            
                            
                    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (sidebarToggle && sidebar && mainContent) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                });
            }
        });
    </script>
</body>
</html>
