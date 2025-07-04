<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['utilizador_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../../BLL/EquipaBLL.php';
require_once __DIR__ . '/../../BLL/UtilizadorBLL.php';

$equipaBLL = new EquipaBLL();
$utilizadorBLL = new UtilizadorBLL();

// Obtém todas as equipas
$equipas = $equipaBLL->listarEquipas();

// Conta o total de equipas
$totalEquipas = count($equipas);

// Conta o total de membros de todas as equipas
$totalMembros = 0;
$membrosPorEquipa = [];

foreach ($equipas as $equipa) {
    $numMembros = $equipaBLL->contarMembrosEquipa($equipa['id']);
    $membrosPorEquipa[$equipa['id']] = $numMembros;
    $totalMembros += $numMembros;
}

// Obtém as 5 equipas mais recentes
$equipasRecentes = array_slice($equipas, 0, 5, true);

$page_title = "Dashboard de Equipas";
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - Tlantic</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
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
        
        .sidebar {
            width: 250px !important;
            background-color: #1a1a2e;
            color: #fff;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 1040;
            overflow-y: auto;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
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
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            color: #fff;
            margin-bottom: 20px;
        }
        
        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .stat-card .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .bg-primary-gradient {
            background: linear-gradient(45deg, #4361ee, #3f37c9);
        }
        
        .bg-success-gradient {
            background: linear-gradient(45deg, #28a745, #20c997);
        }
        
        .bg-warning-gradient {
            background: linear-gradient(45deg, #ffc107, #fd7e14);
        }
        
        .bg-danger-gradient {
            background: linear-gradient(45deg, #ef476f, #d0006f);
        }
        
        .recent-teams .team-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
        }
        
        .recent-teams .team-item:last-child {
            border-bottom: none;
        }
        
        .recent-teams .team-item:hover {
            background-color: #f8f9fa;
        }
        
        .recent-teams .team-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary);
            font-weight: 600;
        }
        
        .recent-teams .team-info {
            flex: 1;
        }
        
        .recent-teams .team-name {
            font-weight: 600;
            margin-bottom: 2px;
        }
        
        .recent-teams .team-members {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .recent-teams .team-actions {
            margin-left: 15px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
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
                    <h1 class="h3 mb-1">Dashboard de Equipas</h1>
                    <p class="mb-0 text-muted">Visão geral das equipas e membros</p>
                </div>
                <div>
                    <a href="equipas.php" class="btn btn-primary">
                        <i class='bx bx-group me-2'></i>Gerir Equipas
                    </a>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="container-fluid px-4">
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card bg-primary-gradient">
                            <i class='bx bx-group'></i>
                            <div class="stat-value"><?= $totalEquipas ?></div>
                            <div class="stat-label">Total de Equipas</div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card stat-card bg-success-gradient">
                            <i class='bx bx-user'></i>
                            <div class="stat-value"><?= $totalMembros ?></div>
                            <div class="stat-label">Total de Membros</div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card stat-card bg-warning-gradient">
                            <i class='bx bx-user-check'></i>
                            <div class="stat-value"><?= $totalEquipas > 0 ? round($totalMembros / $totalEquipas, 1) : 0 ?></div>
                            <div class="stat-label">Média por Equipa</div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card stat-card bg-danger-gradient">
                            <i class='bx bx-star'></i>
                            <div class="stat-value"><?= count(array_filter($equipas, function($e) { return !empty($e['coordenador_id']); })) ?></div>
                            <div class="stat-label">Coordenadores Ativos</div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Teams & Chart -->
                <div class="row g-4">
                    <!-- Recent Teams -->
                    <div class="col-lg-5">
                        <div class="card h-100">
                            <div class="card-header bg-white border-0">
                                <h5 class="mb-0">Equipas Recentes</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="recent-teams">
                                    <?php if (count($equipasRecentes) > 0): ?>
                                        <?php foreach ($equipasRecentes as $equipa): ?>
                                            <div class="team-item">
                                                <div class="team-avatar">
                                                    <?= strtoupper(substr($equipa['nome'], 0, 2)) ?>
                                                </div>
                                                <div class="team-info">
                                                    <div class="team-name"><?= htmlspecialchars($equipa['nome']) ?></div>
                                                    <div class="team-members">
                                                        <?= $membrosPorEquipa[$equipa['id']] ?? 0 ?> membro(s)
                                                    </div>
                                                </div>
                                                <div class="team-actions">
                                                    <a href="equipas.php?acao=editar&id=<?= $equipa['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class='bx bx-edit-alt'></i>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center p-4 text-muted">
                                            Nenhuma equipa encontrada.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 text-center">
                                <a href="equipas.php" class="btn btn-link">Ver todas as equipas</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Chart -->
                    <div class="col-lg-7">
                        <div class="card h-100">
                            <div class="card-header bg-white border-0">
                                <h5 class="mb-0">Distribuição de Membros</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="membersChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- All Teams -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Todas as Equipas</h5>
                                <div>
                                    <input type="text" id="searchTeam" class="form-control form-control-sm" placeholder="Pesquisar equipa...">
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="teamsTable">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>Descrição</th>
                                                <th>Membros</th>
                                                <th>Coordenador</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($equipas as $equipa): 
                                                $coordenador = !empty($equipa['coordenador_id']) ? $utilizadorBLL->obterPorId($equipa['coordenador_id']) : null;
                                            ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                                <i class='bx bx-group text-primary'></i>
                                                            </div>
                                                            <div>
                                                                <div class="fw-semibold"><?= htmlspecialchars($equipa['nome']) ?></div>
                                                                <small class="text-muted">Criada em: <?= date('d/m/Y', strtotime($equipa['data_criacao'])) ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?= !empty($equipa['descricao']) ? htmlspecialchars(substr($equipa['descricao'], 0, 50)) . (strlen($equipa['descricao']) > 50 ? '...' : '') : 'Nenhuma descrição' ?></td>
                                                    <td>
                                                        <span class="badge bg-primary rounded-pill">
                                                            <?= $membrosPorEquipa[$equipa['id']] ?? 0 ?> membro(s)
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($coordenador): ?>
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar-xs me-2">
                                                                    <span class="avatar-title bg-primary bg-opacity-10 text-primary rounded-circle">
                                                                        <?= strtoupper(substr($coordenador['nome'], 0, 1)) ?>
                                                                    </span>
                                                                </div>
                                                                <span><?= htmlspecialchars($coordenador['nome']) ?></span>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-muted">Não definido</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($equipa['ativo'])): ?>
                                                            <span class="badge bg-success">Ativo</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Inativo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-link text-muted p-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class='bx bx-dots-vertical-rounded font-size-18'></i>
                                                            </button>
                                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                <li>
                                                                    <a class="dropdown-item" href="equipas.php?acao=editar&id=<?= $equipa['id'] ?>">
                                                                        <i class='bx bx-edit-alt me-2'></i>Editar
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item" href="equipas.php?acao=visualizar&id=<?= $equipa['id'] ?>">
                                                                        <i class='bx bx-show me-2'></i>Visualizar
                                                                    </a>
                                                                </li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <a class="dropdown-item text-danger" href="#" onclick="confirmarExclusao(<?= $equipa['id'] ?>, '<?= htmlspecialchars(addslashes($equipa['nome'])) ?>')">
                                                                        <i class='bx bx-trash me-2'></i>Excluir
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Inicialização do DataTable
        $(document).ready(function() {
            const table = $('#teamsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-PT.json',
                    search: "",
                    searchPlaceholder: "Pesquisar equipa..."
                },
                order: [[0, 'asc']],
                pageLength: 10,
                responsive: true,
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
            });
            
            // Pesquisa personalizada
            $('#searchTeam').on('keyup', function() {
                table.search(this.value).draw();
            });
            
            // Inicialização do gráfico
            const ctx = document.getElementById('membersChart').getContext('2d');
            
            // Dados para o gráfico (exemplo)
            const teamNames = <?= json_encode(array_map(function($e) { return $e['nome']; }, $equipas)) ?>;
            const teamMembers = <?= json_encode(array_values($membrosPorEquipa)) ?>;
            
            // Cores para o gráfico
            const backgroundColors = [
                'rgba(67, 97, 238, 0.7)',
                'rgba(40, 167, 69, 0.7)',
                'rgba(255, 193, 7, 0.7)',
                'rgba(220, 53, 69, 0.7)',
                'rgba(23, 162, 184, 0.7)',
                'rgba(111, 66, 193, 0.7)',
                'rgba(253, 126, 20, 0.7)'
            ];
            
            const borderColors = [
                'rgba(67, 97, 238, 1)',
                'rgba(40, 167, 69, 1)',
                'rgba(255, 193, 7, 1)',
                'rgba(220, 53, 69, 1)',
                'rgba(23, 162, 184, 1)',
                'rgba(111, 66, 193, 1)',
                'rgba(253, 126, 20, 1)'
            ];
            
            // Criar o gráfico
            const membersChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: teamNames,
                    datasets: [{
                        label: 'Número de Membros',
                        data: teamMembers,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.parsed.y} membro(s)`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0
                            }
                        },
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });
            
            // Função para confirmar exclusão
            window.confirmarExclusao = function(id, nome) {
                if (confirm(`Tem certeza que deseja excluir a equipa "${nome}"? Esta ação não pode ser desfeita.`)) {
                    window.location.href = `equipas.php?acao=excluir&id=${id}`;
                }
            };
            
            // Atualizar o gráfico quando a janela for redimensionada
            window.addEventListener('resize', function() {
                membersChart.resize();
            });
        });
    </script>
</body>
</html>
