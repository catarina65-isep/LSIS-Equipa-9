<?php
session_start();

// Verifica se o usuário está logado e tem perfil de RH
if (!isset($_SESSION['utilizador_id']) || $_SESSION['id_perfilacesso'] != 2) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../DAL/convidado.php';
require_once __DIR__ . '/../../DAL/utilidades.php';

$page_title = "Gerir Fichas de Convidados";

try {
    $convidado = new Convidado();
    $convidados = $convidado->listarTodos();
    
    // Separar os convidados por status
    $pendentes = array_filter($convidados, function($c) { return $c['ativo'] == 1; });
    $aceites = array_filter($convidados, function($c) { return $c['ativo'] == 2; });
    $rejeitados = array_filter($convidados, function($c) { return $c['ativo'] == 0; });
} catch (Exception $e) {
    $error = "Erro ao carregar lista de convidados: " . $e->getMessage();
}

// Verificar mensagens de erro/sucesso na sessão
$mensagem = isset($_SESSION['error']) ? $_SESSION['error'] : null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - Tlantic</title>
    
    <script>
        function confirmarAceite(id) {
            if (confirm('Tem certeza que deseja aceitar este candidato?')) {
                window.location.href = '../processar_acoes.php?action=aceitar&id=' + id;
            }
        }

        function confirmarRejeicao(id) {
            if (confirm('Tem certeza que deseja rejeitar este candidato?')) {
                window.location.href = '../processar_acoes.php?action=rejeitar&id=' + id;
            }
        }

        function confirmarExclusao(id) {
            if (confirm('Tem certeza que deseja excluir este candidato?')) {
                window.location.href = '../processar_acoes.php?action=excluir&id=' + id;
            }
        }
    </script>
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #eef2ff;
            --secondary: #6c757d;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #ef476f;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        body {
            background-color: #f5f7fb;
            color: #4a5568;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .main-content {
            margin-left: 250px;
            flex: 1;
            min-height: 100vh;
            transition: all 0.3s ease-in-out;
            background-color: #f5f7fb;
            position: relative;
        }
        
        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        
        .table {
            border: none;
        }
        
        .table th {
            background-color: var(--primary-light);
            color: var(--primary);
            font-weight: 600;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: #000; }
        .badge-danger { background-color: #dc3545; color: white; }
        
        .section-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--primary-light);
        }
        
        .section-header h2 {
            color: var(--primary);
            font-size: 1.5rem;
        }
        
        .section-header small {
            color: var(--secondary);
            font-size: 0.9rem;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container-fluid p-0">
        <!-- Sidebar -->
        <div class="sidebar">
            <?php include 'includes/sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 p-4 bg-white shadow-sm">
                <div>
                    <h1 class="h3 mb-1">Gerir Fichas de Convidados</h1>
                    <p class="mb-0 text-muted">Lista completa de convidados cadastrados no sistema</p>
                </div>
            </div>

            <!-- Mensagens -->
            <?php if ($mensagem): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($mensagem) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Pendentes -->
            <div class="section-header">
                <h2>Pendentes</h2>
                <small class="text-muted"><?= count($pendentes) ?> candidatos aguardando análise</small>
            </div>
            <div class="card">
                <div class="card-body">
                    <?php if (count($pendentes) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Telefone</th>
                                        <th>Localidade</th>
                                        <th>Data de Candidatura</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendentes as $convidado): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($convidado['nome_completo']) ?></td>
                                            <td><?= htmlspecialchars($convidado['email']) ?></td>
                                            <td><?= htmlspecialchars($convidado['contacto_telefonico']) ?></td>
                                            <td><?= htmlspecialchars($convidado['localidade']) ?></td>
                                            <td><?= $convidado['data_inicio'] ? date('d/m/Y', strtotime($convidado['data_inicio'])) : '-' ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="#" class="btn btn-sm btn-success" onclick="confirmarAceite(<?= $convidado['id_convidado'] ?>)">
                                                        <i class='bx bx-check'></i> Aceitar
                                                    </a>
                                                    <a href="#" class="btn btn-sm btn-danger" onclick="confirmarRejeicao(<?= $convidado['id_convidado'] ?>)">
                                                        <i class='bx bx-x'></i> Rejeitar
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class='bx bx-check-circle text-success' style='font-size: 4rem;'></i>
                            <p class="mt-3 text-muted">Nenhum candidato pendente no momento</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Aceites -->
            <div class="section-header">
                <h2>Aceites</h2>
                <small class="text-muted"><?= count($aceites) ?> candidatos aceitos</small>
            </div>
            <div class="card">
                <div class="card-body">
                    <?php if (count($aceites) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Telefone</th>
                                        <th>Localidade</th>
                                        <th>Data de Candidatura</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($aceites as $convidado): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($convidado['nome_completo']) ?></td>
                                            <td><?= htmlspecialchars($convidado['email']) ?></td>
                                            <td><?= htmlspecialchars($convidado['contacto_telefonico']) ?></td>
                                            <td><?= htmlspecialchars($convidado['localidade']) ?></td>
                                            <td><?= $convidado['data_inicio'] ? date('d/m/Y', strtotime($convidado['data_inicio'])) : '-' ?></td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-danger" onclick="confirmarExclusao(<?= $convidado['id_convidado'] ?>)">
                                                    <i class='bx bx-trash'></i> Excluir
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class='bx bx-user-check text-success' style='font-size: 4rem;'></i>
                            <p class="mt-3 text-muted">Nenhum candidato aceito no momento</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Rejeitados -->
            <div class="section-header">
                <h2>Rejeitados</h2>
                <small class="text-muted"><?= count($rejeitados) ?> candidatos rejeitados</small>
            </div>
            <div class="card">
                <div class="card-body">
                    <?php if (count($rejeitados) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Telefone</th>
                                        <th>Localidade</th>
                                        <th>Data de Candidatura</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rejeitados as $convidado): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($convidado['nome_completo']) ?></td>
                                            <td><?= htmlspecialchars($convidado['email']) ?></td>
                                            <td><?= htmlspecialchars($convidado['contacto_telefonico']) ?></td>
                                            <td><?= htmlspecialchars($convidado['localidade']) ?></td>
                                            <td><?= $convidado['data_inicio'] ? date('d/m/Y', strtotime($convidado['data_inicio'])) : '-' ?></td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-danger" onclick="confirmarExclusao(<?= $convidado['id_convidado'] ?>)">
                                                    <i class='bx bx-trash'></i> Excluir
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class='bx bx-x-circle text-danger' style='font-size: 4rem;'></i>
                            <p class="mt-3 text-muted">Nenhum candidato rejeitado no momento</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            $('#convidadosTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
                },
                pageLength: 10,
                responsive: true
            });
        });
    </script>
</body>
</html>
